<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $model = new \App\Models\UserModel();

        $username = $this->request->getVar('username');
        $password = (string)$this->request->getVar('password'); // Siguraduhing string

        $user = $model->where('username', $username)->first();

        if ($user) {
            
            if (password_verify($password, $user['password'])) {
                $session->set([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'isLoggedIn' => true
                ]);
                return redirect()->to('/dashboard');
            } else {
                return redirect()->back()->with('error', 'Wrong Password');
            }
        }

        return redirect()->back()->with('error', 'Username not found');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    public function reset_admin()
    {
        $model = new \App\Models\UserModel();

        $newPassword = password_hash('password123', PASSWORD_DEFAULT);

        $model->where('username', 'admin')->set(['password' => $newPassword])->update();

        return "Admin password has been fixed! Ang haba na ng hash ngayon ay: " . strlen($newPassword) . " characters. <br> <a href='/'>Go to Login</a>";
    }
}
