<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Clinic extends BaseConfig
{
    public string $phone;
    public string $email;
    public string $name = 'AuraDent Dental Center';
    public string $address = 'Unit 4, Dynasty Building, Brgy. Halang, Calamba City, Laguna';

    public function __construct()
    {
        parent::__construct();

        $this->phone = env('clinic.phone', '09562746203');
        $this->email = env('clinic.email', 'auradent@gmail.com');
    }
}