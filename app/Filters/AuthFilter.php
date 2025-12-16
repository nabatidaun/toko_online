<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Auth Filter
 * Proteksi route yang harus login
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Cek apakah user sudah login
        if (!$session->get('logged_in')) {
            // Simpan URL tujuan untuk redirect setelah login
            $session->set('redirect_url', current_url());
            
            // Redirect ke halaman login dengan flash message
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }
        
        // Jika ada role yang diperlukan
        if ($arguments) {
            $userRole = $session->get('role');
            
            // Cek apakah user memiliki role yang sesuai
            if (!in_array($userRole, $arguments)) {
                return redirect()->to(base_url('dashboard'))
                    ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}