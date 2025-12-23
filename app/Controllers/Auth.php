<?php

namespace App\Controllers;

use App\Models\Model_user;

class Auth extends BaseController
{
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new Model_user();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
    }
    
    /**
     * Halaman Login
     */
    public function login()
    {
        // Jika sudah login, redirect berdasarkan role
        if ($this->session->get('logged_in')) {
            $role = $this->session->get('role');
            
            if (in_array($role, ['admin', 'superadmin'])) {
                return redirect()->to(base_url('admin/dashboard_admin'));
            } else {
                return redirect()->to(base_url('dashboard'));
            }
        }
        
        $data = [
            'title' => 'Login'
        ];
        
        return view('auth/login', $data);
    }
    
    /**
     * Proses Login
     */
    public function login_process()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');
        
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Email tidak valid'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Password minimal 6 karakter'
                ]
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        // Cari user berdasarkan email
        $user = $this->userModel->getUserByEmail($email);
        
        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah!');
        }
        
        // Verifikasi password
        // Support untuk MD5 (data lama) dan bcrypt (data baru)
        $isPasswordValid = false;
        
        if (strlen($user['password']) == 32) {
            // MD5 hash (32 karakter)
            $isPasswordValid = (md5($password) === $user['password']);
        } else {
            // Bcrypt hash (60 karakter)
            $isPasswordValid = password_verify($password, $user['password']);
        }
        
        if (!$isPasswordValid) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah!');
        }
        
        // Cek status user
        if ($user['status'] !== 'active') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }
        
        // Set session
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'nama' => $user['nama'],
            'role' => $user['role'],
            'logged_in' => true
        ];
        
        $this->session->set($sessionData);
        
        // Update last login
        try {
            $this->userModel->updateLastLogin($user['id']);
        } catch (\Exception $e) {
            log_message('error', 'Failed to update last login: ' . $e->getMessage());
        }
        
        // Remember me functionality
        if ($remember) {
            try {
                $token = bin2hex(random_bytes(32));
                $this->userModel->update($user['id'], ['remember_token' => $token]);
                
                // Set cookie for 30 days
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            } catch (\Exception $e) {
                log_message('error', 'Failed to set remember token: ' . $e->getMessage());
            }
        }
        
        // ===================================================
        // REDIRECT BERDASARKAN ROLE - INI YANG DIPERBAIKI
        // ===================================================
        
        // Cek apakah ada URL redirect yang tersimpan
        $redirectUrl = $this->session->get('redirect_url');
        
        if (in_array($user['role'], ['admin', 'superadmin'])) {
            // ADMIN - selalu ke admin dashboard
            $this->session->remove('redirect_url'); // Clear redirect URL
            
            return redirect()->to(base_url('admin/dashboard_admin'))
                ->with('success', 'Selamat datang Admin, ' . $user['nama'] . '!');
                
        } else {
            // USER/CUSTOMER
            if ($redirectUrl) {
                // Cek apakah redirect URL adalah halaman admin
                if (strpos($redirectUrl, '/admin') !== false) {
                    // Jika user biasa mencoba akses admin, redirect ke dashboard
                    $this->session->remove('redirect_url');
                    return redirect()->to(base_url('dashboard'))
                        ->with('error', 'Anda tidak memiliki akses ke halaman admin.');
                } else {
                    // Redirect ke URL tujuan awal
                    $this->session->remove('redirect_url');
                    return redirect()->to($redirectUrl)
                        ->with('success', 'Login berhasil!');
                }
            }
            
            // Default ke dashboard user
            return redirect()->to(base_url('dashboard'))
                ->with('success', 'Selamat datang, ' . $user['nama'] . '!');
        }
    }
    
    /**
     * Halaman Register
     */
    public function register()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }
        
        $data = [
            'title' => 'Register'
        ];
        
        return view('auth/register', $data);
    }
    
    /**
     * Proses Register
     */
    public function register_process()
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => [
                'rules' => 'required|valid_email|is_unique[tb_user.email]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Email tidak valid',
                    'is_unique' => 'Email sudah terdaftar'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Password minimal 6 karakter'
                ]
            ],
            'password_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password harus diisi',
                    'matches' => 'Password tidak cocok'
                ]
            ],
            'nama' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama harus diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        // Hash password dengan bcrypt
        $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        
        // Simpan user baru - ALWAYS as customer
        $data = [
            'email' => $this->request->getPost('email'),
            'password' => $password,
            'nama' => $this->request->getPost('nama'),
            'no_telp' => $this->request->getPost('no_telp') ?? null,
            'alamat' => $this->request->getPost('alamat') ?? null,
            'role' => 'customer', // FORCE customer role
            'status' => 'active'
        ];
        
        try {
            if ($this->userModel->insert($data)) {
                return redirect()->to(base_url('auth/login'))
                    ->with('success', 'Registrasi berhasil! Silakan login.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Registrasi gagal. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
        }
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        // Hapus remember token jika ada
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Simpan pesan sebelum destroy session
        $this->session->setFlashdata('success', 'Anda telah logout.');
        
        // Destroy session
        $this->session->destroy();
        
        return redirect()->to(base_url('auth/login'));
    }
    
    /**
     * Check Auth (AJAX)
     */
    public function check_auth()
    {
        $isLoggedIn = $this->session->get('logged_in') ? true : false;
        
        return $this->response->setJSON([
            'logged_in' => $isLoggedIn,
            'user' => [
                'nama' => $this->session->get('nama'),
                'email' => $this->session->get('email'),
                'role' => $this->session->get('role')
            ]
        ]);
    }
    
    /**
     * Forgot Password
     */
    public function forgot_password()
    {
        $data = [
            'title' => 'Lupa Password'
        ];
        
        return view('auth/forgot_password', $data);
    }
    
    /**
     * Reset Password Process
     */
    public function reset_password()
    {
        $email = $this->request->getPost('email');
        
        // Validasi
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Email tidak valid'
                ]
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        // Cek apakah email terdaftar
        $user = $this->userModel->getUserByEmail($email);
        
        if (!$user) {
            return redirect()->back()
                ->with('error', 'Email tidak terdaftar.');
        }
        
        // Generate token reset
        $token = bin2hex(random_bytes(32));
        
        // Simpan token ke session (dalam implementasi lengkap, simpan ke database dengan expiry time)
        $this->session->set('reset_token', $token);
        $this->session->set('reset_email', $email);
        
        // TODO: Kirim email reset password
        // Untuk demo, tampilkan pesan sukses
        return redirect()->back()
            ->with('success', 'Link reset password telah dikirim ke email Anda. (Demo mode: cek console)');
    }
}