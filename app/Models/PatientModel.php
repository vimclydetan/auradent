<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'patient_code',
        'first_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'nickname',

        // contact
        'primary_mobile',
        'home_number',
        'office_number',
        'email',

        // address
        'house_number',
        'building_name',
        'street_name',
        'subdivision',
        'barangay',
        'city',
        'province',
        'postal_code',
        'region',
        'country',

        // personal
        'birthdate',
        'gender',

        // others
        'guardian_id',
        'has_insurance',
        'insurance_provider',
        'insurance_policy_number',
        'insurance_valid_until',
        'referred_by',
        'reason_for_consultation',
        'previous_dentist',
        'last_dental_visit',
        'signature_path'
    ];
    protected $useAutoIncrement = true;
    public function generatePatientCode(): string
    {
        $prefix = 'PAT-';
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;

            // Get the highest existing numeric suffix (ignoring NULLs)
            $lastPatient = $this->select('patient_code')
                ->where('patient_code IS NOT NULL')
                ->where('patient_code !=', '')
                ->orderBy('patient_code', 'DESC')
                ->first();

            if (!$lastPatient || empty($lastPatient['patient_code'])) {
                $nextNumber = 1;
            } else {
                // Extract number from code like "PAT-00008" → 8
                $lastCode = $lastPatient['patient_code'];
                $numberStr = preg_replace('/[^0-9]/', '', $lastCode);
                $nextNumber = !empty($numberStr) ? (int)$numberStr + 1 : 1;
            }

            // Format with zero-padding (5 digits)
            $candidate = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // 🔥 CHECK IF CODE ALREADY EXISTS
            $exists = $this->where('patient_code', $candidate)->first();

            if (!$exists) {
                return $candidate;
            }

            // If collision, try next number (with safety limit)
            if ($attempt >= $maxAttempts) {
                // Fallback: timestamp-based unique code
                return $prefix . date('Ymd') . '-' . bin2hex(random_bytes(3));
            }
        } while (true);
    }

    public function getPatientsWithLastVisit()
    {
        $db = \Config\Database::connect();

        // Subquery para sa latest visit
        $subQuery = $db->table('visits')
            ->select('MAX(check_in_time)')
            ->where('patient_id = patients.id')
            ->getCompiledSelect();

        return $this->select('patients.*, users.email, (' . $subQuery . ') as last_visit_date')
            ->join('users', 'users.id = patients.user_id', 'left')
            ->orderBy('patients.last_name', 'ASC')
            ->findAll();
    }

    public function getPatientProfile($id)
    {
        return $this->select('patients.*, users.email, users.username, users.is_active')
            ->join('users', 'users.id = patients.user_id', 'left')
            ->find($id);
    }

    public function searchPatients($term)
    {
        $builder = $this->builder();
        $builder->select('id, first_name, last_name, patient_code')
            ->groupStart()
            ->like('first_name', $term)
            ->orLike('last_name', $term)
            ->orLike('patient_code', $term)
            // Dagdag: Para gumana kapag buong pangalan ang tinype
            ->orLike("CONCAT(first_name, ' ', last_name)", $term)
            ->groupEnd()
            ->limit(20);

        $patients = $builder->get()->getResultArray();

        $results = [];
        foreach ($patients as $p) {
            $code = !empty($p['patient_code']) ? ' [' . $p['patient_code'] . ']' : '';

            $results[] = [
                'id'   => $p['id'],
                // Juan Dela Cruz [PAT-00001]
                'text' => esc($p['first_name'] . ' ' . $p['last_name'] . $code)
            ];
        }
        return $results;
    }
}
