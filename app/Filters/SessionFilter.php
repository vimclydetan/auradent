<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SessionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Redirect if no patient_id or user_id in session
        if (! session()->get('patient_id') && ! session()->get('user_id')) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Please log in to continue.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}