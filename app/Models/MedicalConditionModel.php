<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicalConditionModel extends Model
{
    protected $table = 'medical_conditions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['condition_key', 'condition_label', 'category', 'is_active'];

    /**
     * Get patient's medical conditions by parsing JSON from patient_medical_records
     */
    public function getConditionsByPatient($patient_id)
    {
        $medicalRecordModel = new MedicalRecordModel();
        $record = $medicalRecordModel->where('patient_id', $patient_id)->first();

        if (!$record || empty($record['medical_conditions'])) {
            return [];
        }

        $conditionKeys = json_decode($record['medical_conditions'], true);

        if (!is_array($conditionKeys) || empty($conditionKeys)) {
            return [];
        }

        $conditionKeys = array_map('trim', $conditionKeys);

        // ✅ Remove is_active filter for testing - kunin lahat ng match
        return $this->whereIn('condition_key', $conditionKeys)->findAll();
    }
}
