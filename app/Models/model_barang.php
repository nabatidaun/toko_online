<?php
namespace App\Models;
use CodeIgniter\Model;

class Model_barang extends Model{

    protected $table = 'tb_barang'; // nama tabel
    protected $primaryKey = 'id_brg'; // kalau ada primary key, isi di sini
    protected $allowedFields = ['nama_brg', 'keterangan', 'kategori', 'harga', 'stock', 'gambar']; // kolom yang bisa diakses

    public function tampil_data(){
        return $this->findAll(); // otomatis ambil dari tabel 'tb_barang'
    }

    public function tambah_barang($data)
    {
        return $this->insert($data);
    }

    public function edit_barang($id)
    {
        return $this->where('id_brg', $id)->first();
    }

    public function update_data($id, $data){
        return $this->update($id,$data);
    }

    public function detail_brg($id_brg)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_barang');
        $result = $builder->where('id_brg', $id_brg)->get();

        if ($result->getNumRows() > 0) {
            return $result->getRow(); // ambil satu baris data (objek)
        } else {
            return false;
        }
    }

}