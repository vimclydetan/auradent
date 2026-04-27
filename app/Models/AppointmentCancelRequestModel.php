<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentCancelRequestModel extends Model
{
    protected $table      = 'appointment_cancel_requests';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'appointment_id', 
        'patient_id', 
        'reason', 
        'status', 
        'denial_reason', 
        'action_by', 
        'action_at', 
        'created_at'
    ];

    protected $useTimestamps = false; // Manual created_at handle
}