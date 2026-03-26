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
        'remarks'
    ];

    // Gagamitin para sa Main List (Admin/Receptionist)
    public function getAppointments($filters = [])
    {
        $builder = $this->buildBaseQuery();

        // Pwedeng magdagdag ng filters dito sa hinaharap (e.g. by Status)
        if (!empty($filters['status'])) {
            $builder->where('appointments.status', $filters['status']);
        }

        return $builder->orderBy('appointments.appointment_date', 'DESC')
                       ->orderBy('appointments.appointment_time', 'ASC')
                       ->findAll();
    }

    // Specific para sa Receptionist Dashboard (Today only)
    public function getTodaySchedule()
    {
        return $this->buildBaseQuery()
            ->where('appointments.appointment_date', date('Y-m-d'))
            ->whereIn('appointments.status', ['Pending', 'Confirmed']) // Para hindi na makita yung Cancelled/Completed sa list
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();
    }

    // Para sa "View Appointment" Modal
    public function getAppointmentByID($id)
    {
        return $this->buildBaseQuery()
            ->where('appointments.id', $id)
            ->first();
    }

    // Helper function para sa JOIN logic
    protected function buildBaseQuery()
    {
        // Ginamit ang GROUP_CONCAT para makuha ang multiple services sa isang row
        return $this->select('
                appointments.*, 
                patients.patient_code,
                CONCAT(patients.first_name, " ", patients.last_name) as patient_name,
                patients.primary_mobile as patient_phone,
                CONCAT("Dr. ", dentists.first_name, " ", dentists.last_name) as dentist_name,
                GROUP_CONCAT(DISTINCT services.service_name SEPARATOR ", ") as service_name,
                GROUP_CONCAT(appointment_services.service_level SEPARATOR ", ") as service_level
            ')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->join('appointment_services', 'appointment_services.appointment_id = appointments.id', 'left')
            ->join('services', 'services.id = appointment_services.service_id', 'left')
            ->groupBy('appointments.id');
    }
}