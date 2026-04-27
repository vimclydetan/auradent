<?php

namespace App\Controllers;
use App\Config\Clinic;

class Home extends BaseController
{
    public function index(): string
    {
        $clinic = config('Clinic');

        $data['clinic'] = $clinic;

        return view('index', $data);
    }
}
