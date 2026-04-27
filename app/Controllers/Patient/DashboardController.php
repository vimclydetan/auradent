<?php

namespace App\Controllers\Patient;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\MedicalConditionModel;

class DashboardController extends BaseController
{
    protected $session;
    protected $appointmentModel;
    protected $patientModel;
    protected $medicalConditionModel;
    protected $mcModel;
    public function __construct()
    {
        $this->session = session();
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->medicalConditionModel = new MedicalConditionModel();
        $this->mcModel = new MedicalConditionModel();
        date_default_timezone_set('Asia/Manila');
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $role   = session()->get('role');

        $patient = $this->patientModel->where('user_id', $userId)->first();
        if (!$patient) {
            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard')->with('error', 'Admins cannot access the patient dashboard.');
            }
            session()->destroy();
            return redirect()->to('/')->with('error', 'Patient profile not found.');
        }

        $patientId = $patient['id'];

        // ✅ FETCH USER DATA (for username & email)
        $userModel = new \App\Models\UserModel();
        $userData = $userModel->find($patient['user_id']);

        // ✅ STEP 1: Check Personal Info Completeness
        $isPersonalInfoComplete = $this->checkPersonalInfoComplete($patient);

        // ✅ STEP 2: Determine which modal to show (Priority Flow)
        $showPersonalInfoModal = !$isPersonalInfoComplete;
        $showInsuranceModal = false;
        $showMedicalModal = false;

        if ($isPersonalInfoComplete) {
            $hasInsurance = !empty($patient['has_insurance']) && $patient['has_insurance'] == 1;

            if ($hasInsurance) {
                $insuranceComplete = !empty($patient['insurance_provider']) && !empty($patient['insurance_valid_until']);
                $showInsuranceModal = !$insuranceComplete;
            }

            if (!$showInsuranceModal) {
                $medicalRecord = $db->table('patient_medical_records')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();

                $hasMedicalData = false;
                if ($medicalRecord) {
                    $critical = ['blood_type', 'is_good_health', 'physician_name', 'medical_conditions'];
                    foreach ($critical as $field) {
                        if (!empty($medicalRecord[$field])) {
                            $hasMedicalData = true;
                            break;
                        }
                    }
                }
                $showMedicalModal = !$hasMedicalData;
            }
        }

        // ✅ STEP 3: Fetch supporting data
        $guardianInfo = null;
        if ($showPersonalInfoModal && $this->isMinor($patient['birthdate'])) {
            $guardianInfo = $db->table('guardian_information')
                ->where('patient_id', $patientId)
                ->where('is_primary', 1)
                ->get()
                ->getRowArray();
        }

        $upcomingAppointment = $this->appointmentModel
            ->select('appointments.*, dentists.first_name as d_first, dentists.last_name as d_last')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->where('patient_id', $patientId)
            ->where('appointment_date >=', date('Y-m-d'))
            ->whereIn('appointments.status', ['Pending', 'Confirmed'])
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->first();

