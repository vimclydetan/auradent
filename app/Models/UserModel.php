<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Napaka-importante na nandito ang fields na ito:
    protected $allowedFields = ['username', 'password', 'email', 'role'];

    protected $useTimestamps = false;
}