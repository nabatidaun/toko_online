<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Data_barang extends BaseController{
    public function index(){

        //buat instance model
        $model = new \App\Models\Model_barang();

        $data = [
        'title' => 'Data Barang',
        'barang' => $model->tampil_data()
        ];

        //kirim ke view
        return view('templates_admin/header',$data)
         . view('templates_admin/sidebar',$data)
         . view('admin/data_barang',$data)
         . view('templates_admin/footer');
    }

    public function tambah_aksi()
    {
        $model = new \App\Models\Model_barang();

        // Ambil input form
        $nama_brg   = $this->request->getPost('nama_brg');
        $keterangan = $this->request->getPost('keterangan');        
        $kategori   = $this->request->getPost('kategori');  
        $harga      = $this->request->getPost('harga');
        $stock      = $this->request->getPost('stock');
        $gambar     = $this->request->getFile('gambar');

        // Validasi gambar (boleh hanya jpg, jpeg, png, gif)
        $validationRule = [
            'gambar' => [
                'rules' => 'uploaded[gambar]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/gif]',
                'errors' => [
                    'uploaded' => 'Pilih gambar terlebih dahulu.',
                    'is_image' => 'File yang diupload harus berupa gambar.',
                    'mime_in'  => 'Hanya format JPG, JPEG, PNG, dan GIF yang diperbolehkan.'
                ]
            ]
        ];

        if (! $this->validate($validationRule)) {
            // Jika validasi gagal, kirim pesan error
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Jika validasi lolos, proses upload
        if ($gambar->isValid() && !$gambar->hasMoved()) {
            $newName = $gambar->getRandomName();
            $gambar->move('uploads', $newName);
        } else {
            $newName = 'default.jpg';
        }

        // Simpan ke database
        $data = [
            'nama_brg'   => $nama_brg,
            'keterangan' => $keterangan,
            'kategori'   => $kategori,
            'harga'      => $harga,
            'stock'      => $stock,
            'gambar'     => $newName,
        ];

        $model->insert($data);

        // Redirect ke halaman barang (ubah sesuai rute kamu)
        return redirect()->to('/admin/data_barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id){

        $model = new \App\Models\Model_barang();
        $data['barang'] = $model->where('id_brg', $id)->first();

        return view('templates_admin/header', $data)
            . view('templates_admin/sidebar', $data)
            . view('admin/edit_barang', $data)
            . view('templates_admin/footer');
    }

    public function update(){
        $model = new \App\Models\Model_barang();

        // ambil data dari form edit
        $id = $this->request->getPost('id_brg');
        $nama_brg = $this->request->getPost('nama_brg');
        $keterangan = $this->request->getPost('keterangan');
        $kategori = $this->request->getPost('kategori');
        $harga = $this->request->getPost('harga');
        $stock = $this->request->getPost('stock');

        // susun data yang nak diupdate
        $data = [
            'nama_brg' => $nama_brg,
            'keterangan' => $keterangan,
            'kategori' => $kategori,
            'harga' => $harga,
            'stock' => $stock
        ];

        // jalankan update ke db
        $model->update($id,$data);

        //balik ke halaman data brg
        return redirect()->to(base_url('admin/data_barang'));
    }

    public function hapus($id){
        $model = new \App\Models\Model_barang();
        $model->delete($id);
        return redirect()->to(base_url('admin/data_barang'));
    }
}