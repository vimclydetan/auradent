<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\ServiceModel;
use App\Models\DentistModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AppointmentController extends BaseController
{
    private $timezone;

    public function __construct()
    {
        // Set global timezone for the controller
        $this->timezone = new \DateTimeZone('Asia/Manila');
        date_default_timezone_set('Asia/Manila');
    }

    /**
     * Display the main appointments list
     */
    public function index()
    {
        $apptModel = new AppointmentModel();
        $patientModel = new PatientModel();
        $serviceModel = new ServiceModel();
        $dentistModel = new DentistModel();

        $data = [
            'title'        => 'Appointments',
            'appointments' => $apptModel->getAppointments(),
            'services'     => $serviceModel->where('status', 'active')->findAll(),
            'dentists'     => $dentistModel->findAll()
        ];

        return view('admin/appointments/index', $data);
    }

    /**
     * AJAX Search for Patients
     */
    public function searchPatients()
    {
        $search = $this->request->getGet('q');
        $patientModel = new PatientModel();

        $patients = $patientModel->select('id, first_name, last_name, middle_name')
            ->groupStart()
                ->like('first_name', $search)
                ->orLike('last_name', $search)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        $result = [];
        foreach ($patients as $p) {
            $result[] = [
                'id'   => $p['id'],
                'text' => $p['last_name'] . ', ' . $p['first_name'] . ' ' . ($p['middle_name'] ?? '')
            ];
        }

        return $this->response->setJSON($result);
    }

    /**
     * Helper: Check if Dentist has an overlapping appointment
     * Formula: (StartA < EndB) AND (EndA > StartB)
     */
    private function isDentistBusy($dentistId, $startStr, $endStr, $excludeApptId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments');

        $builder->where('dentist_id', $dentistId);
        $builder->whereIn('status', ['Pending', 'Confirmed']);

        if ($excludeApptId) {
            $builder->where('id !=', $excludeApptId);
        }

        // Check for overlap between requested range and existing database ranges
        $builder->where("CONCAT(appointment_date, ' ', appointment_time) <", $endStr);
        $builder->where("CONCAT(end_date, ' ', end_time) >", $startStr);

        return $builder->countAllResults() > 0;
    }

    /**
     * Helper: Convert MM-DD-YYYY from input to YYYY-MM-DD for Database
     */
    private function formatToDbDate($dateStr)
    {
        if (empty($dateStr)) return null;
        try {
            // Check if format is MM-DD-YYYY or YYYY-MM-DD (fallback)
            $date = \DateTime::createFromFormat('m-d-Y', $dateStr) ?: new \DateTime($dateStr);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return $dateStr; // Return as is if parsing fails
        }
    }

    /**
     * Store a new appointment
     */
    public function store()
    {
        $db = \Config\Database::connect();

        $mobile = $this->request->getPost('primary_mobile');
        $cleanMobile = $mobile ? str_replace(' ', '', $mobile) : '';

        // Initial Validation
        $validationRules = [
            'account_type'     => 'required',
            'dentist_id'       => 'required|is_natural_no_zero',
            'appointment_date' => 'required',
            'appointment_time' => 'required',
            'end_date'         => 'required',
            'end_time'         => 'required',
            'services'         => 'required',
        ];

        $accountType = $this->request->getPost('account_type');
        if ($accountType === 'new') {
            $validationRules += [
                'first_name'       => 'required|min_length[2]',
                'last_name'        => 'required|min_length[2]',
                'username'         => 'required|is_unique[users.username]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[8]',
                'confirm_password' => 'required|matches[password]',
            ];
        } else {
            $validationRules['patient_id'] = 'required|is_natural_no_zero';
        }

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed. Please check all fields.');
        }

        // Convert Dates to DB Format
        $db_appt_date = $this->formatToDbDate($this->request->getPost('appointment_date'));
        $db_end_date  = $this->formatToDbDate($this->request->getPost('end_date'));
        
        $requestedStart = $db_appt_date . ' ' . $this->request->getPost('appointment_time');
        $requestedEnd   = $db_end_date . ' ' . $this->request->getPost('end_time');

        // Logic Time Validation
        $startObj = new \DateTime($requestedStart, $this->timezone);
        $endObj   = new \DateTime($requestedEnd, $this->timezone);
        $now      = new \DateTime('now', $this->timezone);

        if ($startObj < $now->modify('-1 minute')) {
            return redirect()->back()->withInput()->with('error', 'Cannot book an appointment in the past.');
        }
        if ($endObj <= $startObj) {
            return redirect()->back()->withInput()->with('error', 'End time must be after the start time.');
        }

        // CHECK DENTIST AVAILABILITY (The requested logic)
        if ($this->isDentistBusy($this->request->getPost('dentist_id'), $requestedStart, $requestedEnd)) {
            return redirect()->back()->withInput()->with('error', 'The selected Dentist is already booked for this time slot. Please choose another dentist or time.');
        }

        $db->transStart();
        try {
            $patientId = $this->request->getPost('patient_id');

            if ($accountType === 'new') {
                $userData = [
                    'username'  => $this->request->getPost('username'),
                    'email'     => $this->request->getPost('email'),
                    'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role'      => 'patient',
                    'is_active' => 1
                ];
                $db->table('users')->insert($userData);
                $userId = $db->insertID();

                $patientData = [
                    'user_id'        => $userId,
                    'first_name'     => $this->request->getPost('first_name'),
                    'middle_name'    => $this->request->getPost('middle_name'),
                    'last_name'      => $this->request->getPost('last_name'),
                    'name_suffix'    => $this->request->getPost('name_suffix'),
                    'birthdate'      => $this->formatToDbDate($this->request->getPost('birthdate')),
                    'gender'         => $this->request->getPost('gender'),
                    'primary_mobile' => $cleanMobile,
                    'region'         => $this->request->getPost('region'),
                    'province'       => $this->request->getPost('province'),
                    'city'           => $this->request->getPost('city'),
                    'barangay'       => $this->request->getPost('barangay'),
                ];
                $db->table('patients')->insert($patientData);
                $patientId = $db->insertID();
            }

            $appointmentData = [
                'patient_id'       => $patientId,
                'dentist_id'       => $this->request->getPost('dentist_id'),
                'appointment_date' => $db_appt_date,
                'appointment_time' => $this->request->getPost('appointment_time'),
                'end_date'         => $db_end_date,
                'end_time'         => $this->request->getPost('end_time'),
                'status'           => 'Pending'
            ];
            $db->table('appointments')->insert($appointmentData);
            $appointmentId = $db->insertID();

            // Insert Services
            $services = $this->request->getPost('services');
            $levels = $this->request->getPost('levels');
            foreach ($services as $idx => $sId) {
                if (!empty($sId)) {
                    $db->table('appointment_services')->insert([
                        'appointment_id' => $appointmentId,
                        'service_id'     => $sId,
                        'service_level'  => $levels[$idx] ?? 'Standard'
                    ]);
                }
            }

            $db->transComplete();
            return redirect()->to('/admin/appointments')->with('success', 'Appointment booked successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'System Error: ' . $e->getMessage());
        }
    }

    /**
     * Reschedule and Reassign Dentist
     */
    public function reschedule()
    {
        $id = $this->request->getPost('appointment_id');

        $validationRules = [
            'dentist_id'       => 'required|is_natural_no_zero',
            'appointment_date' => 'required',
            'appointment_time' => 'required',
            'end_date'         => 'required',
            'end_time'         => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->with('error', 'Please complete all fields correctly.');
        }

        $db_appt_date = $this->formatToDbDate($this->request->getPost('appointment_date'));
        $db_end_date  = $this->formatToDbDate($this->request->getPost('end_date'));
        
        $requestedStart = $db_appt_date . ' ' . $this->request->getPost('appointment_time');
        $requestedEnd   = $db_end_date . ' ' . $this->request->getPost('end_time');

        // Check Availability (Exclude itself)
        if ($this->isDentistBusy($this->request->getPost('dentist_id'), $requestedStart, $requestedEnd, $id)) {
            return redirect()->back()->with('error', 'The Dentist is busy during this new time slot.');
        }

        $model = new AppointmentModel();
        $data = [
            'dentist_id'       => $this->request->getPost('dentist_id'),
            'appointment_date' => $db_appt_date,
            'appointment_time' => $this->request->getPost('appointment_time'),
            'end_date'         => $db_end_date,
            'end_time'         => $this->request->getPost('end_time'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('/admin/appointments')->with('success', 'Appointment rescheduled successfully!');
        }
        return redirect()->back()->with('error', 'Update failed.');
    }

    /**
     * Update Status
     */
    public function updateStatus($id, $status)
    {
        $model = new AppointmentModel();
        if ($model->update($id, ['status' => $status])) {
            return redirect()->to('/admin/appointments')->with('success', 'Status updated to ' . $status);
        }
        return redirect()->back()->with('error', 'Update failed.');
    }

    /**
     * JSON Data for FullCalendar
     */
    public function getCalendarEvents()
    {
        $apptModel = new AppointmentModel();
        $appointments = $apptModel->select('appointments.*, patients.last_name as p_last, patients.first_name as p_first, dentists.last_name as d_last')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->whereIn('appointments.status', ['Pending', 'Confirmed'])
            ->findAll();

        $events = [];
        foreach ($appointments as $a) {
            $events[] = [
                'id'    => $a['id'],
                'title' => $a['p_first'] . ' ' . $a['p_last'] . " (Dr. " . ($a['d_last'] ?? 'None') . ")",
                'start' => $a['appointment_date'] . 'T' . $a['appointment_time'],
                'end'   => $a['end_date'] . 'T' . $a['end_time'],
                'backgroundColor' => ($a['status'] == 'Pending' ? '#f59e0b' : '#2563eb'),
                'extendedProps'   => ['status' => $a['status']]
            ];
        }
        return $this->response->setJSON($events);
    }
}