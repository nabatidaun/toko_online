<?php

namespace App\Controllers;

use App\Models\Model_barang;
use App\Models\Model_invoice;

class Dashboard extends BaseController
{
    protected $session;
    protected $barangModel;

    public function __construct()
    {
        $this->session = session();
        $this->barangModel = new Model_barang();
        
        // Load cart library
        helper(['cart', 'form', 'url']);
    }

    /**
     * Homepage - Bisa diakses tanpa login
     */
    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['barang'] = $this->barangModel->findAll();
        
        // Cek login status untuk tampilan berbeda
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        $data['user_name'] = $this->session->get('nama');
        
        // Hitung total keranjang
        $cart = \Config\Services::cart();
        $data['total_item'] = count($cart->contents());
        
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('dashboard', $data);
        echo view('templates/footer');
    }

    /**
     * Detail Produk - Bisa diakses tanpa login
     */
    public function detail($id_brg)
    {
        $data['title'] = 'Detail Produk';
        $data['barang'] = $this->barangModel->find($id_brg);
        
        if (!$data['barang']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan');
        }

        // Cek login status
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        
        // Get related products (sama kategori)
        $data['related_products'] = $this->barangModel
            ->where('kategori', $data['barang']['kategori'])
            ->where('id_brg !=', $id_brg)
            ->limit(4)
            ->findAll();
        
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('detail_barang', $data);
        echo view('templates/footer');
    }

    /**
     * Tambah ke Keranjang - HARUS LOGIN
     */
    public function tambah_ke_keranjang($id)
    {
        // ============================================
        // CEK LOGIN - FITUR UTAMA AUTH
        // ============================================
        if (!$this->session->get('logged_in')) {
            // Simpan URL tujuan untuk redirect setelah login
            $this->session->set('redirect_url', current_url());
            
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang.');
        }

        // Get product data
        $barang = $this->barangModel->find($id);

        if (!$barang) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan!');
        }

        // Check stock
        if ($barang['stock'] <= 0) {
            return redirect()->back()->with('error', 'Stok produk habis!');
        }

        $cart = \Config\Services::cart();
        
        // Check if item already in cart
        $existing = false;
        foreach ($cart->contents() as $item) {
            if ($item['id'] == $id) {
                $existing = true;
                $newQty = $item['qty'] + 1;
                
                // Check if new quantity exceeds stock
                if ($newQty > $barang['stock']) {
                    return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia!');
                }
                
                $cart->update([
                    'rowid' => $item['rowid'],
                    'qty' => $newQty
                ]);
                break;
            }
        }

        // If not exists, insert new item
        if (!$existing) {
            $data = [
                'id'      => $barang['id_brg'],
                'qty'     => 1,
                'price'   => $barang['harga'],
                'name'    => $barang['nama_brg'],
                'options' => [
                    'gambar' => $barang['gambar'],
                    'kategori' => $barang['kategori'],
                    'stock' => $barang['stock']
                ]
            ];

            $cart->insert($data);
        }

        return redirect()->to(base_url('dashboard/keranjang'))
            ->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Lihat Keranjang - HARUS LOGIN
     */
    public function keranjang()
    {
        // CEK LOGIN
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login untuk melihat keranjang.');
        }

        $cart = \Config\Services::cart();
        
        $data['title'] = 'Keranjang Belanja';
        $data['cart_items'] = $cart->contents();
        $data['total'] = $cart->total();
        $data['total_items'] = count($cart->contents());

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('keranjang', $data);
        echo view('templates/footer');
    }

    /**
     * Hapus Item dari Keranjang
     */
    public function hapus_item($rowid)
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $cart = \Config\Services::cart();
        $cart->remove($rowid);

        return redirect()->to(base_url('dashboard/keranjang'))
            ->with('success', 'Item berhasil dihapus dari keranjang!');
    }

    /**
     * Hapus Semua Item dari Keranjang
     */
    public function hapus_semua()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $cart = \Config\Services::cart();
        $cart->destroy();

        return redirect()->to(base_url('dashboard/keranjang'))
            ->with('success', 'Keranjang berhasil dikosongkan!');
    }

    /**
     * Update Quantity via AJAX
     */
    public function update_cart()
    {
        if (!$this->session->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $rowid = $this->request->getPost('rowid');
        $qty = $this->request->getPost('qty');

        if ($qty < 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jumlah minimal 1']);
        }

        $cart = \Config\Services::cart();
        
        // Get item to check stock
        $item = null;
        foreach ($cart->contents() as $cartItem) {
            if ($cartItem['rowid'] == $rowid) {
                $item = $cartItem;
                break;
            }
        }

        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Item tidak ditemukan']);
        }

        // Check stock
        $barang = $this->barangModel->find($item['id']);
        if ($qty > $barang['stock']) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Stok tidak mencukupi! Stok tersedia: ' . $barang['stock']
            ]);
        }

        // Update cart
        $cart->update([
            'rowid' => $rowid,
            'qty' => $qty
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'subtotal' => number_format($item['price'] * $qty, 0, ',', '.'),
            'total' => number_format($cart->total(), 0, ',', '.')
        ]);
    }

    /**
     * Halaman Pembayaran/Checkout - HARUS LOGIN
     */
    public function pembayaran()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login untuk checkout.');
        }

        $cart = \Config\Services::cart();
        
        // Cek keranjang kosong
        if (count($cart->contents()) == 0) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Keranjang belanja Anda kosong!');
        }

        // Validasi stock sebelum checkout
        foreach ($cart->contents() as $item) {
            $barang = $this->barangModel->find($item['id']);
            if (!$barang || $barang['stock'] < $item['qty']) {
                return redirect()->to(base_url('dashboard/keranjang'))
                    ->with('error', 'Stok produk "' . $item['name'] . '" tidak mencukupi!');
            }
        }

        $data['title'] = 'Pembayaran';
        $data['cart_items'] = $cart->contents();
        $data['total'] = $cart->total();
        
        // Get user data untuk auto-fill
        $data['user'] = [
            'nama' => $this->session->get('nama'),
            'email' => $this->session->get('email')
        ];

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('pembayaran', $data);
        echo view('templates/footer');
    }

    /**
     * Search Products
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');
        $kategori = $this->request->getGet('kategori');
        
        $builder = $this->barangModel;
        
        if ($keyword) {
            $builder = $builder->like('nama_brg', $keyword)
                              ->orLike('keterangan', $keyword);
        }
        
        if ($kategori) {
            $builder = $builder->where('kategori', $kategori);
        }
        
        $data['title'] = 'Hasil Pencarian';
        $data['keyword'] = $keyword;
        $data['barang'] = $builder->findAll();
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('dashboard', $data);
        echo view('templates/footer');
    }

    /**
     * Filter by Category
     */
    public function poster()
    {
        $data['title'] = 'Poster';
        $data['barang'] = $this->barangModel->where('kategori', 'poster')->findAll();
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('dashboard', $data);
        echo view('templates/footer');
    }

    public function pamflet()
    {
        $data['title'] = 'Pamflet';
        $data['barang'] = $this->barangModel->where('kategori', 'pamflet')->findAll();
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('dashboard', $data);
        echo view('templates/footer');
    }

    /**
     * Get Cart Count (AJAX)
     */
    public function cart_count()
    {
        $cart = \Config\Services::cart();
        return $this->response->setJSON([
            'count' => count($cart->contents())
        ]);
    }

    /**
     * Proses Pesanan - Create Invoice
     */
    public function proses_pesanan()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $cart = \Config\Services::cart();
        
        if (count($cart->contents()) == 0) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Keranjang kosong!');
        }

        // Get form data
        $nama = $this->request->getPost('nama');
        $alamat = $this->request->getPost('alamat');

        // Create invoice
        $invoiceModel = new Model_invoice();
        
        $invoiceData = [
            'nama' => $nama,
            'alamat' => $alamat,
            'tgl_pesan' => date('Y-m-d H:i:s'),
            'batas_bayar' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'status' => 'pending',
            'payment_type' => 'midtrans',
            'payment_status' => 'pending',
            'gross_amount' => $cart->total()
        ];

        $invoiceId = $invoiceModel->insert($invoiceData);

        if ($invoiceId) {
            // Insert order items
            $pesananModel = new \App\Models\Model_pesanan();
            
            foreach ($cart->contents() as $item) {
                $pesananModel->insert([
                    'id_invoice' => $invoiceId,
                    'id_brg' => $item['id'],
                    'nama_brg' => $item['name'],
                    'jumlah' => $item['qty'],
                    'harga' => $item['price'],
                    'pilihan' => ''
                ]);
            }

            // Clear cart
            $cart->destroy();

            // Redirect to payment
            return redirect()->to(base_url('payment/process/' . $invoiceId))
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        }

        return redirect()->back()->with('error', 'Gagal membuat pesanan!');
    }
}