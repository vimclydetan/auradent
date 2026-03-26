<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\TreatmentRecordModel;

class BillingController extends BaseController
{
    /**
     * Main Billing Page
     */
    public function index()
    {
        $db = \Config\Database::connect();

        // Get active services for manual selection if needed
        $data['services'] = $db->table('services')
            ->where('status', 'active')
            ->get()->getResultArray();

        // Get dentists list
        $data['dentists'] = $db->table('dentists')
            ->select('id, first_name, last_name')
            ->get()->getResultArray();

        return view('receptionist/billing/index', $data);
    }

    /**
     * Fetch treatment history for a specific patient (Compliance Table)
     */
    public function history($patient_id)
    {
        $db = \Config\Database::connect();
        $history = $db->table('treatment_records tr')
            ->select('tr.*, s.service_name, d.first_name as d_first, d.last_name as d_last')
            ->join('services s', 's.id = tr.service_id')
            ->join('dentists d', 'd.id = tr.dentist_id')
            ->where('tr.patient_id', $patient_id)
            ->orderBy('tr.treatment_date', 'DESC')
            ->get()->getResultArray();

        $data = [];
        foreach ($history as $h) {
            $data[] = [
                'treatment_date' => date('M d, Y', strtotime($h['treatment_date'])),
                'tooth_number'   => $h['tooth_number'],
                'service_name'   => $h['service_name'],
                'dentist_name'   => $h['d_first'] . ' ' . $h['d_last'],
                'consent_given'  => $h['consent_given'],
                'signature_path' => $h['signature_path'],
                'amount_charge'  => number_format($h['amount_charge'], 2),
                'amount_paid'    => number_format($h['amount_paid'], 2),
                'balance'        => number_format($h['balance'], 2)
            ];
        }
        return $this->response->setJSON($data);
    }

    /**
     * Logic for fetching both Outstanding Balances and Today's Appointments
     */
    public function getActiveDetails($patient_id)
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // 1. Get all unique unpaid treatment cycles
        // We use a subquery to get the LATEST record per visit/service to show current balance
        $balances = $db->query("
            SELECT tr.*, s.service_name, d.first_name, d.last_name 
            FROM treatment_records tr
            JOIN services s ON s.id = tr.service_id
            JOIN dentists d ON d.id = tr.dentist_id
            WHERE tr.id IN (
                SELECT MAX(id) FROM treatment_records 
                WHERE patient_id = ? 
                GROUP BY visit_id, service_id
            )
            AND tr.balance > 0
        ", [$patient_id])->getResultArray();

        // 2. Get Appointment for today
        $appointment = $db->table('appointments a')
            ->select('a.id as app_id, a.dentist_id, d.first_name, d.last_name, aps.service_id, s.service_name, s.price, s.price_simple, s.price_moderate, s.price_severe, s.has_levels, aps.service_level')
            ->join('dentists d', 'd.id = a.dentist_id')
            ->join('appointment_services aps', 'aps.appointment_id = a.id', 'left')
            ->join('services s', 's.id = aps.service_id', 'left')
            ->where('a.patient_id', $patient_id)
            ->where('a.appointment_date', $today)
            ->where('a.status !=', 'Cancelled')
            ->where('a.status !=', 'Completed')
            ->get()->getRow();

        $billable_items = [];

        // Format balances into the selectable list
        foreach ($balances as $b) {
            $billable_items[] = [
                'type'           => 'balance',
                'id'             => $b['id'],
                'visit_id'       => $b['visit_id'],
                'service_id'     => $b['service_id'],
                'dentist_id'     => $b['dentist_id'],
                'display_text'   => "[OUTSTANDING] " . $b['service_name'] . " (Tooth: " . ($b['tooth_number'] ?: 'N/A') . ") - Date: " . date('M d', strtotime($b['treatment_date'])),
                'dentist_name'   => 'Dr. ' . $b['first_name'] . ' ' . $b['last_name'],
                'amount_charge'  => $b['balance'],
                'tooth_number'   => $b['tooth_number']
            ];
        }

        // Format new appointment into the selectable list
        if ($appointment) {
            $price = $appointment->price ?? 0;
            if (isset($appointment->has_levels) && $appointment->has_levels == 1) {
                $lvl = strtolower($appointment->service_level);
                if ($lvl == 'simple') $price = $appointment->price_simple;
                elseif ($lvl == 'moderate') $price = $appointment->price_moderate;
                elseif ($lvl == 'severe') $price = $appointment->price_severe;
            }

            $billable_items[] = [
                'type'           => 'appointment',
                'id'             => $appointment->app_id,
                'visit_id'       => null, // Will be created upon save
                'service_id'     => $appointment->service_id,
                'dentist_id'     => $appointment->dentist_id,
                'display_text'   => "[TODAY'S APPT] " . ($appointment->service_name ?: 'Consultation'),
                'dentist_name'   => 'Dr. ' . $appointment->first_name . ' ' . $appointment->last_name,
                'amount_charge'  => $price,
                'tooth_number'   => ''
            ];
        }

        if (empty($billable_items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No active balance or appointment found for this patient.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'items'  => $billable_items
        ]);
    }

