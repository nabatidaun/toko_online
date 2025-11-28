<?php 

namespace App\Controllers;

use App\Models\Model_barang;

class Dashboard extends BaseController
{
    protected $session;
    protected $barangModel;

    public function __construct()
    {
        helper(['url']);
        $this->session = session();
        $this->barangModel = new Model_barang();
    } 

    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'barang' => $this->barangModel->tampil_data(),
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('dashboard', $data)
            . view('templates/footer');
    }

    public function tambah_ke_keranjang($id)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->to(base_url('dashboard'));
        }

        $cart = $this->session->get('cart') ?? [];

        if (isset($cart[$id])) {
            $cart[$id]['qty']++;
        } else {
            $cart[$id] = [
                'id' => $barang['id_brg'],
                'nama' => $barang['nama_brg'],
                'harga' => $barang['harga'],
                'qty' => 1
            ];
        }

        $this->session->set('cart', $cart);
        return redirect()->to(base_url('dashboard'));
    }

    public function keranjang()
    {
        $data = [
            'title' => 'Keranjang Belanja',
            'cart' => $this->session->get('cart') ?? [],
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('keranjang', $data)
            . view('templates/footer');
    }

    public function hapus_item($id)
    {
        $cart = $this->session->get('cart') ?? [];
        unset($cart[$id]);
        $this->session->set('cart', $cart);
        return redirect()->to(base_url('dashboard/keranjang'));
    }

    public function hapus_semua()
    {
        session()->remove('cart');
        return redirect()->to('/dashboard/keranjang')->with('success', 'Keranjang berhasil dikosongkan.');
    }

    public function detail_keranjang()
    {
        $cart = $this->session->get('cart') ?? [];
        $data = [
            'title' => 'Detail Keranjang',
            'cart' => $cart
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('keranjang', $data)
            . view('templates/footer');
    }

    public function pembayaran()
    {
        $data = ['title' => 'Pembayaran'];
        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('pembayaran')
            . view('templates/footer');
    }

    public function proses_pesanan()
    {
        try {
            // STEP 1: Validasi form
            $nama = $this->request->getPost('nama');
            $alamat = $this->request->getPost('alamat');
            $no_telp = $this->request->getPost('no_telp');
            $jasa = $this->request->getPost('jasa');
            $payment_method = $this->request->getPost('payment_method');

            // Debug log
            log_message('info', '=== PROSES PESANAN START ===');
            log_message('info', 'Nama: ' . $nama);
            log_message('info', 'Alamat: ' . $alamat);
            log_message('info', 'No Telp: ' . $no_telp);
            log_message('info', 'Jasa: ' . $jasa);
            log_message('info', 'Payment Method: ' . $payment_method);

            // STEP 2: Validasi input
            if (empty($nama) || empty($alamat) || empty($no_telp) || empty($jasa) || empty($payment_method)) {
                log_message('error', 'Form validation failed: empty fields');
                return redirect()->back()->withInput()->with('error', 'Semua field harus diisi!');
            }

            // STEP 3: Cek keranjang
            $cart = session()->get('cart');
            if (empty($cart)) {
                log_message('error', 'Cart kosong');
                return redirect()->to(base_url('dashboard/keranjang'))
                    ->with('error', 'Keranjang belanja Anda kosong!');
            }

            // STEP 4: Simpan ke session
            session()->set('nama', $nama);
            session()->set('alamat', $alamat);
            session()->set('payment_method', $payment_method);

            log_message('info', 'Session data saved');

            // STEP 5: Buat invoice
            $invoiceModel = new \App\Models\Model_invoice();
            $proses = $invoiceModel->simpanInvoice();

            if (!$proses) {
                log_message('error', 'Failed to create invoice');
                return redirect()->to(base_url('dashboard/pembayaran'))
                    ->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
            }

            $id_invoice = $invoiceModel->getInsertID();
            log_message('info', 'Invoice created: ' . $id_invoice);

            // STEP 6: Hapus keranjang
            session()->remove('cart');

            // STEP 7: Redirect berdasarkan payment method
            if ($payment_method === 'midtrans') {
                log_message('info', 'Redirecting to payment/process/' . $id_invoice);
                return redirect()->to(base_url('payment/process/' . $id_invoice));
            } else {
                log_message('info', 'Manual payment - showing success page');
                $data = ['title' => 'Proses Pesanan'];
                return view('templates/header', $data)
                    . view('templates/sidebar')
                    . view('proses_pesanan')
                    . view('templates/footer');
            }

        } catch (\Exception $e) {
            log_message('error', 'Exception in proses_pesanan: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->to(base_url('dashboard/pembayaran'))
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function detail($id_brg)
    {
        $data = [
            'title' => 'Detail Produk',
            'barang' => $this->barangModel->detail_brg($id_brg)
        ];

        if (!$data['barang']) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Produk tidak ditemukan');
        }

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('detail_barang', $data)
            . view('templates/footer');
    }

    public function poster()
    {
        $kategoriModel = new \App\Models\Model_kategori();
        $data = [
            'title' => 'Poster',
            'poster' => $kategoriModel->data_poster()
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('poster', $data)
            . view('templates/footer');
    }

    public function pamflet()
    {
        $kategoriModel = new \App\Models\Model_kategori();
        $data = [
            'title' => 'Pamflet',
            'pamflet' => $kategoriModel->data_pamflet()
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('pamflet', $data)
            . view('templates/footer');
    }
}