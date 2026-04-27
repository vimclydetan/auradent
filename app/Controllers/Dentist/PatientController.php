<?php

namespace App\Controllers\Dentist;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\MedicalRecordModel;
use App\Models\MedicalConditionModel; // ✅ Only this model needed

class PatientController extends BaseController
{
    protected $patientModel;
    protected $medicalHistoryModel;
    protected $medicalConditionModel;

    // ✅ Config: Define which keys are allergies vs medical conditions
    private const ALLERGY_KEYS = [
        'local_anesthetic',
        'penicillin_antibiotics',
        'latex',
        'sulfa_drugs',
        'aspirin',
        'nsaids',
        'codeine',
        'morphine'
    ];

    public function __construct()
    {
        date_default_timezone_set('Asia/Manila');

        $this->patientModel = new PatientModel();
        $this->medicalHistoryModel = new MedicalRecordModel();
        $this->medicalConditionModel = new MedicalConditionModel();
        // ✅ REMOVED: $this->patientConditionModel (not needed)
    }

    public function viewHistory($patientId = null)
    {
        if (!in_array(session('role'), ['admin', 'dentist'], true)) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if (!$patientId || !is_numeric($patientId)) {
            return redirect()->back()->with('error', 'Invalid Patient ID');
        }

        $patient = $this->patientModel->find($patientId);
        if (!$patient) {
            return redirect()->back()->with('error', 'Patient record not found.');
        }

        $medical_history = $this->medicalHistoryModel
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'DESC')
            ->first();

        // ✅ REMOVED: $patient_conditions = $this->getPatientConditions($patientId);
        // (Not needed — we use JSON field instead)

        // ✅ Parse JSON medical_conditions field
        $parsedData = $this->parseMedicalConditionsJson($medical_history['medical_conditions'] ?? null);

        $data = [
            'title'                 => 'Patient History | ' . esc($patient['first_name'] . ' ' . $patient['last_name']),
            'patient'               => $patient,
            'medical_history'       => $medical_history,
            // ✅ REMOVED: 'patient_conditions' => $patient_conditions,
            'allergies_list'        => $parsedData['allergies'],      // ✅ From JSON
            'conditions_list'       => $parsedData['conditions'],     // ✅ From JSON
            'other_allergy'         => $medical_history['other_allergy'] ?? null,
        ];

        return view('dentist/patient/medical_history', $data);
    }

    // ✅ REMOVED: getPatientConditions() method (not needed)

    /**
     * ✅ Parse JSON medical_conditions field and separate allergies vs conditions
     */
    private function parseMedicalConditionsJson(?string $jsonData): array
    {
        $allergies = [];
        $conditions = [];

        if (empty($jsonData)) {
            return ['allergies' => [], 'conditions' => []];
        }

        $items = json_decode($jsonData, true);
        if (!is_array($items)) {
            return ['allergies' => [], 'conditions' => []];
        }

        // ✅ Use medical_conditions table for labels (more reliable than hardcoded array)
        $labels = [];
        $allConditions = $this->medicalConditionModel->where('is_active', 1)->findAll();
        foreach ($allConditions as $cond) {
            $labels[$cond['condition_key']] = $cond['condition_label'];
        }

        foreach ($items as $key) {
            $key = trim($key); // ✅ Clean whitespace from JSON values
            if (empty($key)) continue;

            $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));

            if (in_array($key, self::ALLERGY_KEYS, true)) {
                $allergies[] = $label;
            } else {
                $conditions[] = $label;
            }
        }

        return [
            'allergies' => array_unique($allergies),
            'conditions' => array_unique($conditions)
        ];
    }
}
