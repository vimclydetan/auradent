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
}