        $history = $this->appointmentModel
            ->select('appointments.*, dentists.last_name as d_last')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'DESC')
            ->limit(5)
            ->findAll();

        $stats = [
            'total_appointments' => $this->appointmentModel->where('patient_id', $patientId)->countAllResults(),
            'pending_requests'   => $this->appointmentModel->where(['patient_id' => $patientId, 'status' => 'Pending'])->countAllResults(),
            'completed'          => $this->appointmentModel->where(['patient_id' => $patientId, 'status' => 'Completed'])->countAllResults(),
        ];
        $patientAge = $this->calculateAge($patient['birthdate']);
        // ✅ FETCH ALLERGIES (matching AppointmentService pattern)
                // ✅ FETCH ALL ACTIVE MEDICAL CONDITIONS (simple query)
        $allConditions = $this->medicalConditionModel
            ->where('is_active', 1)
            ->orderBy('category', 'ASC')
            ->orderBy('condition_label', 'ASC')
            ->findAll();

        // ✅ Define allergy keys (matching AppointmentService)
        $allergyKeys = ['local_anesthetic', 'penicillin_antibiotics', 'latex', 'sulfa_drugs', 'aspirin', 'iodine'];

        // ✅ Separate allergies vs other conditions in PHP
        $allergies = [];
        $otherConditions = [];

        foreach ($allConditions as $mc) {
            $isAllergy = in_array($mc['condition_key'], $allergyKeys, true) 
                || stripos($mc['category'], 'allerg') !== false;
            
            if ($isAllergy) {
                $allergies[] = $mc;
            } else {
                $otherConditions[] = $mc;
            }
        }

        // ✅ Group non-allergies by category (EXACT format as appointment.php)
        $grouped = [];
        foreach ($otherConditions as $mc) {
            $cat = $mc['category'] ?? 'Other';
            $grouped[$cat][] = $mc;
        }

        // ✅ STEP 4: Pass ALL data to view
        $data = [
            'title'                    => 'Patient Dashboard',
            'patient'                  => $patient,
            'user_data'                => $userData,
            'upcoming_appointment'     => $upcomingAppointment,
            'recent_appointments'      => $history,
            'stats'                    => $stats,
            'patient_age'              => $patientAge,
            'showPersonalInfoModal'    => $showPersonalInfoModal,
            'showInsuranceModal'       => $showInsuranceModal,
            'showMedicalModal'         => $showMedicalModal,
            'guardian_info'            => $guardianInfo,
            'is_minor'                 => $this->isMinor($patient['birthdate']),
            
            // ✅ Medical data for modal (keys match view)
            'allergies'                => $allergies,
            'grouped'                  => $grouped,
        ];

        return view('patient/dashboard', $data);
    }

    /**
     * ✅ Helper: Check if personal info is complete
     */
    private function checkPersonalInfoComplete(array $patient): bool
    {
        $requiredFields = ['barangay', 'city', 'province', 'primary_mobile', 'birthdate', 'gender'];
        foreach ($requiredFields as $field) {
            if (empty($patient[$field]) || trim($patient[$field]) === '') return false;
        }

        $hasAddressDetails = !empty($patient['house_number']) || !empty($patient['street_name']) || !empty($patient['subdivision']);

        if ($this->isMinor($patient['birthdate'])) {
            $db = \Config\Database::connect();
            $hasGuardian = $db->table('guardian_information')
                ->where('patient_id', $patient['id'])
                ->where('is_primary', 1)
                ->countAllResults() > 0;
            return $hasAddressDetails && $hasGuardian;
        }

        return $hasAddressDetails;
    }

    /**
     * ✅ Helper: Check if minor (age < 18)
     */
    private function isMinor(?string $birthdate): bool
    {
        if (empty($birthdate)) return true;
        $birth = new \DateTime($birthdate);
        $today = new \DateTime();
        return $today->diff($birth)->y < 18;
    }

    /**
     * ✅ SAVE PERSONAL INFO → Redirect to trigger next modal
     */
    public function savePersonalInfo()
    {
        $patientId = $this->request->getPost('patient_id');
        if (!$patientId) return redirect()->back()->with('error', 'Patient ID is missing.');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'barangay' => 'required',
            'city' => 'required',
            'province' => 'required',
            'primary_mobile' => 'required',
            'nickname' => 'permit_empty|max_length[255]', // ✅ Optional but validated
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            // ✅ EDITABLE: Nickname
            'nickname'        => null_if_empty($this->request->getPost('nickname')),

            // ✅ Address Fields
            'house_number'    => null_if_empty($this->request->getPost('house_number')),
            'building_name'   => null_if_empty($this->request->getPost('building_name')),
            'street_name'     => null_if_empty($this->request->getPost('street_name')),
            'subdivision'     => null_if_empty($this->request->getPost('subdivision')),
            'barangay'        => trim($this->request->getPost('barangay')),
            'city'            => trim($this->request->getPost('city')),
            'province'        => trim($this->request->getPost('province')),
            'region'          => null_if_empty($this->request->getPost('region')),
            'postal_code'     => null_if_empty($this->request->getPost('postal_code')),

            // ✅ PH Mobile (09XXXXXXXXX format)
            'primary_mobile'  => clean_ph_mobile($this->request->getPost('primary_mobile')),

            // ✅ General Phone (International/Landline friendly)
            'home_number'     => clean_phone_general($this->request->getPost('home_number')),
            'office_number'   => clean_phone_general($this->request->getPost('office_number')),

            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();

        try {
            $db->transBegin();
            $this->patientModel->update($patientId, $data);

            // Handle guardian for minors (unchanged)
            if ($this->isMinor($this->request->getPost('birthdate'))) {
                $guardianData = [
                    'patient_id' => $patientId,
                    'contact_first_name' => trim($this->request->getPost('guardian_first_name')),
                    'contact_last_name' => trim($this->request->getPost('guardian_last_name')),
                    'contact_middle_name' => null_if_empty($this->request->getPost('guardian_middle_name')),
                    'mobile_number' => clean_ph_mobile($this->request->getPost('guardian_mobile')),
                    'occupation' => null_if_empty($this->request->getPost('guardian_occupation')),
                    'is_primary' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if (empty($guardianData['contact_first_name']) || empty($guardianData['contact_last_name']) || empty($guardianData['mobile_number'])) {
                    throw new \Exception("Guardian information is required for minor patients.");
                }

                $existingGuardian = $db->table('guardian_information')
                    ->where('patient_id', $patientId)
                    ->where('is_primary', 1)
                    ->get()
                    ->getRowArray();

                if ($existingGuardian) {
                    $db->table('guardian_information')->where('id', $existingGuardian['id'])->update($guardianData);
                } else {
                    $db->table('guardian_information')->insert($guardianData);
                }
            }

            $db->transCommit();
            return redirect()->to('/patient/dashboard')->with('success', 'Personal information updated!');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[savePersonalInfo] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    /**
     * ✅ SAVE INSURANCE INFO → Redirect to trigger Medical Modal
     */
    public function saveInsuranceInfo()
    {
        $patientId = $this->request->getPost('patient_id');
        if (!$patientId) return redirect()->back()->with('error', 'Patient ID is missing.');

        $hasInsurance = $this->request->getPost('has_insurance') === '1';

        $validation = \Config\Services::validation();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'barangay' => 'required',
            'city' => 'required',
            'province' => 'required',
            'primary_mobile' => 'required',
            // ✅ General phone: + and digits only, max 20 chars
            'home_number' => 'permit_empty|regex_match[/^[\d+]{1,20}$/]',
            'office_number' => 'permit_empty|regex_match[/^[\d+]{1,20}$/]',
        ]);

        if ($hasInsurance) {
            $validation->setRules([
                'insurance_provider'    => 'required|min_length[3]',
                'insurance_valid_until' => 'required|valid_date',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
        }

        $data = [
            'has_insurance'         => $hasInsurance ? 1 : 0,
            'insurance_provider'    => $hasInsurance ? trim($this->request->getPost('insurance_provider')) : null,
            'insurance_valid_until' => $hasInsurance && !empty($this->request->getPost('insurance_valid_until'))
                ? $this->request->getPost('insurance_valid_until')
                : null,
            'updated_at'            => date('Y-m-d H:i:s'),
        ];

        try {
            $this->patientModel->update($patientId, $data);
            return redirect()->to('/patient/dashboard')->with('success', 'Insurance details saved!');
        } catch (\Exception $e) {
            log_message('error', '[saveInsuranceInfo] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to save insurance: ' . $e->getMessage());
        }
    }
    /**
     * ✅ SAVE MEDICAL HISTORY → Final step, unlock dashboard
     */
    public function saveMedicalHistory()
    {
        $db = \Config\Database::connect();
        $patientId = $this->request->getPost('patient_id');

        if (!$patientId) {
            return redirect()->back()->with('error', 'Patient ID is missing.');
        }

        // ✅ Process medical conditions (same as AppointmentService)
        $conditions = array_filter($this->request->getPost('medical_conditions') ?? []);
        $otherAllergy = $this->request->getPost('other_allergy');

        // Handle "Other" allergy: create if not exists, add to conditions
        if (!empty($otherAllergy)) {

            $key = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim($otherAllergy)));

            $exists = $this->mcModel->where('condition_key', $key)->first();
            if (!$exists) {
                $this->mcModel->insert([
                    'condition_key'   => $key,
                    'condition_label' => ucfirst(trim($otherAllergy)),
                    'category'        => 'allergy',
                    'is_active'       => 1
                ]);
            }
            $conditions[] = $key;
        }

        // ✅ Combine bleeding time (matching service)
        $bleedingMins = (int)($this->request->getPost('bleeding_mins') ?? 0);
        $bleedingSecs = (int)($this->request->getPost('bleeding_secs') ?? 0);
        $bleedingTime = ($bleedingMins > 0 || $bleedingSecs > 0)
            ? trim("{$bleedingMins}m {$bleedingSecs}s")
            : null;

        // ✅ Blood pressure: use combined field (XXX/XXX)
        $bloodPressure = $this->request->getPost('blood_pressure'); // e.g., "120/80"

        $medData = [
            'patient_id' => $patientId,

            // ✅ Conditions as JSON array (matching service)
            'medical_conditions' => !empty($conditions) ? json_encode(array_unique($conditions)) : null,
            'other_allergy'      => null_if_empty($otherAllergy),

            // ✅ Physician info
            'physician_name'      => null_if_empty($this->request->getPost('physician_name')),
            'physician_specialty' => null_if_empty($this->request->getPost('physician_specialty')),
            'physician_address'   => null_if_empty($this->request->getPost('physician_address')),
            'physician_phone'     => null_if_empty($this->request->getPost('physician_phone')),

            // ✅ Health questions (bool_to_int helper)
            'is_good_health'             => bool_to_int($this->request->getPost('is_good_health')),
            'is_under_medical_treatment' => bool_to_int($this->request->getPost('is_under_medical_treatment')),
            'has_serious_illness'        => bool_to_int($this->request->getPost('has_serious_illness')),
            'is_hospitalized'            => bool_to_int($this->request->getPost('is_hospitalized')),
            'is_taking_medication'       => bool_to_int($this->request->getPost('is_taking_medication')),
            'uses_tobacco'               => bool_to_int($this->request->getPost('uses_tobacco')),
            'uses_drugs'                 => bool_to_int($this->request->getPost('uses_drugs')),

            // ✅ Details for "Yes" answers
            'serious_illness_details' => null_if_empty($this->request->getPost('serious_illness_details')),
            'hospitalization_details' => null_if_empty($this->request->getPost('hospitalization_details')),
            'medication_details'      => null_if_empty($this->request->getPost('medication_details')),

            // ✅ Women's health
            'is_pregnant'             => bool_to_int($this->request->getPost('is_pregnant')),
            'is_nursing'              => bool_to_int($this->request->getPost('is_nursing')),
            'is_taking_birth_control' => bool_to_int($this->request->getPost('is_taking_birth_control')),

            // ✅ Vitals
            'blood_type'     => null_if_empty($this->request->getPost('blood_type')),
            'blood_pressure' => null_if_empty($bloodPressure),  // Single field: "120/80"
            'bleeding_time'  => $bleedingTime,                   // Combined: "5m 30s"

            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $medModel = new \App\Models\MedicalRecordModel();

            // ✅ Upsert pattern (matching service)
            $existing = $medModel->where('patient_id', $patientId)->first();

            if ($existing) {
                $medModel->update($existing['id'], $medData);
            } else {
                // Only insert if there's meaningful data
                $hasData = !empty(array_filter($medData, function ($v, $k) {
                    return !in_array($k, ['patient_id', 'created_at', 'updated_at']) && !empty($v);
                }, ARRAY_FILTER_USE_BOTH));

                if ($hasData) {
                    $medModel->insert($medData);
                }
            }

            return redirect()->to('/patient/dashboard')
                ->with('success', '🎉 Medical history saved! You can now book appointments.');
        } catch (\Exception $e) {
            log_message('error', '[saveMedicalHistory] ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }
    /**
     * ✅ Helper: Calculate age from birthdate safely
     */
    private function calculateAge(?string $birthdate): string
    {
        if (empty($birthdate) || $birthdate === '0000-00-00') {
            return 'N/A';
        }

        try {
            $birth = new \DateTime($birthdate);
            $today = new \DateTime();
            return $birth->diff($today)->y . ' years old';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
