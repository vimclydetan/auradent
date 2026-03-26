<?php

namespace App\Controllers\Patient;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\ServiceModel;
use App\Models\DentistModel;
use App\Models\PatientModel;

class AppointmentController extends BaseController
{
    // Sa AppointmentController.php index() method:
    public function index()
    {
        $userId = session()->get('user_id');

        // 1. Check kung may naka-login na user
        if (!$userId) {
            return "DEBUG ERROR: Walang user_id sa session. Naka-login ka ba?";
        }

        $patientModel = new \App\Models\PatientModel();
        $patient = $patientModel->where('user_id', $userId)->first();

        // 2. Check kung may Patient Record
        if (!$patient) {
            return "DEBUG ERROR: Walang record sa 'patients' table para sa user_id: " . $userId;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('appointments');
        $builder->select('appointments.*, services.service_name, d.first_name as d_first, d.last_name as d_last');
        $builder->join('services', 'services.id = appointments.service_id', 'left');
        $builder->join('dentists d', 'd.id = appointments.dentist_id', 'left');

        // DITO ANG CRITICAL PART:
        // Siguraduhin na ang 'patient_id' sa table mo ay MATCH sa 'id' ng patient
        $builder->where('appointments.patient_id', $patient['id']);
        $builder->orderBy('appointment_date', 'ASC');

        $appointments = $builder->get()->getResultArray();

        $upcoming = [];
        $past = [];

        foreach ($appointments as $a) {
            // Formatting
            $a['dentist_name'] = $a['d_first'] ? 'Dr. ' . $a['d_first'] . ' ' . $a['d_last'] : 'Assigning a Dentist...';
            $a['fmt_date'] = date('M d, Y', strtotime($a['appointment_date']));
            $a['fmt_time'] = date('h:i A', strtotime($a['appointment_time']));
            $a['fmt_end'] = $a['end_time'] ? date('h:i A', strtotime($a['end_time'])) : '';

            // Mas maluwag na comparison para lumabas lahat muna
            $status = strtolower($a['status']);

            if ($status === 'pending' || $status === 'confirmed') {
                $upcoming[] = $a;
            } else {
                $past[] = $a;
            }
        }

        $data = [
            'title'    => 'My Appointments',
            'patient'  => $patient,
            'upcoming' => $upcoming,
            'past'     => $past,
            'services' => (new \App\Models\ServiceModel())->findAll(),
            'dentists' => (new \App\Models\DentistModel())->findAll(),
        ];

        return view('patient/appointments/index', $data);
    }

    // Mag-book ng Appointment
    public function store()
    {
        $apptModel = new AppointmentModel();
        $userId = session()->get('user_id');

        $patientModel = new PatientModel();
        $patient = $patientModel->where('user_id', $userId)->first();

        $data = [
            'patient_id'       => $patient['id'],
            'service_id'       => $this->request->getPost('service_id'),
            'dentist_id'       => $this->request->getPost('dentist_id'),
            'appointment_date' => $this->request->getPost('appointment_date'),
            'appointment_time' => $this->request->getPost('appointment_time'),
            'end_date'         => $this->request->getPost('appointment_date'),
            'end_time'         => $this->request->getPost('end_time'),
            'status'           => 'Pending',
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        if ($apptModel->insert($data)) {
            return redirect()->back()->with('success', 'Appointment requested successfully.');
        }

        return redirect()->back()->with('error', 'Booking failed.');
    }

    // Cancel Appointment
    public function status($id, $status)
    {
        $apptModel = new AppointmentModel();

        // Safety check: Pasyente ba ang may-ari nito?
        $userId = session()->get('user_id');
        $patientModel = new PatientModel();
        $patient = $patientModel->where('user_id', $userId)->first();

        $appt = $apptModel->where('id', $id)->where('patient_id', $patient['id'])->first();

        if (!$appt || $status !== 'Cancelled') {
            return redirect()->back()->with('error', 'Action not allowed.');
        }

        $apptModel->update($id, ['status' => 'Cancelled']);
        return redirect()->back()->with('success', 'Appointment cancelled.');
    }

    // Reschedule
    public function reschedule()
    {
        $apptModel = new AppointmentModel();
        $id = $this->request->getPost('appointment_id');

        $data = [
            'appointment_date' => $this->request->getPost('appointment_date'),
            'appointment_time' => $this->request->getPost('appointment_time'),
            'end_date'         => $this->request->getPost('appointment_date'),
            'end_time'         => $this->request->getPost('end_time'),
            'status'           => 'Pending'
        ];

        if ($apptModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Reschedule request sent.');
        }

        return redirect()->back()->with('error', 'Failed to reschedule.');
    }
}
