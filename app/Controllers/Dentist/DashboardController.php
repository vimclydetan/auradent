<?php

namespace App\Controllers\Dentist;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\MedicalRecordModel;
use App\Models\DentistModel;
use App\Models\ServiceModel;

class DashboardController extends BaseController
{
    protected $appointmentModel;
    protected $patientModel;
    protected $medicalRecordModel;
    protected $serviceModel;
    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->medicalRecordModel = new MedicalRecordModel();
        $this->serviceModel = new ServiceModel();
    }

    public function index()
    {
        $today = date('Y-m-d');

        // 1. Kunin ang user_id ng naka-login
        $userId = session()->get('user_id');

        // 2. Hanapin ang totoong dentist_id niya sa dentists table
        $dentistModel = new DentistModel();
        $dentistProfile = $dentistModel->where('user_id', $userId)->first();

        // Kung walang profile, default to 0 para hindi mag-error
        $dentistId = $dentistProfile ? $dentistProfile['id'] : 0;

        $nextWeek = date('Y-m-d', strtotime('+7 days'));

        $data = [
            'title'             => 'Dentist Dashboard',
            'myTodayCount'      => $this->appointmentModel->where('dentist_id', $dentistId)->where('appointment_date', $today)->countAllResults(),
            'myCompletedCount'  => $this->appointmentModel->where('dentist_id', $dentistId)->where('appointment_date', $today)->where('status', 'Completed')->countAllResults(),
            'myUpcomingCount'   => $this->appointmentModel->where('dentist_id', $dentistId)->where('appointment_date >', $today)->where('appointment_date <=', $nextWeek)->countAllResults(),
            'myPatientCount'    => $this->appointmentModel->select('patient_id')->where('dentist_id', $dentistId)->distinct()->countAllResults(),
            'mySchedule'        => $this->appointmentModel->getDentistTodaySchedule($dentistId, $today),

            // IPASA ANG SERVICES PARA SA UPDATE TREATMENT MODAL
            'services'          => $this->serviceModel->where('status', 'active')->findAll()
        ];

        return view('dentist/dashboard', $data);
    }
}
