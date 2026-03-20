<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'patient_id',
        'dentist_id',
        'appointment_date',
        'end_date',
        'appointment_time',
        'end_time',
        'status',
        'region', // Idinagdag
        'remarks'
    ];

    public function getAppointments()
    {
        return $this->select('
                appointments.*, 
                CONCAT(patients.first_name, " ", patients.last_name) as patient_name,
                CONCAT("Dr. ", dentists.first_name, " ", dentists.last_name) as dentist_name,
                GROUP_CONCAT(DISTINCT services.service_name SEPARATOR ", ") as service_name,
                GROUP_CONCAT(appointment_services.service_level SEPARATOR ", ") as service_level
            ')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->join('appointment_services', 'appointment_services.appointment_id = appointments.id', 'left')
            ->join('services', 'services.id = appointment_services.service_id', 'left')
            ->groupBy('appointments.id')
            ->orderBy('appointment_date', 'DESC')
            ->findAll();
    }
}