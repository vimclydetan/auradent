<?php
namespace App\Models;
use CodeIgniter\Model;

class MedicalConditionModel extends Model {
    protected $table = 'medical_conditions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['condition_key', 'condition_label', 'category', 'is_active'];
}