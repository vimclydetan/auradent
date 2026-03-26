<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\UserModel;

class PatientsController extends BaseController
{
    protected $patientModel;
    protected $userModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display the Patient Directory
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('patients');

        // Subquery para makuha ang latest check_in_time mula sa visits table
        $subQuery = $db->table('visits')
            ->select('MAX(check_in_time)')
            ->where('patient_id = patients.id')
            ->getCompiledSelect();

        $patients = $builder->select('
                patients.*, 
                users.email, 
                (' . $subQuery . ') as last_visit_date
            ')
            ->join('users', 'users.id = patients.user_id')
            ->orderBy('patients.last_name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'    => 'Patients Directory',
            'patients' => $patients
        ];

        return view('receptionist/patients/index', $data);
    }


    public function history($id = null)
    {
        if (!$id) return redirect()->back();

        $patientModel = new \App\Models\PatientModel();
        $patient = $patientModel->find($id);

        if (!$patient) return redirect()->back()->with('error', 'Patient not found.');

        $db = \Config\Database::connect();

        // 1. Kunin ang lahat ng records para sa Table
        $history = $db->table('treatment_records tr')
            ->select('
            tr.treatment_date as date,
            tr.tooth_number,
            tr.amount_charge as charge,
            tr.amount_paid as paid,
            tr.balance,
            tr.consent_given,
            tr.signature_path as treat_sig,
            d.first_name as d_fname, d.last_name as d_lname,
            s.service_name
        ')
            ->join('dentists d', 'd.id = tr.dentist_id', 'left')
            ->join('services s', 's.id = tr.service_id', 'left')
            ->where('tr.patient_id', $id)
            ->orderBy('tr.treatment_date', 'DESC')
            ->get()
            ->getResultArray();

        // 2. TAMA NA KWENTA NG TOTAL PAID
        $totalPaid = array_sum(array_column($history, 'paid'));

        // 3. TAMA NA KWENTA NG TOTAL BALANCE (Latest balance lang bawat visit/service)
        $balanceQuery = $db->query("
        SELECT SUM(balance) as total_bal 
        FROM treatment_records 
        WHERE id IN (
            SELECT MAX(id) FROM treatment_records 
            WHERE patient_id = ? 
            GROUP BY visit_id, service_id
        )
    ", [$id])->getRow();

        $data = [
            'title'         => 'Treatment History',
            'patient'       => $patient,
            'history'       => $history,
            'total_paid'    => $totalPaid,
            'total_balance' => $balanceQuery->total_bal ?? 0
        ];

        return view('receptionist/patients/history', $data);
    }

    /**
     * View Patient Profile (Placeholder for your 'View' button)
     */
    public function view($id = null)
    {
        if (!$id) return redirect()->to(base_url('receptionist/patient'))->with('error', 'Invalid Patient ID');

        $patient = $this->patientModel->select('patients.*, users.email, users.username, users.is_active')
            ->join('users', 'users.id = patients.user_id')
            ->find($id);

        if (!$patient) {
            return redirect()->to(base_url('receptionist/patient'))->with('error', 'Patient record not found.');
        }

        // Dito mo pwedeng kunin ang medical history or appointment history ng pasyente sa future
        $data = [
            'title'   => 'Patient Profile | ' . $patient['first_name'] . ' ' . $patient['last_name'],
            'patient' => $patient
        ];

        return view('receptionist/patients/view', $data);
    }

    /**
     * Edit Patient (Placeholder for your 'Edit' button)
     */
    public function edit($id = null)
    {
        if (!$id) return redirect()->to(base_url('receptionist/patient'));

        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to(base_url('receptionist/patient'))->with('error', 'Record not found.');
        }

        $data = [
            'title'   => 'Edit Patient Record',
            'patient' => $patient
        ];

        return view('receptionist/patients/edit', $data);
    }
}
