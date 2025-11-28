<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Model_invoice;

class Invoice extends BaseController
{
    public function index()
    {
        $model = new Model_invoice();
        
        // Filter berdasarkan status jika ada
        $status = $this->request->getGet('status');
        
        if ($status && $status != 'semua') {
            $data['invoice'] = $model->getByStatus($status);
        } else {
            $data['invoice'] = $model->tampil_data();
        }
        
        // Hitung jumlah per status untuk badge
        $data['count_status'] = $model->countByStatus();
        $data['filter_status'] = $status ?? 'semua';

        return view('templates_admin/header')
            . view('templates_admin/sidebar')
            . view('admin/invoice', $data)
            . view('templates_admin/footer');
    }

    public function detail($id_invoice)
    {
        $model = new Model_invoice();
        $data['invoice'] = $model->ambil_id_invoice($id_invoice);
        $data['pesanan'] = $model->ambil_id_pesanan($id_invoice);

        return view('templates_admin/header')
            . view('templates_admin/sidebar')
            . view('admin/detail_invoice', $data)
            . view('templates_admin/footer');
    }

    // Update status pesanan
    public function update_status($id_invoice)
    {
        $model = new Model_invoice();
        
        $status = $this->request->getPost('status');
        $no_resi = $this->request->getPost('no_resi');
        $catatan_admin = $this->request->getPost('catatan_admin');
        
        $data = [
            'status' => $status
        ];
        
        // Tambahkan no resi jika status dikirim
        if ($status == 'dikirim' && !empty($no_resi)) {
            $data['no_resi'] = $no_resi;
        }
        
        // Tambahkan catatan admin jika ada
        if (!empty($catatan_admin)) {
            $data['catatan_admin'] = $catatan_admin;
        }
        
        $update = $model->updateStatus($id_invoice, $data);
        
        if ($update) {
            session()->setFlashdata('success', 'Status pesanan berhasil diupdate!');
        } else {
            session()->setFlashdata('error', 'Gagal update status pesanan.');
        }
        
        return redirect()->to(base_url('admin/invoice/detail/' . $id_invoice));
    }

    // Batalkan pesanan
    public function batalkan($id_invoice)
    {
        $model = new Model_invoice();
        
        $data = [
            'status' => 'dibatalkan',
            'catatan_admin' => $this->request->getPost('alasan_batal') ?? 'Dibatalkan oleh admin'
        ];
        
        $update = $model->updateStatus($id_invoice, $data);
        
        if ($update) {
            session()->setFlashdata('success', 'Pesanan berhasil dibatalkan.');
        } else {
            session()->setFlashdata('error', 'Gagal membatalkan pesanan.');
        }
        
        return redirect()->to(base_url('admin/invoice'));
    }
}