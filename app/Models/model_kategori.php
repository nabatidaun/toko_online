<?php
namespace App\Models;
use CodeIgniter\Model;

class Model_kategori extends Model{
    protected $table = 'tb_barang'; // nama tabel
    protected $primaryKey = 'id_brg'; // kalau ada primary key, isi di sini
    protected $allowedFields = ['nama_brg', 'keterangan', 'kategori', 'harga', 'stock', 'gambar']; // kolom yang bisa diakses

    public function data_poster()
    {
        return $this->db->table($this->table)
                        ->where('kategori', 'poster')
                        ->get()
                        ->getResultArray(); // atau getResult() kalau mau object
    }

    public function data_pamflet()
    {
        return $this->db->table($this->table)
                        ->where('kategori', 'pamflet')
                        ->get()
                        ->getResultArray(); // atau getResult() kalau mau object
    }
}