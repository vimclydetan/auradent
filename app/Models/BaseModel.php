<?php

namespace App\Models;
// app/Models/BaseModel.php 
use CodeIgniter\Model;

class BaseModel extends Model
{
    /**
     * @param int|string|array|null $id
     * @return array|null
     */
    public function find($id = null): array|null
    {
        $result = parent::find($id);
        return is_array($result) ? $result : null;
    }
}