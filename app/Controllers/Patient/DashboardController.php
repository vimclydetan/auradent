<?php

namespace App\Controllers\Patient;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\MedicalConditionModel; // Idinagdag ito

class DashboardController extends BaseController
{
    protected $session;
    protected $appointmentModel;
    protected $patientModel;
    protected $medicalConditionModel; // Idinagdag ito

    public function __construct()
    {
        $this->session = session();
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->medicalConditionModel = new MedicalConditionModel(); // Idinagdag ito

        // Set timezone
        date_default_timezone_set('Asia/Manila');
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $role   = session()->get('role');

        // 1. Hanapin ang Patient record
        $patient = $this->patientModel->where('user_id', $userId)->first();

        if (!$patient) {
            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard')->with('error', 'Admins cannot access the patient dashboard.');
            }
            session()->destroy();
            return redirect()->to('/')->with('error', 'Patient profile not found.');
        }

        $patientId = $patient['id'];

        // 2. CHECK KUNG MAY MEDICAL HISTORY NA (Eto ang kailangan para sa Modal)
        $medicalRecord = $db->table('patient_medical_records')
            ->where('patient_id', $patientId)
            ->get()
            ->getRowArray();

        // 3. Kunin ang "Upcoming Appointment"
        $upcomingAppointment = $this->appointmentModel
            ->select('appointments.*, dentists.first_name as d_first, dentists.last_name as d_last')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->where('patient_id', $patientId)
            ->where('appointment_date >=', date('Y-m-d'))
            ->whereIn('appointments.status', ['Pending', 'Confirmed'])
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->first();

        // 4. Kunin ang Appointment History
        $history = $this->appointmentModel
            ->select('appointments.*, dentists.last_name as d_last')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'DESC')
            ->limit(5)
            ->findAll();

        // 5. Statistics
        $stats = [
            'total_appointments' => $this->appointmentModel->where('patient_id', $patientId)->countAllResults(),
            'pending_requests'   => $this->appointmentModel->where(['patient_id' => $patientId, 'status' => 'Pending'])->countAllResults(),
            'completed'          => $this->appointmentModel->where(['patient_id' => $patientId, 'status' => 'Completed'])->countAllResults(),
        ];

        $data = [
            'title'                => 'Patient Dashboard',
            'patient'              => $patient,
            'upcoming_appointment' => $upcomingAppointment,
            'recent_appointments'  => $history,
            'stats'                => $stats,
            // ETO ANG MGA DINAGDAG PARA SA MODAL:
            'showMedicalModal'     => empty($medicalRecord), // TRUE kung wala pang record
            'medical_conditions'   => $this->medicalConditionModel->where('is_active', 1)->orderBy('category', 'ASC')->findAll(),
        ];

        return view('patient/dashboard', $data);
    }

    /**
     * Eto ang method na mag-sa-save ng data galing sa Modal
     */
    public function saveMedicalHistory()
    {
        $db = \Config\Database::connect();

        // 1. Kunin ang Patient ID mula sa form (hidden input)
        $patientId = $this->request->getPost('patient_id');

        if (!$patientId) {
            return redirect()->back()->with('error', 'Patient ID is missing.');
        }

        // 2. I-prepare ang data (Dapat tugma ito sa table structure mo gaya sa admin)
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
            'medical_conditions'             => json_encode($this->request->getPost('medical_conditions') ?? []), // Checklist
            'other_allergy'                  => $this->request->getPost('other_allergy'),
            'bleeding_time'                  => $this->request->getPost('bleeding_time'),
            'is_pregnant'                    => $this->request->getPost('is_pregnant') ?? 0,
            'is_nursing'                     => $this->request->getPost('is_nursing') ?? 0,
            'is_taking_birth_control'        => $this->request->getPost('is_taking_birth_control') ?? 0,
            'blood_type'                     => $this->request->getPost('blood_type'),
        ];

        try {
            // 3. I-insert sa table (Siguraduhin na 'patient_medical_records' ang table name mo)
            $db->table('patient_medical_records')->insert($medicalRecord);

            // 4. Kapag successful, ibalik sa dashboard. 
            // Dahil may record na siya, hindi na lilitaw ang modal sa index() function.
            return redirect()->to('/patient/dashboard')->with('success', 'Medical history has been saved. Welcome to your dashboard!');
        } catch (\Exception $e) {
            // Log the error if something goes wrong
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
