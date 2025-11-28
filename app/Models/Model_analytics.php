<?php

namespace App\Models;
use CodeIgniter\Model;

class Model_analytics extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // Total pendapatan keseluruhan
    public function getTotalRevenue()
    {
        $query = $this->db->query("SELECT SUM(harga * jumlah) as total FROM tb_pesanan");
        $result = $query->getRowArray();
        return $result['total'] ?? 0;
    }

    // Pendapatan bulan ini
    public function getMonthlyRevenue()
    {
        $month = date('m');
        $year = date('Y');
        
        $query = $this->db->query("
            SELECT SUM(p.harga * p.jumlah) as total 
            FROM tb_pesanan p
            JOIN tb_invoice i ON p.id_invoice = i.id
            WHERE MONTH(i.tgl_pesan) = ? 
            AND YEAR(i.tgl_pesan) = ?
            AND i.status != 'dibatalkan'
        ", [$month, $year]);
        
        $result = $query->getRowArray();
        return $result['total'] ?? 0;
    }

    // Pendapatan hari ini
    public function getTodayRevenue()
    {
        $today = date('Y-m-d');
        
        $query = $this->db->query("
            SELECT SUM(p.harga * p.jumlah) as total 
            FROM tb_pesanan p
            JOIN tb_invoice i ON p.id_invoice = i.id
            WHERE DATE(i.tgl_pesan) = ?
            AND i.status != 'dibatalkan'
        ", [$today]);
        
        $result = $query->getRowArray();
        return $result['total'] ?? 0;
    }

    // Total pesanan
    public function getTotalOrders()
    {
        return $this->db->table('tb_invoice')->countAllResults();
    }

    // Pesanan bulan ini
    public function getMonthlyOrders()
    {
        $month = date('m');
        $year = date('Y');
        
        $builder = $this->db->table('tb_invoice');
        $builder->where('MONTH(tgl_pesan)', $month);
        $builder->where('YEAR(tgl_pesan)', $year);
        return $builder->countAllResults();
    }

    // Pesanan hari ini
    public function getTodayOrders()
    {
        $today = date('Y-m-d');
        
        $builder = $this->db->table('tb_invoice');
        $builder->where('DATE(tgl_pesan)', $today);
        return $builder->countAllResults();
    }

    // Jumlah produk
    public function getTotalProducts()
    {
        return $this->db->table('tb_barang')->countAllResults();
    }

    // Produk stok menipis (< 5)
    public function getLowStockProducts()
    {
        $builder = $this->db->table('tb_barang');
        $builder->where('stock <', 5);
        $builder->where('stock >', 0);
        return $builder->countAllResults();
    }

    // Produk habis
    public function getOutOfStockProducts()
    {
        $builder = $this->db->table('tb_barang');
        $builder->where('stock', 0);
        return $builder->countAllResults();
    }

    // Produk terlaris (Top 5)
    public function getTopProducts($limit = 5)
    {
        $query = $this->db->query("
            SELECT 
                nama_brg,
                SUM(jumlah) as total_terjual,
                SUM(harga * jumlah) as total_pendapatan
            FROM tb_pesanan
            GROUP BY nama_brg
            ORDER BY total_terjual DESC
            LIMIT ?
        ", [$limit]);
        
        return $query->getResultArray();
    }

    // Data penjualan 7 hari terakhir (untuk chart)
    public function getLast7DaysSales()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            $query = $this->db->query("
                SELECT SUM(p.harga * p.jumlah) as total
                FROM tb_pesanan p
                JOIN tb_invoice i ON p.id_invoice = i.id
                WHERE DATE(i.tgl_pesan) = ?
                AND i.status != 'dibatalkan'
            ", [$date]);
            
            $result = $query->getRowArray();
            
            $data[] = [
                'date' => date('d M', strtotime($date)),
                'total' => $result['total'] ?? 0
            ];
        }
        
        return $data;
    }

    // Penjualan per bulan (12 bulan terakhir)
    public function getMonthlySales()
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('m', strtotime("-$i months"));
            $year = date('Y', strtotime("-$i months"));
            
            $query = $this->db->query("
                SELECT SUM(p.harga * p.jumlah) as total
                FROM tb_pesanan p
                JOIN tb_invoice i ON p.id_invoice = i.id
                WHERE MONTH(i.tgl_pesan) = ?
                AND YEAR(i.tgl_pesan) = ?
                AND i.status != 'dibatalkan'
            ", [$month, $year]);
            
            $result = $query->getRowArray();
            
            $data[] = [
                'month' => date('M Y', strtotime("$year-$month-01")),
                'total' => $result['total'] ?? 0
            ];
        }
        
        return $data;
    }

    // Kategori terlaris
    public function getCategorySales()
    {
        $query = $this->db->query("
            SELECT 
                b.kategori,
                COUNT(p.id) as total_pesanan,
                SUM(p.jumlah) as total_qty,
                SUM(p.harga * p.jumlah) as total_pendapatan
            FROM tb_pesanan p
            JOIN tb_barang b ON p.id_brg = b.id_brg
            GROUP BY b.kategori
            ORDER BY total_pendapatan DESC
        ");
        
        return $query->getResultArray();
    }

    // Persentase perubahan bulan ini vs bulan lalu
    public function getMonthlyGrowth()
    {
        // Revenue bulan ini
        $thisMonth = $this->getMonthlyRevenue();
        
        // Revenue bulan lalu
        $lastMonth = date('m', strtotime('-1 month'));
        $lastYear = date('Y', strtotime('-1 month'));
        
        $query = $this->db->query("
            SELECT SUM(p.harga * p.jumlah) as total
            FROM tb_pesanan p
            JOIN tb_invoice i ON p.id_invoice = i.id
            WHERE MONTH(i.tgl_pesan) = ?
            AND YEAR(i.tgl_pesan) = ?
            AND i.status != 'dibatalkan'
        ", [$lastMonth, $lastYear]);
        
        $result = $query->getRowArray();
        $lastMonthRevenue = $result['total'] ?? 0;
        
        if ($lastMonthRevenue > 0) {
            $growth = (($thisMonth - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } else {
            $growth = $thisMonth > 0 ? 100 : 0;
        }
        
        return round($growth, 2);
    }
}