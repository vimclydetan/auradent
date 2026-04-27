<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentServiceModel extends BaseModel
{
    protected $table = 'appointment_services';
    protected $primaryKey = 'id';
    protected $allowedFields = ['appointment_id', 'service_id', 'service_level'];

    public function insertServices(int $appointmentId, array $services): bool
    {
        if (empty($services)) return false;

        $data = [];

        foreach ($services as $service) {
            $data[] = [
                'appointment_id' => $appointmentId,
                'service_id'     => $service['service_id'],
                'service_level'  => $service['service_level'] ?? 'Standard',
            ];
        }

        return $this->insertBatch($data);
    }

    public function getServicesByAppointment(int $appointmentId): array
    {
        return $this->select('appointment_services.*, services.service_name, services.price')
            ->join('services', 'services.id = appointment_services.service_id', 'left')
            ->where('appointment_services.appointment_id', $appointmentId)
            ->findAll();
    }

}
