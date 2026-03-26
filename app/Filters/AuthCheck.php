<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'You must be logged in.');
        }

        if (!empty($arguments)) {
            $allowedRoles = is_array($arguments) ? $arguments : explode(',', $arguments);
            $userRole = $session->get('role');

            if (!in_array($userRole, $allowedRoles)) {
                // Kunin ang kasalukuyang path para maiwasan ang loop
                $currentPath = $request->getUri()->getPath();
                $targetPath = $userRole . '/dashboard';

                if ($currentPath !== $targetPath) {
                    return redirect()->to('/' . $targetPath)->with('error', 'Access denied.');
                }
            }
        }
    }


    public function after(RequestInterface $request, $response, $arguments = null)
    {
        // nothing
    }
}
