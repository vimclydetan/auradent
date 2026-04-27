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
            'dentist_type' => 'required|in_list[Regular,On-call]',
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
                'dentist_type'   => $this->request->getPost('dentist_type'),
                'gender'         => $this->request->getPost('gender'),
                'birthdate'      => $this->request->getPost('birthdate'),
                'house_number'   => $this->request->getPost('house_number'),
                'street'         => $this->request->getPost('street'),
                'barangay'       => $this->request->getPost('barangay'),
                'city'           => $this->request->getPost('city'),
                'province'       => $this->request->getPost('province'),
                'contact_number' => $this->request->getPost('contact_number'),
                'status'         => 'Active'
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

    public function edit($id)
    {
        $dentistModel = new DentistModel();

        // Join users table para makuha rin ang email at username sa edit form
        $dentist = $dentistModel->select('dentists.*, users.email, users.username')
            ->join('users', 'users.id = dentists.user_id')
            ->where('dentists.id', $id)
            ->first();

        if (!$dentist) {
            return redirect()->to('/admin/dentists')->with('error', 'Dentist not found.');
        }

        $data = [
            'title'   => 'Edit Dentist Profile',
            'dentist' => $dentist
        ];

        return view('admin/dentists/edit', $data);
    }

    public function update($id)
    {
        $dentistModel = new DentistModel();
        $userModel = new UserModel();
        $db = \Config\Database::connect();

        $dentist = $dentistModel->find($id);
        $userId = $dentist['user_id'];

        // Validation
        $rules = [
            'username'   => "required|is_unique[users.username,id,{$userId}]",
            'email'      => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'first_name' => 'required',
            'last_name'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // DIREKTA NATING KUNIN ANG NAMES MULA SA FORM
        // Dahil sa JS mo: <option value="${item[nameKey]}">, ang pinapasa nito ay ang PANGALAN na.
        $regionName   = $this->request->getPost('region');
        $provinceName = $this->request->getPost('province');
        $cityName     = $this->request->getPost('city');
        $barangayName = $this->request->getPost('barangay');

        $db->transStart();

        try {
            // 1. UPDATE USERS TABLE
            $userData = [
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
            ];

            if ($this->request->getPost('password')) {
                $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            $userModel->update($userId, $userData);

            // 2. HANDLE PROFILE PIC
            $fileName = $dentist['profile_pic'];
            $img = $this->request->getFile('profile_pic');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                $fileName = $img->getRandomName();
                $img->move(FCPATH . 'uploads/profile', $fileName);
            }

            // 3. UPDATE DENTIST TABLE
            $dentistModel->update($id, [
                'profile_pic'    => $fileName,
                'first_name'     => $this->request->getPost('first_name'),
                'middle_name'    => $this->request->getPost('middle_name'),
                'last_name'      => $this->request->getPost('last_name'),
                'extension_name' => $this->request->getPost('extension_name'),
                'dentist_type'   => $this->request->getPost('dentist_type'),
                'gender'         => $this->request->getPost('gender'),
                'birthdate'      => $this->request->getPost('birthdate'),
                'house_number'   => $this->request->getPost('house_number'),
                'street'         => $this->request->getPost('street'),
                'region'         => $regionName,   // Ito ay may laman na string/pangalan
                'province'       => $provinceName, // Ito ay may laman na string/pangalan
                'city'           => $cityName,     // Ito ay may laman na string/pangalan
                'barangay'       => $barangayName, // Ito ay may laman na string/pangalan
                'contact_number' => $this->request->getPost('contact_number'),
            ]);

            $db->transComplete();

            return redirect()->to('/admin/dentists/view/' . $id)->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function deactivate($id)
    {
        $dentistModel = new DentistModel();
        $userModel = new UserModel();
        $db = \Config\Database::connect();

        // 1. Hanapin muna ang dentist para makuha ang user_id
        $dentist = $dentistModel->find($id);
        if (!$dentist) {
            return redirect()->back()->with('error', 'Dentist not found.');
        }

        $db->transStart();
        try {
            // 2. Update sa Dentists Table (status column)
            $dentistModel->update($id, [
                'status' => 'Inactive'
            ]);

            // 3. Update sa Users Table (is_active column - tinyint 0)
            $userModel->update($dentist['user_id'], [
                'is_active' => 0
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to update status.');
            }

            return redirect()->to('/admin/dentists')->with('success', 'Dentist account deactivated.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $dentistModel = new DentistModel();
        $userModel = new UserModel();
        $db = \Config\Database::connect();

        $dentist = $dentistModel->find($id);
        if (!$dentist) return redirect()->back()->with('error', 'Dentist not found.');

        $db->transStart();
        try {
            // Update Dentist Table (Active)
            $dentistModel->update($id, ['status' => 'Active']);

            // Update Users Table (tinyint 1)
            $userModel->update($dentist['user_id'], ['is_active' => 1]);

            $db->transComplete();
            return redirect()->to('/admin/dentists')->with('success', 'Dentist account restored.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    private function getAddressName($file, $code, $codeKey, $nameKey)
    {
        $path = FCPATH . 'data/ph-addresses/' . $file . '.json';

        if (!file_exists($path)) return null;

        $json = json_decode(file_get_contents($path), true);

        foreach ($json as $item) {
            if ($item[$codeKey] == $code) {
                return $item[$nameKey];
            }
        }

        return null;
    }
}
