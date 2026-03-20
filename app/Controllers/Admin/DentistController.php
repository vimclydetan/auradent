<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DentistModel;
use App\Models\UserModel;

class DentistController extends BaseController
{
    // Eto ang index method na hinahanap ng system
    public function index()
    {
        $dentistModel = new DentistModel();
        
        // Kinukuha natin ang data para ipakita sa table
        $data = [
            'title'    => 'Dentist Directory',
            'dentists' => $dentistModel->findAll(), 
        ];

        return view('admin/dentists/index', $data);
    }

    // Eto naman ang method para sa pag-save (store)
    public function store()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $dentistModel = new DentistModel();

        // Validation
        $rules = [
            'username'         => 'required|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[5]',
            'confirm_password' => 'matches[password]',
            'first_name'       => 'required',
            'last_name'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db->transStart();

        try {
            // Save sa Users Table
            $userModel->insert([
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'     => 'dentist',
            ]);
            
            $userId = $userModel->insertID();

            // Handle Profile Pic
            $img = $this->request->getFile('profile_pic');
            $fileName = 'default.png';
            if ($img->isValid() && !$img->hasMoved()) {
                $fileName = $img->getRandomName();
                $img->move(FCPATH . 'uploads/profile', $fileName);
            }

            // Save sa Dentists Table
            $dentistModel->insert([
                'user_id'        => $userId,
                'profile_pic'    => $fileName,
                'first_name'     => $this->request->getPost('first_name'),
                'middle_name'    => $this->request->getPost('middle_name'),
                'last_name'      => $this->request->getPost('last_name'),
                'extension_name' => $this->request->getPost('extension_name'),
                'gender'         => $this->request->getPost('gender'),
                'birthdate'      => $this->request->getPost('birthdate'),
                'house_number'   => $this->request->getPost('house_number'),
                'street'         => $this->request->getPost('street'),
                'barangay'       => $this->request->getPost('barangay'),
                'city'           => $this->request->getPost('city'),
                'province'       => $this->request->getPost('province'),
                'contact_number' => $this->request->getPost('contact_number'),
            ]);

            $db->transComplete();
            return redirect()->to('/admin/dentists')->with('success', 'Dentist added successfully!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function view($id)
{
    $dentistModel = new \App\Models\DentistModel();

    // Kuhanin ang dentist data at i-join ang users table para sa email at username
    $dentist = $dentistModel->select('dentists.*, users.email, users.username, users.role')
                            ->join('users', 'users.id = dentists.user_id')
                            ->where('dentists.id', $id)
                            ->first();

    if (!$dentist) {
        return redirect()->to('/admin/dentists')->with('error', 'Dentist not found.');
    }

    $data = [
        'title'   => 'Dentist Profile',
        'dentist' => $dentist
    ];

    return view('admin/dentists/view', $data);
}
}