<?php

namespace App\Models;

use CodeIgniter\Model;

class Model_pesanan extends Model
{
    protected $table = 'tb_pesanan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_invoice',
        'id_brg',
        'nama_brg',
        'jumlah',
        'harga',
        'pilihan'
    ];
    protected $useTimestamps = false;
    
    /**
     * Get pesanan by invoice ID
     */
    public function getPesananByInvoice($id_invoice)
    {
        return $this->where('id_invoice', $id_invoice)->findAll();
    }
    
    /**
     * Get pesanan dengan detail barang
     */
    public function getPesananWithBarang($id_invoice)
    {
        return $this->select('tb_pesanan.*, tb_barang.gambar, tb_barang.kategori')
                    ->join('tb_barang', 'tb_barang.id_brg = tb_pesanan.id_brg', 'left')
                    ->where('tb_pesanan.id_invoice', $id_invoice)
                    ->findAll();
    }
    
    /**
     * Simpan pesanan baru
     */
    public function simpanPesanan($data)
    {
        return $this->insert($data);
    }
    
    /**
     * Simpan multiple pesanan sekaligus
     */
    public function simpanBatchPesanan($dataArray)
    {
        // Gunakan parent insertBatch() dengan benar
        return parent::insertBatch($dataArray);
    }
    
    /**
     * Get total revenue
     */
    public function getTotalRevenue()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $result = $builder->selectSum('harga * jumlah', 'total')->get()->getRowArray();
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Get best selling products
     */
    public function getBestSellingProducts($limit = 10)
    {
        return $this->select('nama_brg, SUM(jumlah) as total_terjual, SUM(harga * jumlah) as total_pendapatan')
                    ->groupBy('nama_brg')
                    ->orderBy('total_terjual', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get pesanan by product ID
     */
    public function getPesananByProduct($id_brg)
    {
        return $this->where('id_brg', $id_brg)->findAll();
    }
    
    /**
     * Count total products sold
     */
    public function countTotalProductsSold()
    {
        $result = $this->selectSum('jumlah', 'total')->first();
        return $result['total'] ?? 0;
    }
    
    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $builder->select('SUM(harga * jumlah) as total');
        $builder->join('tb_invoice', 'tb_invoice.id = tb_pesanan.id_invoice');
        $builder->where('DATE(tb_invoice.tgl_pesan) >=', $startDate);
        $builder->where('DATE(tb_invoice.tgl_pesan) <=', $endDate);
        $builder->where('tb_invoice.status !=', 'dibatalkan');
        
        $result = $builder->get()->getRowArray();
        return $result['total'] ?? 0;
    }
    
    /**
     * Get pesanan summary by invoice
     */
    public function getSummaryByInvoice($id_invoice)
    {
        $pesanan = $this->where('id_invoice', $id_invoice)->findAll();
        
        $summary = [
            'total_items' => count($pesanan),
            'total_qty' => 0,
            'subtotal' => 0
        ];
        
        foreach ($pesanan as $item) {
            $summary['total_qty'] += $item['jumlah'];
            $summary['subtotal'] += ($item['harga'] * $item['jumlah']);
        }
        
        return $summary;
    }
    
    /**
     * Delete pesanan by invoice
     */
    public function deletePesananByInvoice($id_invoice)
    {
        return $this->where('id_invoice', $id_invoice)->delete();
    }
}