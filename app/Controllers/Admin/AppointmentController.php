<?php

namespace App\Controllers\Admin;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\ServiceModel;
use App\Models\DentistModel;
use App\Models\MedicalConditionModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Controllers\BaseController;
use App\Services\AppointmentService;
use App\Constants\AppointmentStatus;

class AppointmentController extends BaseController
{
    private $timezone;

    protected AppointmentModel $apptModel;
    protected PatientModel $patientModel;
    protected ServiceModel $serviceModel;
    protected DentistModel $dentistModel;
    protected MedicalConditionModel $mcModel;
    protected $db;

    public function __construct()
    {

        $this->apptModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->serviceModel = new ServiceModel();
        $this->dentistModel = new DentistModel();
        $this->mcModel = new MedicalConditionModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display the main appointments list
     */
    public function index()
    {
        $tab = $this->request->getGet('tab') ?? 'today';

        $medicalConditions = $this->mcModel
            ->select('id, condition_key, condition_label, category, is_critical')
            ->where('is_active', 1)
            ->orderBy('category', 'ASC')
            ->asArray()
            ->findAll();

        return view('admin/appointments/index', [
            'title'        => 'Appointments',
            'currentTab'   => $tab,
            'counts'       => $this->apptModel->getCounts(),
            'appointments' => $this->apptModel->getFilteredAppointments($tab),
            'dentists'     => $this->getActiveDentists(),
            'services'     => $this->getActiveServices()
        ]);
    }

    private function getActiveDentists(): array
    {
        return $this->dentistModel
            ->select('id, first_name, last_name')
            ->where('status', 'Active')
            ->asArray()
            ->findAll();
    }
    /**
     * AJAX Search for Patients
     */
    public function searchPatients()
    {
        $search = $this->request->getGet('q');
        $patientModel = new \App\Models\PatientModel();
        $patients = $patientModel->select('id, first_name, last_name, middle_name')
            ->groupStart()
            ->like('first_name', $search)->orLike('last_name', $search)
            ->groupEnd()->limit(10)->findAll();

        $result = [];
        foreach ($patients as $p) {
            $result[] = ['id' => $p['id'], 'text' => $p['last_name'] . ', ' . $p['first_name']];
        }
        return $this->response->setJSON($result);
    }

    /**
     * Helper: Convert MM-DD-YYYY from input to YYYY-MM-DD for Database
     */
    private function formatToDbDate($dateStr)
    {
        if (empty($dateStr)) return null;
        try {
            $date = \DateTime::createFromFormat('m-d-Y', $dateStr) ?: new \DateTime($dateStr);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return $dateStr;
        }
    }

    private function isDentistBusy($dentistId, $start, $end, $excludeId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments');
        $builder->where('dentist_id', $dentistId);
        $builder->whereIn('status', ['Pending', 'Confirmed']);
        if ($excludeId) $builder->where('id !=', $excludeId);
        $builder->where("CONCAT(appointment_date, ' ', appointment_time) <", $end);
        $builder->where("CONCAT(end_date, ' ', end_time) >", $start);
        return $builder->countAllResults() > 0;
    }
    /**
     * Store a new appointment
     */
    public function store()
    {
        $db = \Config\Database::connect();
        $accountType = $this->request->getPost('account_type');

        // 1. VALIDATION RULES
        $validationRules = [
            'dentist_id'       => 'required',
            'appointment_date' => 'required',
            'appointment_time' => 'required',
            'end_date'         => 'required',
            'end_time'         => 'required',
            'services'         => 'required',
        ];

        if ($accountType === 'new') {
            $validationRules += [
                'first_name' => 'required',
                'last_name'  => 'required',
                'username'   => 'required|is_unique[users.username]',
                'email'      => 'required|valid_email|is_unique[users.email]',
                'password'   => 'required|min_length[8]',
                'region'     => 'required',
            ];
        } else {
            $validationRules['patient_id'] = 'required';
        }

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // 2. DATE & AVAILABILITY CHECK
        $db_appt_date = $this->formatToDbDate($this->request->getPost('appointment_date'));
        $db_end_date  = $this->formatToDbDate($this->request->getPost('end_date'));
        $reqStart     = $db_appt_date . ' ' . $this->request->getPost('appointment_time');
        $reqEnd       = $db_end_date . ' ' . $this->request->getPost('end_time');

        if ($this->isDentistBusy($this->request->getPost('dentist_id'), $reqStart, $reqEnd)) {
            return redirect()->back()->withInput()->with('error', 'The dentist is already booked for this schedule.');
        }

        // 3. START DATABASE TRANSACTION
        $db->transBegin();

        try {
            $patientId = $this->request->getPost('patient_id');

            // A. Create New Patient Account if needed
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
                    'primary_mobile' => str_replace(' ', '', $this->request->getPost('primary_mobile')),
                    'region'         => $this->request->getPost('region'),
                    'province'       => $this->request->getPost('province'),
                    'city'           => $this->request->getPost('city'),
                    'barangay'       => $this->request->getPost('barangay'),
                ];
                $db->table('patients')->insert($patientData);
                $patientId = $db->insertID();
            }

            // B. Create Appointment
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

            // C. Save Services
            $services = $this->request->getPost('services');
            $levels   = $this->request->getPost('levels');
            foreach ($services as $idx => $sId) {
                if (!empty($sId)) {
                    $db->table('appointment_services')->insert([
                        'appointment_id' => $appointmentId,
                        'service_id'     => $sId,
                        'service_level'  => $levels[$idx] ?? 'Standard'
                    ]);
                }
            }

            // D. Save Medical History (Step 2 Data)
            $medConditions = $this->request->getPost('medical_conditions') ?? [];

            // Kunin ang text mula sa "Others" field
            $otherAllergies = $this->request->getPost('other_allergies');

            // Kung may laman ang "Others", isama natin ito sa array
            if (!empty($otherAllergies)) {
                // Lalabas ito sa listahan bilang "Other: [yung tinype ng user]"
                $medConditions[] = 'Other Allergy: ' . $otherAllergies;
            }
            $medicalRecord = [
                'patient_id'                     => $patientId,
                'physician_name'                 => $this->request->getPost('physician_name'),
                'physician_specialty'            => $this->request->getPost('physician_specialty'),
                'physician_address'              => $this->request->getPost('physician_address'),
                'physician_phone'                => $this->request->getPost('physician_phone'),
                'is_good_health'                 => $this->request->getPost('is_good_health'),
                'is_under_medical_treatment'     => $this->request->getPost('is_under_medical_treatment'),
                'has_serious_illness'            => $this->request->getPost('has_serious_illness'),
                'serious_illness_details'        => $this->request->getPost('serious_illness_details'),
                'is_hospitalized'                => $this->request->getPost('is_hospitalized'),
                'hospitalization_details'        => $this->request->getPost('hospitalization_details'),
                'is_taking_medication'           => $this->request->getPost('is_taking_medication'),
                'medication_details'             => $this->request->getPost('medication_details'),
                'uses_tobacco'                   => $this->request->getPost('uses_tobacco'),
                'uses_drugs'                     => $this->request->getPost('uses_drugs'),
                'medical_conditions'             => json_encode($this->request->getPost('medical_conditions') ?? []), // Checklist & Allergies
                'other_allergy'                  => $this->request->getPost('other_allergy'),
                'bleeding_time'                  => $this->request->getPost('bleeding_time'),
                'is_pregnant'                    => $this->request->getPost('pregnancy_status') ?? 0,
                'is_nursing'                     => $this->request->getPost('is_nursing') ?? 0,
                'is_taking_birth_control'        => $this->request->getPost('is_taking_birth_control') ?? 0,
                'blood_type'                     => $this->request->getPost('blood_type'),
                'blood_pressure'                 => ($this->request->getPost('blood_pressure_systolic') && $this->request->getPost('blood_pressure_diastolic'))
                    ? $this->request->getPost('blood_pressure_systolic') . '/' . $this->request->getPost('blood_pressure_diastolic')
                    : null,
            ];

            if (!$db->table('patient_medical_records')->insert($medicalRecord)) {
                $error = $db->error();
                throw new \Exception('Failed to save Medical Record: ' . $error['message']);
            }

            // E. Finalize Transaction
            if ($db->transStatus() === FALSE) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Transaction Failed.');
            } else {
                $db->transCommit();
                return redirect()->to('/admin/appointments')->with('success', 'Appointment and Medical Record saved!');
            }
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
        $model->update($id, ['status' => $status]);
        return redirect()->to('/admin/appointments')->with('success', 'Status updated.');
    }

    private function getActiveServices(): array
    {
        return $this->serviceModel
            ->select('id, service_name, price, estimated_duration_minutes, has_levels')
            ->where('status', 'active')
            ->asArray()
            ->findAll();
    }
    public function patientHistory(int $patientId)
    {
        $patient = $this->patientModel
            ->select('id, patient_code, primary_mobile, gender, birthdate, first_name, middle_name, last_name, name_suffix')
            ->find($patientId);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        $history = $this->apptModel->getPatientHistory($patientId);

        foreach ($history as &$h) {
            $h['fmt_date'] = $h['appointment_date'] ? date('M d, Y', strtotime($h['appointment_date'])) : '';
            $h['fmt_time'] = $h['appointment_time'] ? date('h:i A', strtotime($h['appointment_time'])) : '';
            $h['fmt_end']  = $h['end_time'] ? date('h:i A', strtotime($h['end_time'])) : '';
            $h['duration_label'] = $h['expected_duration_minutes']
                ? $this->formatDuration($h['expected_duration_minutes'])
                : '';
        }

        return view('appointments/patient_appointment_history', [
            'title'   => 'History: ' . $patient['first_name'] . ' ' . $patient['last_name'],
            'patient' => $patient,
            'history' => $history
        ]);
    }
    private function formatDuration(int $minutes): string
    {
        if ($minutes < 60) return "{$minutes}m";
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
    }
}
