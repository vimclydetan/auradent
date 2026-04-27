<?php

namespace App\Controllers\Patient;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\ServiceModel;
use App\Models\DentistModel;
use App\Models\PatientModel;
use App\Constants\AppointmentStatus;
use App\Models\AppointmentCancelRequestModel;

class AppointmentController extends BaseController
{
    // Sa AppointmentController.php index() method:
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('login');

        $patientModel = new PatientModel();
        $patient = $patientModel->where('user_id', $userId)->first();
        if (!$patient) return redirect()->back()->with('error', 'Patient profile not found.');

        $db = \Config\Database::connect();
        $apptSvcModel = new \App\Models\AppointmentServiceModel();

        // ✅ Fetch appointments with the LATEST cancellation request info using subquery
        $builder = $db->table('appointments a');
        $builder->select('a.*, 
            (SELECT s.service_name FROM appointment_services ads JOIN services s ON s.id = ads.service_id WHERE ads.appointment_id = a.id LIMIT 1) as primary_service_name,
            d.first_name as d_first, d.last_name as d_last,
            acr.reason as last_request_reason,
            acr.status as last_request_status,
            acr.denial_reason as last_denial_reason,
            acr.action_at as last_denial_at');

        $builder->join('dentists d', 'd.id = a.dentist_id', 'left');

        // Join with the latest cancel request only
        $builder->join('(SELECT * FROM appointment_cancel_requests WHERE id IN (SELECT MAX(id) FROM appointment_cancel_requests GROUP BY appointment_id)) acr', 'acr.appointment_id = a.id', 'left');

        $builder->where('a.patient_id', $patient['id']);
        $builder->orderBy('a.appointment_date', 'DESC');
        $appointments = $builder->get()->getResultArray();

        $upcoming = [];
        $past = [];

        foreach ($appointments as $a) {
            $a['services'] = $apptSvcModel->getServicesByAppointment($a['id']);
            $a['service_name'] = $a['primary_service_name'] ?? 'Multiple Services';
            $a['dentist_name'] = $a['d_first'] ? 'Dr. ' . $a['d_first'] . ' ' . $a['d_last'] : 'TBD';
            $a['fmt_date'] = date('M d, Y', strtotime($a['appointment_date']));
            $a['fmt_time'] = date('h:i A', strtotime($a['appointment_time']));

            $status = strtolower($a['status']);

            // ✅ CATEGORIZATION
            // Active: Pending, Confirmed, at Cancellation Requested
            if (in_array($status, ['pending', 'confirmed', 'cancellation_requested'])) {
                $upcoming[] = $a;
            } else {
                // Past: Completed, Cancelled, Rejected, No-show
                $past[] = $a;
            }
        }

        // Sort Upcoming: Soonest first
        usort($upcoming, function ($a, $b) {
            return strtotime($a['appointment_date'] . ' ' . $a['appointment_time']) - strtotime($b['appointment_date'] . ' ' . $b['appointment_time']);
        });

        $data = [
            'title'           => 'My Appointments',
            'patient'         => $patient,
            'upcoming'        => $upcoming,
            'past'            => $past,
            'services'        => (new ServiceModel())->findAll(),
            'dentists'        => (new DentistModel())->findAll(),
            'has_active_appt' => !empty($upcoming),
        ];

        return view('patient/appointments/index', $data);
    }


    // Mag-book ng Appointment
    // Sa Patient\AppointmentController.php

    public function store()
    {
        $userId = session()->get('user_id');

        $patientModel = new PatientModel();
        $patient = $patientModel->where('user_id', $userId)->first();

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient profile not found.');
        }

        $db = \Config\Database::connect();

