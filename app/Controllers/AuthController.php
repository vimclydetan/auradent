<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PatientModel;
use CodeIgniter\Controller;

class AuthController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }
        return view('index.php'); // Ngayon lahat ay nasa Landing Page na
    }

    public function login()
    {
        $session = session();
        $userModel = new UserModel();
        $patientModel = new PatientModel();
        $db = \Config\Database::connect();

        $username = $this->request->getVar('username');
        $password = (string)$this->request->getVar('password');
        $ip = $this->request->getIPAddress();

        $user = $userModel->where('username', $username)->first();

        // 🔥 HELPER: Only log if NOT patient role
        $shouldLog = $user && $user['role'] !== 'patient';

        if ($user) {
            if ($user['is_active'] == 0) {
                if ($shouldLog) {
                    $db->table('login_logs')->insert([
                        'user_id' => $user['id'],
                        'username' => $username,
                        'ip_address' => $ip,
                        'status' => 'deactivated'
                    ]);
                }
                return redirect()->back()->with('error', 'Your account is deactivated.');
            }

            if (password_verify($password, $user['password'])) {

                // ✅ SUCCESS LOG (skip patients)
                if ($shouldLog) {
                    $db->table('login_logs')->insert([
                        'user_id' => $user['id'],
                        'username' => $username,
                        'ip_address' => $ip,
                        'status' => 'success'
                    ]);
                }

                $sessionData = [
                    'user_id'    => $user['id'],
                    'username'   => $user['username'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true
                ];

                if ($user['role'] === 'patient') {
                    $patient = $patientModel->where('user_id', $user['id'])->first();
                    if ($patient) {
                        $sessionData['patient_id'] = $patient['id'];
                        $sessionData['full_name']  = $patient['first_name'] . ' ' . $patient['last_name'];
                    }
                }

                $session->set($sessionData);
                return $this->redirectBasedOnRole($user['role']);
            } else {
                // ❌ WRONG PASSWORD LOG (skip patients)
                if ($shouldLog) {
                    $db->table('login_logs')->insert([
                        'user_id' => $user['id'],
                        'username' => $username,
                        'ip_address' => $ip,
                        'status' => 'wrong_password'
                    ]);
                }
                return redirect()->back()->with('error', 'Invalid Credentials.');
            }
        }

        // 🔍 USER NOT FOUND LOG (skip if username looks like patient email pattern)
        // Optional: You can also skip all "not_found" logs if preferred
        if ($shouldLog || !str_contains($username, '@')) {
            $db->table('login_logs')->insert([
                'user_id' => null,
                'username' => $username,
                'ip_address' => $ip,
                'status' => 'not_found'
            ]);
        }

        return redirect()->back()->with('error', 'User not found.');
    }

    public function register()
    {
        $userModel = new UserModel();
        $patientModel = new PatientModel();
        $request = service('request');
        $db = \Config\Database::connect();

        $ip = $request->getIPAddress();
        $throttler = service('throttler');

        if (!$throttler->check(md5($ip), 5, MINUTE * 10)) {
            // Gumamit ng withInput() para hindi mawala ang natype ng user
            return redirect()->back()->withInput()->with('error', 'Too many attempts. Try again later.');
        }

        // VALIDATION RULES
        // Pansinin: Binago ang primary_mobile para tumanggap ng format na "09xx xxx xxxx"
        $rules = [
            'username'       => 'required|min_length[4]|is_unique[users.username]',
            'email'          => 'required|valid_email|is_unique[users.email]',
            'first_name'     => 'required|min_length[2]',
            'last_name'      => 'required|min_length[2]',
            'birthdate'      => 'required|valid_date',
            'primary_mobile' => 'required|regex_match[/^09[0-9]{2} [0-9]{3} [0-9]{4}$/]', // Strict format check
            'password'       => 'required|min_length[8]',
            'gender'         => 'required|in_list[Male,Female,Prefer not to say]'
        ];

        if (!$this->validate($rules)) {
            // IPASA ANG BUONG ERRORS ARRAY + WITH INPUT
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // DUPLICATE PATIENT CHECK
        $existing = $patientModel
            ->where('first_name', $request->getVar('first_name'))
            ->where('last_name', $request->getVar('last_name'))
            ->where('birthdate', $request->getVar('birthdate'))
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'A patient with this name and birthdate is already registered.');
        }

        $db->transStart();

        // SAVE USER
        $userData = [
            'username'    => $request->getVar('username'),
            'password'    => password_hash((string)$request->getVar('password'), PASSWORD_DEFAULT),
            'email'       => $request->getVar('email'),
            'first_name'  => $request->getVar('first_name'),
            'middle_name' => $request->getVar('middle_name') ?: null,
            'last_name'   => $request->getVar('last_name'),
            'birthdate'   => $request->getVar('birthdate'),
            'role'        => 'patient',
            'is_active'   => 1
        ];

        $userModel->insert($userData);
        $userId = $userModel->getInsertID();

        // TANGGALIN ANG SPACES SA MOBILE BAGO ISAVE
        // 0912 345 6789 -> 09123456789
        $cleanMobile = str_replace(' ', '', $request->getVar('primary_mobile'));

        // SAVE PATIENT
        $patientData = [
            'user_id'        => $userId,
            'patient_code'   => $patientModel->generatePatientCode(),
            'first_name'     => $request->getVar('first_name'),
            'middle_name'    => $request->getVar('middle_name') ?: null,
            'last_name'      => $request->getVar('last_name'),
            'name_suffix'    => $request->getVar('suffix') ?: null,
            'birthdate'      => $request->getVar('birthdate'),
            'email'          => $request->getVar('email'),
            'gender'         => $request->getVar('gender'),
            'primary_mobile' => $cleanMobile, // Saved without spaces
            'country'        => 'Philippines'
        ];

        $patientModel->insert($patientData);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong during registration.');
        }

        return redirect()->to('/')->with('success', 'Registration successful! You can now log in.');
    }

    private function redirectBasedOnRole($role)
    {
        return redirect()->to("/$role/dashboard");
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
