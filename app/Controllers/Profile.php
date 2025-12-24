<?php

namespace App\Controllers;

use App\Models\Model_user;

class Profile extends BaseController
{
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new Model_user();
        $this->session = \Config\Services::session();
        helper(['form', 'url', 'filesystem']);
    }
    
    /**
     * Halaman Profile
     */
    public function index()
    {
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Ambil data user
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Data user tidak ditemukan.');
        }
        
        // Cek folder upload
        $uploadPath = FCPATH . 'uploads/profiles/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        $data = [
            'title' => 'Profile Saya',
            'user' => $user
        ];
        
        return view('templates/header', $data)
            . view('templates/sidebar', $data)
            . view('profile/index', $data)
            . view('templates/footer');
    }
    
    /**
     * Update Profile
     */
    public function update()
    {
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }
        
        $userId = $this->session->get('user_id');
        
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama harus diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[tb_user.email,id,' . $userId . ']',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Email tidak valid',
                    'is_unique' => 'Email sudah digunakan'
                ]
            ],
            'no_telp' => [
                'rules' => 'permit_empty|numeric|min_length[10]|max_length[15]',
                'errors' => [
                    'numeric' => 'No. telepon harus berupa angka',
                    'min_length' => 'No. telepon minimal 10 digit',
                    'max_length' => 'No. telepon maksimal 15 digit'
                ]
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        // Data yang akan diupdate
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'no_telp' => $this->request->getPost('no_telp'),
            'alamat' => $this->request->getPost('alamat')
        ];
        
        // Handle upload foto
        $foto = $this->request->getFile('foto_profile');
        
        // Debug info
        log_message('info', 'Upload attempt - File name: ' . ($foto ? $foto->getName() : 'NULL'));
        log_message('info', 'Upload attempt - Is valid: ' . ($foto && $foto->isValid() ? 'YES' : 'NO'));
        log_message('info', 'Upload attempt - Has moved: ' . ($foto && $foto->hasMoved() ? 'YES' : 'NO'));
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            
            // Cek apakah benar-benar ada file
            if ($foto->getSize() == 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'File foto tidak valid atau kosong.');
            }
            
            // Validasi tipe file
            $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
            $fileType = $foto->getMimeType();
            
            if (!in_array($fileType, $allowedTypes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Format foto harus JPG, JPEG, atau PNG. Anda upload: ' . $fileType);
            }
            
            // Validasi ukuran (max 2MB)
            $maxSize = 2048 * 1024; // 2MB in bytes
            if ($foto->getSize() > $maxSize) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ukuran foto maksimal 2MB. Ukuran file Anda: ' . number_format($foto->getSize() / 1024, 2) . ' KB');
            }
            
            // Pastikan folder upload ada
            $uploadPath = FCPATH . 'uploads/profiles/';
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0777, true)) {
                    log_message('error', 'Failed to create upload directory: ' . $uploadPath);
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal membuat folder upload. Hubungi administrator.');
                }
            }
            
            // Cek permission folder
            if (!is_writable($uploadPath)) {
                log_message('error', 'Upload directory not writable: ' . $uploadPath);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Folder upload tidak memiliki permission write. Hubungi administrator.');
            }
            
            try {
                // Hapus foto lama jika ada
                $user = $this->userModel->find($userId);
                if (!empty($user['foto_profile']) && $user['foto_profile'] != 'default.png') {
                    $oldPath = $uploadPath . $user['foto_profile'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                        log_message('info', 'Old photo deleted: ' . $oldPath);
                    }
                }
                
                // Upload foto baru
                $newName = 'profile_' . $userId . '_' . time() . '.' . $foto->getExtension();
                
                if ($foto->move($uploadPath, $newName)) {
                    $data['foto_profile'] = $newName;
                    log_message('info', 'Photo uploaded successfully: ' . $newName);
                } else {
                    log_message('error', 'Failed to move uploaded file');
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal memindahkan file upload. Error: ' . $foto->getErrorString());
                }
                
            } catch (\Exception $e) {
                log_message('error', 'Photo upload exception: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat upload foto: ' . $e->getMessage());
            }
        }
        
        // Update database
        try {
            $this->userModel->update($userId, $data);
            
            // Update session
            $this->session->set('nama', $data['nama']);
            $this->session->set('email', $data['email']);
            
            return redirect()->to(base_url('profile'))
                ->with('success', 'Profile berhasil diupdate!');
                
        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal update profile. Silakan coba lagi.');
        }
    }
    
    /**
     * Change Password
     */
    public function change_password()
    {
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }
        
        $userId = $this->session->get('user_id');
        
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password lama harus diisi'
                ]
            ],
            'new_password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password baru harus diisi',
                    'min_length' => 'Password minimal 6 karakter'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Konfirmasi password harus diisi',
                    'matches' => 'Konfirmasi password tidak cocok'
                ]
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->with('errors', $validation->getErrors());
        }
        
        // Ambil data user
        $user = $this->userModel->find($userId);
        
        // Verifikasi password lama
        $currentPassword = $this->request->getPost('current_password');
        $isPasswordValid = false;
        
        if (strlen($user['password']) == 32) {
            // MD5
            $isPasswordValid = (md5($currentPassword) === $user['password']);
        } else {
            // Bcrypt
            $isPasswordValid = password_verify($currentPassword, $user['password']);
        }
        
        if (!$isPasswordValid) {
            return redirect()->back()
                ->with('error', 'Password lama tidak sesuai!');
        }
        
        // Hash password baru
        $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_BCRYPT);
        
        // Update password
        try {
            $this->userModel->update($userId, ['password' => $newPassword]);
            
            return redirect()->to(base_url('profile'))
                ->with('success', 'Password berhasil diubah!');
                
        } catch (\Exception $e) {
            log_message('error', 'Change password error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengubah password. Silakan coba lagi.');
        }
    }
    
    /**
     * Delete Profile Picture
     */
    public function delete_photo()
    {
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }
        
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);
        
        // Hapus foto jika ada
        if (!empty($user['foto_profile']) && $user['foto_profile'] != 'default.png') {
            $photoPath = FCPATH . 'uploads/profiles/' . $user['foto_profile'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        
        // Set ke default
        $this->userModel->update($userId, ['foto_profile' => 'default.png']);
        
        return redirect()->to(base_url('profile'))
            ->with('success', 'Foto profile berhasil dihapus!');
    }
    
    /**
     * Test Upload (Debug Only)
     */
    public function test_upload()
    {
        $uploadPath = FCPATH . 'uploads/profiles/';
        
        $info = [
            'upload_path' => $uploadPath,
            'path_exists' => is_dir($uploadPath) ? 'YES' : 'NO',
            'path_writable' => is_writable($uploadPath) ? 'YES' : 'NO',
            'php_upload_max' => ini_get('upload_max_filesize'),
            'php_post_max' => ini_get('post_max_size'),
            'fcpath' => FCPATH
        ];
        
        return $this->response->setJSON($info);
    }
}