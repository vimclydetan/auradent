<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PatientModel;

class AdminController extends BaseController
{
    public function patients()
    {
        $patientModel = new PatientModel();
        $data['patients'] = $patientModel->findAll();
        $data['title'] = "Manage Patients";
        return view('admin/patients/index', $data);
    }

    public function save_patient()
    {
        $userModel = new UserModel();
        $patientModel = new PatientModel();

        // 1. Create User Account first
        $userData = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('contact_number'), PASSWORD_DEFAULT), // Default password is their contact number
            'role'     => 'patient'
        ];
        
        $userId = $userModel->insert($userData);

        // 2. Create Patient Profile linked to User ID
        $patientData = [
            'user_id'        => $userId,
            'full_name'      => $this->request->getPost('full_name'),
            'age'            => $this->request->getPost('age'),
            'gender'         => $this->request->getPost('gender'),
            'contact_number' => $this->request->getPost('contact_number'),
            'address'        => $this->request->getPost('address'),
        ];

        $patientModel->insert($patientData);

        return redirect()->to('/admin/patients')->with('success', 'Patient account created successfully!');
    }
}