    /**
     * Save the transaction
     */
    public function save()
    {
        $db = \Config\Database::connect();
        $treatmentModel = new TreatmentRecordModel();

        $patient_id = $this->request->getPost('patient_id');
        $visit_id   = $this->request->getPost('visit_id'); // If balance, this has value. If appt, this is null.
        $app_id     = $this->request->getPost('appointment_id');
        $service_id = $this->request->getPost('service_id');
        $dentist_id = $this->request->getPost('dentist_id');

        // --- SCENARIO: NEW APPOINTMENT ---
        // If visit_id is empty but we have an app_id, create a Visit first.
        if (empty($visit_id) && !empty($app_id)) {
            
            // Check for existing visit to prevent duplicates
            $existingVisit = $db->table('visits')->where('appointment_id', $app_id)->get()->getRow();

            if (!$existingVisit) {
                $db->table('visits')->insert([
                    'patient_id'     => $patient_id,
                    'appointment_id' => $app_id,
                    'dentist_id'     => $dentist_id,
                    'service_id'     => $service_id,
                    'visit_type'     => 'Appointment',
                    'check_in_time'  => date('Y-m-d H:i:s')
                ]);
                $visit_id = $db->insertID();
            } else {
                $visit_id = $existingVisit->id;
            }

            // Update appointment to Completed
            $db->table('appointments')->where('id', $app_id)->update(['status' => 'Completed']);
        }

        // --- FINAL CHECK ---
        if (empty($visit_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'System error: No linked visit found.']);
        }

        // Prepare Treatment Record
        $data = [
            'visit_id'       => $visit_id,
            'patient_id'     => $patient_id,
            'dentist_id'     => $dentist_id,
            'service_id'     => $service_id,
            'tooth_number'   => $this->request->getPost('tooth_number'),
            'amount_charge'  => $this->request->getPost('amount_charge'), // If balance payment, this is the old balance
            'amount_paid'    => $this->request->getPost('amount_paid'),
            'balance'        => $this->request->getPost('balance'),
            'consent_given'  => $this->request->getPost('consent_given') ?? 0,
            'treatment_date' => date('Y-m-d H:i:s')
        ];

        // Handle Signature Upload
        $sigData = $this->request->getPost('signature_data');
        if ($sigData) {
            $sigData = str_replace('data:image/png;base64,', '', $sigData);
            $sigData = str_replace(' ', '+', $sigData);
            $imageName = 'sig_' . time() . '.png';
            $signature_path = 'uploads/signatures/' . $imageName;
            
            // Ensure directory exists
            if (!is_dir(FCPATH . 'uploads/signatures/')) {
                mkdir(FCPATH . 'uploads/signatures/', 0777, true);
            }

            file_put_contents(FCPATH . $signature_path, base64_decode($sigData));
            $data['signature_path'] = $signature_path;
        }

        if ($treatmentModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Transaction saved successfully!']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: Failed to save record.']);
    }
}