        // check active appointment
        $hasActive = $db->table('appointments')
            ->where('patient_id', $patient['id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date >=', date('Y-m-d'))
            ->countAllResults() > 0;

        if ($hasActive) {
            return redirect()->back()->withInput()
                ->with('error', 'You already have an active appointment.');
        }

        // normalize time
        $startTime = $this->request->getPost('appointment_time');
        if ($startTime && preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime .= ':00';
        }

        $endTime = $this->request->getPost('end_time');
        if ($endTime && preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            $endTime .= ':00';
        }

        // validation
        $rules = [
            'services'         => 'required',
            'appointment_date' => 'required|valid_date',
            'appointment_time' => 'required',
            'end_time'         => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('validation_errors', $this->validator->getErrors());
        }

        // prepare services FIRST (IMPORTANT FIX)
        $serviceIds = $this->request->getPost('services') ?? [];
        $levels = $this->request->getPost('levels') ?? [];

        $services = [];
        foreach ($serviceIds as $i => $id) {
            $services[] = [
                'service_id'    => (int)$id,
                'service_level' => $levels[$i] ?? 'Standard'
            ];
        }

        if (empty($services)) {
            return redirect()->back()->with('error', 'Please select services.');
        }

        $apptModel = new AppointmentModel();
        $apptSvcModel = new \App\Models\AppointmentServiceModel();

        $db->transStart();

        try {

            // 1. INSERT APPOINTMENT FIRST
            $apptModel->insert([
                'patient_id' => $patient['id'],
                'dentist_id' => $this->request->getPost('dentist_id') ?: null,
                'appointment_date' => $this->request->getPost('appointment_date'),
                'appointment_time' => $startTime,
                'end_date' => $this->request->getPost('appointment_date'),
                'end_time' => $endTime,
                'expected_duration_minutes' => $this->request->getPost('expected_duration_minutes') ?? 30,
                'status' => AppointmentStatus::PENDING,
                'booked_by' => 'patient',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $appointmentId = $apptModel->getInsertID();

            if (!$appointmentId) {
                throw new \Exception('Failed to create appointment');
            }

            // 2. INSERT SERVICES (NOW MAY APPOINTMENT ID NA)
            $apptSvcModel->insertServices($appointmentId, $services);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->back()->with('success', 'Appointment created successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Reuse slot conflict check (copy from Receptionist controller or make shared trait)
    protected function isSlotAvailable($model, ?int $dentistId, string $date, string $startTime, int $duration): bool
    {
        $startTimestamp = strtotime("$date $startTime");
        $endTimestamp = $startTimestamp + ($duration * 60);
        $endTime = date('H:i:s', $endTimestamp);

        $query = $model->db->table($model->table)
            ->where('appointment_date', $date)
            ->whereIn('status', [\App\Constants\AppointmentStatus::PENDING, \App\Constants\AppointmentStatus::CONFIRMED])
            ->groupStart()
            ->where('appointment_time <', $endTime)
            ->where('end_time >', $startTime)
            ->groupEnd();

        if ($dentistId) {
            $query->where('dentist_id', $dentistId);
        }

        return $query->countAllResults() === 0;
    }
    // Cancel Appointment
    public function status($id, $status)
    {
        $apptModel = new AppointmentModel();

        // Safety check: Pasyente ba ang may-ari nito?
        $userId = session()->get('user_id');
        $patientModel = new PatientModel();
        $patient = $patientModel->where('user_id', $userId)->first();

        $appt = $apptModel->where('id', $id)->where('patient_id', $patient['id'])->first();

        if (!$appt || $status !== 'Cancelled') {
            return redirect()->back()->with('error', 'Action not allowed.');
        }

        $apptModel->update($id, ['status' => 'Cancelled']);
        return redirect()->back()->with('success', 'Appointment cancelled.');
    }

    // Reschedule
    public function reschedule()
    {
        $apptModel = new AppointmentModel();
        $id = $this->request->getPost('appointment_id');

        $data = [
            'appointment_date' => $this->request->getPost('appointment_date'),
            'appointment_time' => $this->request->getPost('appointment_time'),
            'end_date'         => $this->request->getPost('appointment_date'),
            'end_time'         => $this->request->getPost('end_time'),
            'status'           => 'Pending'
        ];

        if ($apptModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Reschedule request sent.');
        }

        return redirect()->back()->with('error', 'Failed to reschedule.');
    }
    public function checkAvailability()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $dentistId = $this->request->getGet('dentist_id');
        $date = $this->request->getGet('date');

        // 🔧 FIX: Handle service_ids as string OR array
        $serviceIds = $this->request->getGet('service_ids');
        if (!is_array($serviceIds)) {
            $serviceIds = $serviceIds ? [(string)$serviceIds] : [];
        }

        // 🔧 FIX: Handle service_levels - it's a JSON string from JS
        $serviceLevelsRaw = $this->request->getGet('service_levels');
        $serviceLevels = is_string($serviceLevelsRaw)
            ? json_decode($serviceLevelsRaw, true) ?? []
            : ($serviceLevelsRaw ?? []);

        if (!$date || empty($serviceIds)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Missing parameters: date and service_ids required'
            ])->setStatusCode(400);
        }

        try {
            // Calculate required duration
            $requiredDuration = $this->calculatePatientDuration($serviceIds, $serviceLevels);

            // Get available slots
            $apptModel = new \App\Models\AppointmentModel();
            $slots = $apptModel->getAvailableSlots(
                $dentistId ?: null,
                $date,
                $requiredDuration,
                \App\Constants\TimeConfig::CLINIC_START ?? '09:00',
                \App\Constants\TimeConfig::CLINIC_END ?? '16:00'
            );

            return $this->response->setJSON([
                'success' => true,
                'date' => $date,
                'required_duration_minutes' => $requiredDuration,
                'slots' => $slots,
                'count' => count($slots)
            ]);
        } catch (\Exception $e) {
            // 🔍 Log full error for debugging
            log_message('error', '[PATIENT AVAILABILITY] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            return $this->response->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'error' => 'Failed to check availability: ' . $e->getMessage(),
                    'debug' => ENVIRONMENT === 'development' ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ] : null
                ]);
        }
    }
    public function view($id)
    {
        $userId = session()->get('user_id');
        $patientModel = new PatientModel();
        $patient = $patientModel->where('user_id', $userId)->first();

        if (!$patient) return redirect()->back()->with('error', 'Patient profile not found.');

        $db = \Config\Database::connect();
        $apptSvcModel = new \App\Models\AppointmentServiceModel();
        $acrModel = new \App\Models\AppointmentCancelRequestModel();

        // 1. Kunin ang Main Appointment Data
        $appointment = $db->table('appointments')
            ->select('appointments.*, d.first_name as d_first, d.last_name as d_last')
            ->join('dentists d', 'd.id = appointments.dentist_id', 'left')
            ->where('appointments.id', $id)
            ->where('appointments.patient_id', $patient['id'])
            ->get()
            ->getRowArray();

        if (!$appointment) return redirect()->to('patient/appointments')->with('error', 'Appointment not found.');

        // 2. Kunin lahat ng Services (Multi-service fix)
        // ✅ Ginagamit natin ang key na 'services_list' para tumugma sa View
        $appointment['services_list'] = $apptSvcModel->getServicesByAppointment($id);

        // Gawing string para sa Header display
        $names = array_column($appointment['services_list'], 'service_name');
        $appointment['service_name'] = !empty($names) ? implode(', ', $names) : 'No service assigned';

        // 3. Kunin ang Cancellation History (Audit Trail)
        // ✅ Ginagamit natin ang key na 'cancel_history'
        $appointment['cancel_history'] = $acrModel->where('appointment_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $appointment['dentist_name'] = $appointment['d_first'] ? 'Dr. ' . $appointment['d_first'] . ' ' . $appointment['d_last'] : 'To be assigned';

        $data = [
            'title' => 'Appointment Details',
            'appointment' => $appointment,
            'clinic_phone' => env('clinic.phone', 'N/A'),
            'clinic_email' => env('clinic.email', 'N/A'),
            'patient' => $patient
        ];

        return view('patient/appointments/view_appointment', $data);
    }

    public function checkActive()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        $patient = (new PatientModel())->where('user_id', $userId)->first();

        if (!$patient) {
            return $this->response->setJSON(['has_active' => false]);
        }

        $hasActive = \Config\Database::connect()
            ->table('appointments')
            ->where('patient_id', $patient['id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date >=', date('Y-m-d'))
            ->countAllResults() > 0;

        return $this->response->setJSON(['has_active' => $hasActive]);
    }
    // Sa calculatePatientDuration() method, ensure it handles array of services:
    protected function calculatePatientDuration(array $serviceIds, array $serviceLevels): int
    {
        if (empty($serviceIds)) return 30;

        $serviceModel = new ServiceModel();
        $services = $serviceModel
            ->select('id, estimated_duration_minutes, has_levels, duration_adjustments')
            ->whereIn('id', array_filter($serviceIds, 'is_numeric'))
            ->asArray()
            ->findAll();

        $total = array_reduce($services, function ($carry, $service) use ($serviceLevels) {
            $baseDuration = (int)($service['estimated_duration_minutes'] ?? 30);

            if (!empty($service['has_levels']) && !empty($serviceLevels[$service['id']])) {
                $adjustments = json_decode($service['duration_adjustments'] ?? '{}', true);
                if (is_array($adjustments)) {
                    $trimmed = array_combine(
                        array_map('trim', array_keys($adjustments)),
                        array_map('intval', $adjustments)
                    );
                    $level = $serviceLevels[$service['id']];
                    return $carry + $baseDuration + ($trimmed[$level] ?? 0);
                }
            }
            return $carry + $baseDuration;
        }, 0);

        return max((int)$total, 15);
    }

    /**
     * Submit cancellation request for pending or confirmed appointment
     */
    public function requestCancellation($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        $patient = (new PatientModel())->where('user_id', $userId)->first();
        if (!$patient) return $this->response->setStatusCode(403)->setJSON(['error' => 'Patient not found']);

        $apptModel = new AppointmentModel();
        $acrModel = new AppointmentCancelRequestModel();

        $appointment = $apptModel->where('id', $id)->where('patient_id', $patient['id'])->first();

        if (!$appointment) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Appointment not found']);
        }

        // Check current status
        $currentStatus = strtolower($appointment['status']);
        if ($currentStatus === 'cancellation_requested') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'You already have a pending cancellation request.']);
        }

        if (!in_array($currentStatus, ['pending', 'confirmed'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Only active appointments can be cancelled.']);
        }

        // ✅ ATTEMPT LIMIT CHECK
        $maxAttempts = 2;
        $attempts = (int) ($appointment['cancel_attempts'] ?? 0);

        if ($attempts >= $maxAttempts) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => "You have reached the maximum of $maxAttempts cancellation attempts. Please contact us via phone."
            ]);
        }

        $reason = trim($this->request->getPost('reason') ?? '');
        if (empty($reason)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Cancellation reason is required.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Create entry in the new Requests table
            $acrModel->insert([
                'appointment_id' => $id,
                'patient_id'     => $patient['id'],
                'reason'         => $reason,
                'status'         => 'pending',
                'created_at'     => date('Y-m-d H:i:s')
            ]);

            // 2. Update the main Appointment table status and attempts
            $apptModel->update($id, [
                'status'              => 'cancellation_requested',
                'cancel_attempts'     => $attempts + 1,
                'cancel_requested_at' => date('Y-m-d H:i:s') // Keep for quick ref
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) throw new \Exception('Database error.');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Your request has been sent. This is attempt ' . ($attempts + 1) . ' of ' . $maxAttempts . '.'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}
