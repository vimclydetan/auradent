<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;

class TimeController extends BaseController
{
    public function getTime()
    {
        $now = Time::now('Asia/Manila');

        return $this->response->setJSON([
            'time' => $now->toDateTimeString()
        ]);
    }
}
