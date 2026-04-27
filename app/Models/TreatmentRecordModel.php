<?php

namespace App\Models;

use CodeIgniter\Model;

class TreatmentRecordModel extends Model
{
    protected $table            = 'treatment_records';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'visit_id',
        'patient_id',
        'dentist_id',
        'service_id',
        'tooth_number',
        'amount_charge',
        'amount_paid',
        'balance',
        'treatment_date'
    ];

    public function getHistoryByPatient($patientId)
    {
        return $this->db->table('treatment_records tr')
            ->select('
                tr.treatment_date as date,
                tr.tooth_number,
                tr.amount_charge as charge,
                tr.amount_paid as paid,
                tr.balance,
                tr.consent_given,
                tr.signature_path as treat_sig,
                d.first_name as d_fname, d.last_name as d_lname,
                s.service_name
            ')
            ->join('dentists d', 'd.id = tr.dentist_id', 'left')
            ->join('services s', 's.id = tr.service_id', 'left')
            ->where('tr.patient_id', $patientId)
            ->orderBy('tr.treatment_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getTotalBalance($patientId)
    {
        $sql = "
            SELECT SUM(balance) as total_bal 
            FROM treatment_records 
            WHERE id IN (
                SELECT MAX(id) FROM treatment_records 
                WHERE patient_id = ? 
                GROUP BY visit_id, service_id
            )
        ";
        $query = $this->db->query($sql, [$patientId]);
        return $query->getRow()->total_bal ?? 0;
    }
}
