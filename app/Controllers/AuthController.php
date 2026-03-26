<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PatientModel; // Idagdag ito para makuha ang patient_id
use CodeIgniter\Controller;

class AuthController extends BaseController
{
    public function index()
    {
        // Kung naka-login na, i-redirect na agad sa tamang dashboard
        if (session()->get('isLoggedIn')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $userModel = new UserModel();
        $patientModel = new PatientModel();

        $username = $this->request->getVar('username');
        $password = (string)$this->request->getVar('password');

        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // Check kung active ang account
            if ($user['is_active'] == 0) {
                return redirect()->back()->with('error', 'Your account is deactivated. Please contact the clinic.');
            }

            if (password_verify($password, $user['password'])) {
                
                $sessionData = [
                    'id'         => $user['id'],
                    'user_id'    => $user['id'],
                    'username'   => $user['username'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true
                ];

                // KUNG PATIENT: Kunin ang kanyang patient_id mula sa patients table
                if ($user['role'] === 'patient') {
                    $patient = $patientModel->where('user_id', $user['id'])->first();
                    if ($patient) {
                        $sessionData['patient_id'] = $patient['id'];
                        $sessionData['full_name']  = $patient['first_name'] . ' ' . $patient['last_name'];
                    }
                }

                $session->set($sessionData);

                // Redirect base sa role
                return $this->redirectBasedOnRole($user['role']);

            } else {
                return redirect()->back()->with('error', 'Wrong Password');
            }
        }

        return redirect()->back()->with('error', 'Username not found');
    }

    /**
     * Helper function para sa redirection base sa role
     */
    private function redirectBasedOnRole($role)
    {
        return redirect()->to("/$role/dashboard");
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    public function reset_admin()
    {
        $model = new UserModel();
        $newPassword = password_hash('password123', PASSWORD_DEFAULT);
        $model->where('username', 'admin')->set(['password' => $newPassword])->update();
        return "Admin password has been fixed! <br> <a href='/'>Go to Login</a>";
    }
}