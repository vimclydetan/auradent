<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'patient_code',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'birthdate',
        'gender',
        'primary_mobile',
        'region',
        'province',
        'city',
        'barangay'
    ];

    public function generatePatientCode()
    {
        // Hanapin ang pinakahuling record
        $lastPatient = $this->select('patient_code')
            ->orderBy('id', 'DESC')
            ->first();

        // Kung wala pang record, magsimula sa PAT-00001
        if (!$lastPatient || empty($lastPatient['patient_code'])) {
            return 'PAT-00001';
        }

        // Kunin ang numerong bahagi (hal. "00001")
        $lastCode = $lastPatient['patient_code'];
        $numberStr = str_replace('PAT-', '', $lastCode);

        // Dagdagan ng isa ang numero
        $nextNumber = (int)$numberStr + 1;

        // I-format ulit na may zeros (pad to 5 digits)
        // Kahit lumampas sa 99,999, mag-aadjust ito kusa (hal. PAT-100000)
        return 'PAT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
