<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'service_name',
        'description',
        'price',
        'has_levels',
        'price_simple',
        'price_moderate',
        'price_severe',
        'status'
    ];
}
