<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\TreatmentRecordModel;
// Idagdag ang mga models para sa medical history
use App\Models\MedicalRecordModel;
use App\Models\PatientConditionModel;
use App\Models\MedicalConditionModel;


class PatientsController extends BaseController
{
    protected $patientModel;
    protected $treatmentModel;
    protected $medicalHistoryModel;
    protected $medicalConditionModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->treatmentModel = new TreatmentRecordModel();
        $this->medicalHistoryModel = new MedicalRecordModel();
        $this->medicalConditionModel = new MedicalConditionModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Patients Directory',
            'patients' => $this->patientModel->getPatientsWithLastVisit()
        ];

        return view('receptionist/patients/index', $data);
    }

    public function view($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('receptionist/patients'))->with('error', 'Invalid Patient ID');
        }

        // 1. Kunin ang basic patient info
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to(base_url('receptionist/patients'))->with('error', 'Patient record not found.');
        }

        // 2. Kunin ang Medical History (Physician info, Vitals, Yes/No questions)
        // Ipinagpapalagay na ang table ay may 'patient_id' column
        $medical_history = $this->medicalHistoryModel->where('patient_id', $id)->first();

        // 3. Kunin ang mga Specific Medical Conditions/Allergies
        // Ito yung mga naka-check sa checkboxes (Cardio, Diabetes, etc.)
        // Ginagamitan ng join sa master medical_conditions table para makuha ang 'condition_label'
        $patient_conditions = $this->medicalConditionModel->getConditionsByPatient($id);

        $data = [
            'title'              => 'Patient Profile | ' . $patient['first_name'] . ' ' . $patient['last_name'],
            'patient'            => $patient,
            'medical_history'    => $medical_history,
            'patient_conditions' => $patient_conditions
        ];

        return view('receptionist/patients/view', $data);
    }

    public function history($id = null)
    {
        if (!$id) return redirect()->back();

        $patient = $this->patientModel->find($id);
        if (!$patient) return redirect()->back()->with('error', 'Patient not found.');

        $history = $this->treatmentModel->getHistoryByPatient($id);
        $totalPaid = array_sum(array_column($history, 'paid'));
        $totalBalance = $this->treatmentModel->getTotalBalance($id);

        $data = [
            'title'         => 'Treatment History',
            'patient'       => $patient,
            'history'       => $history,
            'total_paid'    => $totalPaid,
            'total_balance' => $totalBalance
        ];

        return view('receptionist/patients/history', $data);
    }

    public function edit($id = null)
    {
        if (!$id) return redirect()->to(base_url('receptionist/patients'));

        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to(base_url('receptionist/patients'))->with('error', 'Record not found.');
        }

        $data = [
            'title'   => 'Edit Patient Record',
            'patient' => $patient
        ];

        return view('receptionist/patients/edit', $data);
    }
}
