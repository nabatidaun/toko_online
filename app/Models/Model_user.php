<?php

namespace App\Models;

use CodeIgniter\Model;

class Model_user extends Model
{
    protected $table = 'tb_user';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email',
        'password',
        'nama',
        'role',
        'no_telp',
        'alamat',
        'status',
        'remember_token',
        'foto_profile', // Tambahan untuk foto
        'last_login'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[tb_user.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'nama' => 'required|min_length[3]|max_length[100]',
        'role' => 'required|in_list[customer,admin,superadmin]',
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Email tidak valid',
            'is_unique' => 'Email sudah terdaftar'
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'nama' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter'
        ]
    ];

    /**
     * Cari user by email
     */
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Cari user by remember token
     */
    public function getUserByToken($token)
    {
        return $this->where('remember_token', $token)->first();
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user dengan alamat lengkap
     */
    public function getUserWithAddresses($userId)
    {
        return $this->find($userId);
    }
    
    /**
     * Get user profile dengan foto
     */
    public function getUserProfile($userId)
    {
        $user = $this->find($userId);
        
        if ($user && empty($user['foto_profile'])) {
            $user['foto_profile'] = 'default.png';
        }
        
        return $user;
    }
}