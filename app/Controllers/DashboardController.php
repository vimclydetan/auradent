<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $data['title'] = "Dashboard";

        if ($session->get('role') == 'admin') {
            return view('admin/dashboard', $data);
        } else if ($session->get('role') == 'receptionist') {
            return view('receptionist/dashboard', $data);
        } else if ($session->get('role') == 'dentist') {
            return view('dentist/dashboard', $data);
        } else {
            return view('patient/dashboard', $data);
        }
    }
}
