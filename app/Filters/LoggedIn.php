<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoggedIn implements FilterInterface
{
    /**
     * Allow access only if user session exists
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('patient_id') && ! session()->get('user_id')) {
            // Redirect to login or show error
            return redirect()->to(site_url('login'))
                ->with('error', 'Please log in to confirm your appointment.');
        }
    }

    /**
     * We don't have anything to do here
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}