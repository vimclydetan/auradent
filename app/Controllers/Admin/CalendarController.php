<?php

namespace App\Controllers\Admin; // Idinagdag ang \Admin dito para tumugma sa folder

use App\Controllers\BaseController; // Import ang BaseController dahil nasa ibang folder ito
use App\Models\AppointmentModel;
use App\Models\DentistModel;
use App\Models\ServiceModel;

class CalendarController extends BaseController
{
    private $timezone;

    public function __construct()
    {
        // Set global timezone
        $this->timezone = new \DateTimeZone('Asia/Manila');
        date_default_timezone_set('Asia/Manila');
    }

    /**
     * I-display ang Calendar View
     */
    public function index()
    {
        $dentistModel = new DentistModel();
        $serviceModel = new ServiceModel();

        $data = [
            'title'    => 'Appointment Calendar',
            'dentists' => $dentistModel->findAll(),
            'services' => $serviceModel->where('status', 'active')->findAll(),
        ];

        // Siguraduhin na ang view file ay nasa: app/Views/admin/appointments/calendar.php
        return view('admin/calendar/index', $data);
    }

    /**
     * Kunin ang JSON Data para sa FullCalendar
     */
    public function getEvents()
    {
        $apptModel = new AppointmentModel();
        
        $appointments = $apptModel->select('appointments.*, patients.last_name as p_last, patients.first_name as p_first, dentists.last_name as d_last')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->whereIn('appointments.status', ['Pending', 'Confirmed'])
            ->findAll();

        $events = [];
        foreach ($appointments as $a) {
            $events[] = [
                'id'              => $a['id'],
                'title'           => $a['p_first'] . ' ' . $a['p_last'] . " (Dr. " . ($a['d_last'] ?? 'None') . ")",
                'start'           => $a['appointment_date'] . 'T' . $a['appointment_time'],
                'end'             => $a['end_date'] . 'T' . $a['end_time'],
                'backgroundColor' => ($a['status'] == 'Pending' ? '#f59e0b' : '#2563eb'),
                'borderColor'     => ($a['status'] == 'Pending' ? '#f59e0b' : '#2563eb'),
                'extendedProps'   => [
                    'status' => $a['status']
                ]
            ];
        }

        return $this->response->setJSON($events);
    }
}