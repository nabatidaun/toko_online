<?php

namespace App\Models;
use CodeIgniter\Model;

class Model_invoice extends Model
{
    protected $table = 'tb_invoice';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama', 
        'alamat', 
        'tgl_pesan', 
        'batas_bayar', 
        'status',
        'no_resi',
        'catatan_admin',
        'konfirmasi_user',
        'tgl_konfirmasi',
        'payment_type',
        'payment_status',
        'snap_token',
        'transaction_id',
        'payment_method',
        'payment_time',
        'gross_amount'
    ];

    public function simpanInvoice()
    {
        date_default_timezone_set('Asia/Jakarta');

        // Ambil data keranjang dari session
        $cart = session()->get('cart') ?? [];

        if (empty($cart)) {
            return false;
        }

        // Ambil payment method dari session
        $payment_method = session()->get('payment_method') ?? 'manual';

        // Data untuk tabel tb_invoice
        $invoice = [
            'nama'        => session()->get('nama'),
            'alamat'      => session()->get('alamat'),
            'tgl_pesan'   => date('Y-m-d H:i:s'),
            'batas_bayar' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'status'      => 'pending',
            'payment_type' => $payment_method,
            'payment_status' => 'pending'
        ];

        // Simpan ke tabel tb_invoice
        $this->insert($invoice);
        $id_invoice = $this->getInsertID();

        // Simpan detail pesanan ke tabel tb_pesanan
        $db = \Config\Database::connect();
        foreach ($cart as $item) {
            $data = [
                'id_invoice' => $id_invoice,
                'id_brg'     => $item['id'],
                'nama_brg'   => $item['nama'],
                'jumlah'     => $item['qty'],
                'harga'      => $item['harga']
            ];
            $db->table('tb_pesanan')->insert($data);
        }

        return true;
    }

    public function tampil_data()
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    public function ambil_id_invoice($id_invoice)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_invoice');
        $result = $builder->where('id', $id_invoice)->get()->getRowArray();

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function ambil_id_pesanan($id_invoice)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_pesanan');
        $result = $builder->where('id_invoice', $id_invoice)->get();

        if ($result->getNumRows() > 0) {
            return $result->getResultArray();
        } else {
            return false;
        }
    }

    // FUNGSI: Update status pesanan
    public function updateStatus($id_invoice, $data)
    {
        return $this->update($id_invoice, $data);
    }

    // FUNGSI: Get pesanan by status
    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('tgl_pesan', 'DESC')
                    ->findAll();
    }

    // FUNGSI: Hitung jumlah pesanan per status
    public function countByStatus()
    {
        $db = \Config\Database::connect();
        
        $statuses = ['pending', 'diproses', 'dikemas', 'dikirim', 'selesai', 'dibatalkan'];
        $result = [];
        
        foreach ($statuses as $status) {
            $count = $db->table('tb_invoice')
                ->where('status', $status)
                ->countAllResults();
            $result[$status] = $count;
        }
        
        return $result;
    }

    // FUNGSI: Total invoice
    public function getTotalInvoices()
    {
        return $this->countAllResults();
    }

    // FUNGSI: Konfirmasi penerimaan dari user
    public function konfirmasiPenerimaan($id_invoice)
    {
        date_default_timezone_set('Asia/Jakarta');
        
        $data = [
            'status' => 'selesai',
            'konfirmasi_user' => 1,
            'tgl_konfirmasi' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id_invoice, $data);
    }
}