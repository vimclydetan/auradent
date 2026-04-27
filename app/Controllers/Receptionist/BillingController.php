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
        $data = ['title' => 'Billing'];
        return view('receptionist/billing/index', $data);
    }

    /**
     * Fetch treatment history for a specific patient
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
     * Fetch Balances, UNBILLED Appointments, and UNBILLED Walk-ins
     */
   public function getActiveDetails($patient_id)
{
    $db = \Config\Database::connect();
    $billable_items = [];

    // 1. BALANCES (outstanding)
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
        AND tr.patient_id = ?
    ", [$patient_id, $patient_id])->getResultArray();

    foreach ($balances as $b) {
        $billable_items[] = [
            'type'           => 'balance',
            'id'             => $b['id'],
            'visit_id'       => $b['visit_id'],
            'appointment_id' => null,
            'service_id'     => $b['service_id'],
            'dentist_id'     => $b['dentist_id'],
            'display_text'   => $b['service_name'] . " - Balance: ₱" . number_format($b['balance'], 2),
            'dentist_name'   => 'Dr. ' . $b['first_name'] . ' ' . $b['last_name'],
            'amount_charge'  => $b['balance'],
            'tooth_number'   => $b['tooth_number']
        ];
    }

    // 2. UNBILLED COMPLETED APPOINTMENTS
    // ✅ FIX: lowercase 'completed', GROUP BY a.id para hindi mag-duplicate
    $appointments = $db->query("
        SELECT 
            a.id as app_id, a.dentist_id, a.appointment_date,
            d.first_name, d.last_name,
            aps.service_id, s.service_name,
            s.price, s.price_simple, s.price_moderate, s.price_severe, s.has_levels,
            aps.service_level,
            v.id as visit_id,
            GROUP_CONCAT(s.service_name SEPARATOR ', ') as all_services
        FROM appointments a
        JOIN dentists d ON d.id = a.dentist_id
        LEFT JOIN appointment_services aps ON aps.appointment_id = a.id
        LEFT JOIN services s ON s.id = aps.service_id
        LEFT JOIN visits v ON v.appointment_id = a.id
        LEFT JOIN treatment_records tr ON tr.visit_id = v.id
        WHERE a.patient_id = ?
          AND a.status = 'completed'
          AND tr.id IS NULL
        GROUP BY a.id
    ", [$patient_id])->getResultArray();

    foreach ($appointments as $app) {
        $price = (float)($app['price'] ?? 0);
        if (!empty($app['has_levels']) && $app['has_levels'] == 1) {
            $lvl = strtolower($app['service_level'] ?? '');
            if ($lvl === 'simple')   $price = (float)($app['price_simple'] ?? $price);
            elseif ($lvl === 'moderate') $price = (float)($app['price_moderate'] ?? $price);
            elseif ($lvl === 'severe')   $price = (float)($app['price_severe'] ?? $price);
        }

        $billable_items[] = [
            'type'           => 'appointment',
            'id'             => $app['app_id'],
            'visit_id'       => $app['visit_id'] ?? '',
            'appointment_id' => $app['app_id'],
            'service_id'     => $app['service_id'],
            'dentist_id'     => $app['dentist_id'],
            'display_text'   => ($app['all_services'] ?: 'Consultation') . " (Completed: " . date('M d', strtotime($app['appointment_date'])) . ")",
            'dentist_name'   => 'Dr. ' . $app['first_name'] . ' ' . $app['last_name'],
            'amount_charge'  => $price,
            'tooth_number'   => ''
        ];
    }

    // 3. UNBILLED WALK-INS
    $walkins = $db->query("
        SELECT v.id as visit_id, v.dentist_id, v.check_in_time, v.service_id,
               d.first_name, d.last_name,
               s.service_name, s.price
        FROM visits v
        LEFT JOIN dentists d ON d.id = v.dentist_id
        LEFT JOIN services s ON s.id = v.service_id
        LEFT JOIN treatment_records tr ON tr.visit_id = v.id
        WHERE v.patient_id = ?
          AND v.visit_type = 'Walk-in'
          AND tr.id IS NULL
    ", [$patient_id])->getResultArray();

    foreach ($walkins as $w) {
        $billable_items[] = [
            'type'           => 'walkin',
            'id'             => $w['visit_id'],
            'visit_id'       => $w['visit_id'],
            'appointment_id' => null,
            'service_id'     => $w['service_id'],
            'dentist_id'     => $w['dentist_id'],
            'display_text'   => ($w['service_name'] ?? 'Walk-in') . " - ₱" . number_format($w['price'] ?? 0, 2),
            'dentist_name'   => 'Dr. ' . $w['first_name'] . ' ' . $w['last_name'],
            'amount_charge'  => (float)($w['price'] ?? 0),
            'tooth_number'   => ''
        ];
    }

    if (empty($billable_items)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'No active payables found. Wait for the dentist to complete the appointment.'
        ]);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'items'  => $billable_items
    ]);
}

    /**
     * Save the transaction (Handles Balance, Appt, and Walk-in securely)
     */
    public function save()
    {
        try {
            $db = \Config\Database::connect();
            $treatmentModel = new \App\Models\TreatmentRecordModel();

            // KUNIN ANG INPUTS
            $patient_id = $this->request->getPost('patient_id') ?: null;
            $visit_id   = $this->request->getPost('visit_id') ?: null;
            $app_id     = $this->request->getPost('appointment_id') ?: null;
            $service_id = $this->request->getPost('service_id') ?: null;
            $dentist_id = $this->request->getPost('dentist_id') ?: null;
            $amount_paid = (float)$this->request->getPost('amount_paid');

            $max_allowable = 0;
            $is_paying_balance = false;

            // 1. CHECk KUNG NAGBABAYAD NG BALANCE (UTANG)
            if (!empty($visit_id) && !empty($service_id)) {
                $lastRecord = $db->table('treatment_records')
                    ->where('visit_id', $visit_id)
                    ->where('service_id', $service_id)
                    ->orderBy('id', 'DESC')
                    ->get()->getRow();

                if ($lastRecord) {
                    $max_allowable = (float)$lastRecord->balance;
                    $is_paying_balance = true;
                }
            }

            // 2. KUNG FIRST TIME PAYMENT
            if (!$is_paying_balance) {
                if (!empty($app_id)) {
                    $appService = $db->table('appointment_services aps')
                        ->select('s.price, s.price_simple, s.price_moderate, s.price_severe, s.has_levels, aps.service_level')
                        ->join('services s', 's.id = aps.service_id')
                        ->where('aps.appointment_id', $app_id)
                        ->get()->getRow();

                    if ($appService) {
                        $max_allowable = (float)$appService->price;
                        if ($appService->has_levels == 1) {
                            $lvl = strtolower($appService->service_level);
                            if ($lvl == 'simple') $max_allowable = (float)$appService->price_simple;
                            elseif ($lvl == 'moderate') $max_allowable = (float)$appService->price_moderate;
                            elseif ($lvl == 'severe') $max_allowable = (float)$appService->price_severe;
                        }
                    }
                } else if (!empty($service_id)) { // Kapag Walk-in
                    $walkinService = $db->table('services')->where('id', $service_id)->get()->getRow();
                    if ($walkinService) {
                        $max_allowable = (float)$walkinService->price;
                    }
                }
            }

            // Amount Validation
            if ($amount_paid <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Please enter a valid payment amount.']);
            }
            if ($amount_paid > $max_allowable) {
                return $this->response->setJSON(['status' => 'error', 'message' => "Overpayment! Max allowable payment is ₱" . number_format($max_allowable, 2)]);
            }

            // ================================================================
            // FIX: KUNG WALANG VISIT ID (Hindi nag-check-in), GAWAN NATIN AUTOMATIC
            // ================================================================
            if (empty($visit_id)) {
                $db->table('visits')->insert([
                    'patient_id'     => $patient_id,
                    'appointment_id' => $app_id ?: null,
                    'dentist_id'     => $dentist_id,
                    'service_id'     => $service_id,
                    'visit_type'     => !empty($app_id) ? 'Appointment' : 'Walk-in',
                    'check_in_time'  => date('Y-m-d H:i:s')
                ]);

                // Kunin ang na-generate na ID at ipasa sa $visit_id variable
                $visit_id = $db->insertID();
            }
            // ================================================================

            $new_balance = $max_allowable - $amount_paid;

            // PREPARE DATA PARA SA TREATMENT_RECORDS
            $data = [
                'visit_id'       => $visit_id, // Ngayon, sigurado tayong may laman na ito
                'patient_id'     => $patient_id,
                'dentist_id'     => $dentist_id,
                'service_id'     => $service_id,
                'tooth_number'   => $this->request->getPost('tooth_number') ?: null,
                'amount_charge'  => $max_allowable,
                'amount_paid'    => $amount_paid,
                'balance'        => $new_balance,
                'consent_given'  => $this->request->getPost('consent_given') ?? 0,
                'treatment_date' => date('Y-m-d H:i:s')
            ];

            // Process Signature Safely
            $sigData = $this->request->getPost('signature_data');
            if (!empty($sigData)) {
                $sigData = str_replace(['data:image/png;base64,', ' '], ['', '+'], $sigData);
                $imageName = 'sig_' . time() . '.png';
                $signature_path = 'uploads/signatures/' . $imageName;

                if (!is_dir(FCPATH . 'uploads/signatures/')) {
                    mkdir(FCPATH . 'uploads/signatures/', 0777, true);
                }

                file_put_contents(FCPATH . $signature_path, base64_decode($sigData));
                $data['signature_path'] = $signature_path;
            }

            // INSERT RECORD
            if ($treatmentModel->insert($data)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Transaction saved successfully!']);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Model Validation Failed: ' . json_encode($treatmentModel->errors())
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Backend Error: ' . $e->getMessage() . ' on line ' . $e->getLine()
            ]);
        }
    }
}
