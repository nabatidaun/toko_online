<?php

namespace App\Controllers;
use App\Models\Model_invoice;

class Pesanan extends BaseController
{
    protected $session;
    protected $invoiceModel;

    public function __construct()
    {
        helper(['url']);
        $this->session = session();
        $this->invoiceModel = new Model_invoice();
    }

    // Halaman daftar pesanan user
    public function index()
    {
        // NOTE: Idealnya pakai sistem login/user ID
        // Untuk sementara, tampilkan semua pesanan
        // Nanti bisa filter by user_id setelah ada sistem login
        
        $data = [
            'title' => 'Pesanan Saya',
            'invoice' => $this->invoiceModel->tampil_data()
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('pesanan/index', $data)
            . view('templates/footer');
    }

    // Detail pesanan
    public function detail($id_invoice)
    {
        $data = [
            'title' => 'Detail Pesanan',
            'invoice' => $this->invoiceModel->ambil_id_invoice($id_invoice),
            'pesanan' => $this->invoiceModel->ambil_id_pesanan($id_invoice)
        ];

        if (!$data['invoice']) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->to(base_url('pesanan'));
        }

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('pesanan/detail', $data)
            . view('templates/footer');
    }

    // Tracking pesanan
    public function tracking($id_invoice)
    {
        $data = [
            'title' => 'Tracking Pesanan',
            'invoice' => $this->invoiceModel->ambil_id_invoice($id_invoice)
        ];

        if (!$data['invoice']) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->to(base_url('pesanan'));
        }

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('pesanan/tracking', $data)
            . view('templates/footer');
    }

    // Konfirmasi penerimaan barang
    public function konfirmasi($id_invoice)
    {
        $invoice = $this->invoiceModel->ambil_id_invoice($id_invoice);
        
        if (!$invoice) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->to(base_url('pesanan'));
        }

        // Cek apakah status sudah dikirim
        if ($invoice['status'] != 'dikirim') {
            session()->setFlashdata('error', 'Pesanan belum dikirim atau sudah dikonfirmasi.');
            return redirect()->to(base_url('pesanan/detail/' . $id_invoice));
        }

        // Konfirmasi penerimaan
        $update = $this->invoiceModel->konfirmasiPenerimaan($id_invoice);

        if ($update) {
            session()->setFlashdata('success', 'Terima kasih! Pesanan telah dikonfirmasi diterima.');
        } else {
            session()->setFlashdata('error', 'Gagal konfirmasi pesanan.');
        }

        return redirect()->to(base_url('pesanan/detail/' . $id_invoice));
    }
}