<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\PatientModel;

class DashboardController extends BaseController
{
    protected $appointmentModel;
    protected $patientModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
    }

    public function index()
    {
        $today = date('Y-m-d');

        $data = [
            'title'             => 'Receptionist Dashboard',
            'countToday'        => $this->appointmentModel->where('appointment_date', $today)->countAllResults(),
            'countPending'      => $this->appointmentModel->where('status', 'Pending')->countAllResults(),
            'countConfirmed'    => $this->appointmentModel->where('appointment_date', $today)
                                                          ->where('status', 'Confirmed')
                                                          ->countAllResults(),
            'totalPatients'     => $this->patientModel->countAllResults(),
            'todayAppointments' => $this->appointmentModel->getTodaySchedule(),
        ];

        return view('receptionist/dashboard', $data);
    }
}