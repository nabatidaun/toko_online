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
        $this->session = \Config\Services::session();
        $this->barangModel = new Model_barang();
        
        helper(['form', 'url']);
    }
    
    /**
     * Get cart data from session
     */
    private function getCart()
    {
        return $this->session->get('cart') ?? [];
    }
    
    /**
     * Save cart to session
     */
    private function saveCart($cart)
    {
        $this->session->set('cart', $cart);
    }
    
    /**
     * Calculate cart total
     */
    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['harga'] * $item['qty']);
        }
        return $total;
    }

    /**
     * Homepage - Bisa diakses tanpa login
     */
    public function index()
    {
        $cart = $this->getCart();
        
        $data['title'] = 'Dashboard';
        $data['barang'] = $this->barangModel->findAll();
        
        // Cek login status untuk tampilan berbeda
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        $data['user_name'] = $this->session->get('nama');
        
        // Hitung total keranjang
        $data['total_item'] = count($cart);
        
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
        
        // Get reviews
        $reviewModel = new \App\Models\Model_review();
        $data['reviews'] = $reviewModel->getReviewsByProduct($id_brg);
        $data['rating_summary'] = $reviewModel->getAverageRating($id_brg);
        $data['rating_distribution'] = $reviewModel->getRatingDistribution($id_brg);
        
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
        // CEK LOGIN
        if (!$this->session->get('logged_in')) {
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

        // Get cart from session
        $cart = $this->getCart();
        
        // Check if item already in cart
        $existing = false;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $id) {
                $existing = true;
                $newQty = $item['qty'] + 1;
                
                // Check if new quantity exceeds stock
                if ($newQty > $barang['stock']) {
                    return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia!');
                }
                
                $cart[$key]['qty'] = $newQty;
                break;
            }
        }

        // If not exists, add new item
        if (!$existing) {
            $cart[] = [
                'id' => $barang['id_brg'],
                'nama' => $barang['nama_brg'],
                'qty' => 1,
                'harga' => $barang['harga'],
                'gambar' => $barang['gambar'],
                'kategori' => $barang['kategori'],
                'stock' => $barang['stock']
            ];
        }
        
        // Save cart to session
        $this->saveCart($cart);

        return redirect()->to(base_url('dashboard/keranjang'))
            ->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Lihat Keranjang - HARUS LOGIN
     * INI YANG DIPERBAIKI - Return VIEW bukan JSON
     */
    public function keranjang()
    {
        // CEK LOGIN
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login untuk melihat keranjang.');
        }

        $cart = $this->getCart();
        
        $data = [
            'title' => 'Keranjang Belanja',
            'cart' => $cart,
            'total' => $this->calculateTotal($cart),
            'total_items' => count($cart)
        ];

        // RETURN VIEW - bukan JSON
        return view('templates/header', $data)
            . view('templates/sidebar', $data)
            . view('keranjang', $data)
            . view('templates/footer');
    }

    /**
     * Hapus Item dari Keranjang
     */
    public function hapus_item($id)
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $cart = $this->getCart();
        
        // Hapus item berdasarkan ID
        foreach ($cart as $key => $item) {
            if ($item['id'] == $id) {
                unset($cart[$key]);
                break;
            }
        }
        
        // Re-index array
        $cart = array_values($cart);
        
        $this->saveCart($cart);

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

        $this->session->remove('cart');

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

        $id = $this->request->getPost('id');
        $qty = $this->request->getPost('qty');

        if ($qty < 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jumlah minimal 1']);
        }

        $cart = $this->getCart();
        
        // Update item quantity
        foreach ($cart as $key => $item) {
            if ($item['id'] == $id) {
                // Check stock
                $barang = $this->barangModel->find($id);
                if ($qty > $barang['stock']) {
                    return $this->response->setJSON([
                        'status' => 'error', 
                        'message' => 'Stok tidak mencukupi! Stok tersedia: ' . $barang['stock']
                    ]);
                }
                
                $cart[$key]['qty'] = $qty;
                break;
            }
        }
        
        $this->saveCart($cart);

        return $this->response->setJSON([
            'status' => 'success',
            'total' => number_format($this->calculateTotal($cart), 0, ',', '.')
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

        $cart = $this->getCart();
        
        // Cek keranjang kosong
        if (count($cart) == 0) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Keranjang belanja Anda kosong!');
        }

        // Validasi stock sebelum checkout
        foreach ($cart as $item) {
            $barang = $this->barangModel->find($item['id']);
            if (!$barang || $barang['stock'] < $item['qty']) {
                return redirect()->to(base_url('dashboard/keranjang'))
                    ->with('error', 'Stok produk "' . $item['nama'] . '" tidak mencukupi!');
            }
        }

        $data['title'] = 'Pembayaran';
        $data['cart_items'] = $cart;
        $data['total'] = $this->calculateTotal($cart);
        
        // Get user data untuk auto-fill
        $data['user'] = [
            'nama' => $this->session->get('nama'),
            'email' => $this->session->get('email')
        ];

        return view('templates/header', $data)
            . view('templates/sidebar', $data)
            . view('pembayaran', $data)
            . view('templates/footer');
    }

    /**
     * Proses Pesanan - Create Invoice
     */
    public function proses_pesanan()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $cart = $this->getCart();
        
        if (count($cart) == 0) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Keranjang kosong!');
        }

        // Get form data
        $nama = $this->request->getPost('nama');
        $alamat = $this->request->getPost('alamat');
        $payment_method = $this->request->getPost('payment_method') ?? 'midtrans';

        // Create invoice
        $invoiceModel = new Model_invoice();
        
        $total = $this->calculateTotal($cart);
        
        $invoiceData = [
            'nama' => $nama,
            'alamat' => $alamat,
            'tgl_pesan' => date('Y-m-d H:i:s'),
            'batas_bayar' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'status' => 'pending',
            'payment_type' => $payment_method,
            'payment_status' => 'pending',
            'gross_amount' => $total
        ];

        $invoiceId = $invoiceModel->insert($invoiceData);

        if ($invoiceId) {
            // Insert order items
            $pesananModel = new \App\Models\Model_pesanan();
            
            foreach ($cart as $item) {
                $pesananModel->insert([
                    'id_invoice' => $invoiceId,
                    'id_brg' => $item['id'],
                    'nama_brg' => $item['nama'],
                    'jumlah' => $item['qty'],
                    'harga' => $item['harga'],
                    'pilihan' => ''
                ]);
            }

            // Clear cart
            $this->session->remove('cart');

            // Redirect to payment
            return redirect()->to(base_url('payment/process/' . $invoiceId))
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        }

        return redirect()->back()->with('error', 'Gagal membuat pesanan!');
    }
    
    /**
     * Detail Keranjang untuk AJAX - Return JSON
     */
    public function detail_keranjang()
    {
        $cart = $this->getCart();
        return $this->response->setJSON([
            'items' => $cart,
            'total' => $this->calculateTotal($cart),
            'count' => count($cart)
        ]);
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
        
        return view('templates/header', $data)
            . view('templates/sidebar', $data)
            . view('dashboard', $data)
            . view('templates/footer');
    }

    /**
     * Filter by Category - Poster
     */
    public function poster()
    {
        $data['title'] = 'Poster';
        $data['barang'] = $this->barangModel->where('kategori', 'poster')->findAll();
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        
        return view('templates/header', $data)
            . view('templates/sidebar', $data)
            . view('dashboard', $data)
            . view('templates/footer');
    }

    /**
     * Filter by Category - Pamflet
     */
    public function pamflet()
    {
        $data['title'] = 'Pamflet';
        $data['barang'] = $this->barangModel->where('kategori', 'pamflet')->findAll();
        $data['is_logged_in'] = $this->session->get('logged_in') ? true : false;
        
        return view('templates/header', $data)
            . view('templates/sidebar', $data)
            . view('dashboard', $data)
            . view('templates/footer');
    }

    /**
     * Get Cart Count (AJAX)
     */
    public function cart_count()
    {
        return $this->response->setJSON([
            'count' => count($this->getCart())
        ]);
    }
}