<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicalRecordModel extends Model
{
    protected $table = 'patient_medical_records';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'patient_id',
        'chief_complaint',
        'diagnosis_notes',
        'treatment_plan_notes',
        'dental_chart_path',
        'physician_name',
        'physician_specialty',
        'physician_address',
        'physician_phone',
        'is_good_health',
        'is_under_medical_treatment',
        'has_serious_illness',
        'serious_illness_details',
        'is_hospitalized',
        'hospitalization_details',
        'is_taking_medication',
        'medication_details',
        'uses_tobacco',
        'uses_drugs',
        'medical_conditions',
        'other_allergy',
        'bleeding_time',
        'is_pregnant',
        'is_nursing',
        'is_taking_birth_control',
        'blood_type',
        'blood_pressure',
    ];

    // 🔒 Validation rules
    protected $validationRules = [
        'patient_id' => 'required|is_natural_no_zero',
        'chief_complaint' => 'permit_empty|max_length[2000]',
        'diagnosis_notes' => 'permit_empty|max_length[2000]',
        'treatment_plan_notes' => 'permit_empty|max_length[2000]',
        'dental_chart_path' => 'permit_empty|max_length[255]',
        'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown]',
        'blood_pressure' => 'permit_empty|regex_match[/^\d{2,3}\/\d{2,3}$/]',
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient ID is required.',
            'is_natural_no_zero' => 'Invalid patient ID.'
        ],
        'blood_type' => [
            'in_list' => 'Please select a valid blood type.'
        ]
    ];

    // 🔒 Skip validation only when absolutely necessary (e.g., bulk imports)
    protected $skipValidation = false;

    public function saveDentalChart(int $patientId, string $chartPath, array $clinicalData = []): bool
    {
        // 🔒 Sanitize inputs
        $data = [
            'patient_id'           => $patientId,
            'chief_complaint'      => !empty($clinicalData['complaint']) ? strip_tags(trim($clinicalData['complaint'])) : null,
            'diagnosis_notes'      => !empty($clinicalData['diagnosis']) ? strip_tags(trim($clinicalData['diagnosis'])) : null,
            'treatment_plan_notes' => !empty($clinicalData['treatment_plan']) ? strip_tags(trim($clinicalData['treatment_plan'])) : null,
            'dental_chart_path'    => $chartPath,
        ];

        $record = $this->where('patient_id', $patientId)
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($record) {
            return $this->update($record['id'], $data);
        }
        
        return $this->insert($data);
    }

    // 🔒 Secure getter: limit fields returned
    public function getLatestChart(int $patientId, bool $includeSensitive = false): ?array
    {
        $builder = $this->where('patient_id', $patientId)
            ->where('dental_chart_path IS NOT NULL')
            ->orderBy('created_at', 'DESC');
            
        // 🔒 Only include sensitive fields if explicitly requested + authorized
        if (!$includeSensitive) {
            $builder->select('id, patient_id, chief_complaint, diagnosis_notes, treatment_plan_notes, dental_chart_path, created_at');
        }
        
        return $builder->first();
    }
    
    // 🔒 Audit-friendly method for retrieving records
    public function getPatientRecords(int $patientId, int $limit = 10, int $offset = 0): array
    {
        return $this->where('patient_id', $patientId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }
}