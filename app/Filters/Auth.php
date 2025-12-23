<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        // Cek apakah user sudah login
        if (!$session->get('logged_in')) {
            // Simpan URL tujuan untuk redirect setelah login
            $session->set('redirect_url', current_url());
            
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Jika ada argument (role yang diizinkan)
        if (!empty($arguments)) {
            $userRole = $session->get('role');
            
            // Cek apakah role user ada dalam daftar yang diizinkan
            if (!in_array($userRole, $arguments)) {
                // Redirect berdasarkan role user
                if (in_array($userRole, ['admin', 'superadmin'])) {
                    return redirect()->to(base_url('admin/dashboard_admin'))
                        ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                } else {
                    return redirect()->to(base_url('dashboard'))
                        ->with('error', 'Anda tidak memiliki akses ke halaman admin.');
                }
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}