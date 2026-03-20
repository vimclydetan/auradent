<?php

namespace App\Models;

use CodeIgniter\Model;

class DentistModel extends Model
{
    protected $table = 'dentists';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'profile_pic',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'gender',
        'birthdate',
        'house_number',
        'street',
        'barangay',
        'city',
        'province',
        'contact_number'
    ];
    protected $useTimestamps = true;
}
