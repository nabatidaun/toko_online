<?php

/**
 * Custom Helper Functions
 * File: app/Helpers/custom_helper.php
 */

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke format Rupiah
     * 
     * @param int|float $angka
     * @return string
     */
    function format_rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Format tanggal Indonesia
     * 
     * @param string $tanggal
     * @param string $format
     * @return string
     */
    function format_tanggal($tanggal, $format = 'd M Y H:i')
    {
        return date($format, strtotime($tanggal));
    }
}

if (!function_exists('status_badge')) {
    /**
     * Generate badge HTML untuk status pesanan
     * 
     * @param string $status
     * @return string
     */
    function status_badge($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">PENDING</span>',
            'diproses' => '<span class="badge badge-info">DIPROSES</span>',
            'dikemas' => '<span class="badge badge-secondary">DIKEMAS</span>',
            'dikirim' => '<span class="badge badge-primary">DIKIRIM</span>',
            'selesai' => '<span class="badge badge-success">SELESAI</span>',
            'dibatalkan' => '<span class="badge badge-danger">DIBATALKAN</span>',
        ];
        
        return $badges[$status] ?? '<span class="badge badge-secondary">UNKNOWN</span>';
    }
}

if (!function_exists('status_color')) {
    /**
     * Get color class untuk status
     * 
     * @param string $status
     * @return string
     */
    function status_color($status)
    {
        $colors = [
            'pending' => 'warning',
            'diproses' => 'info',
            'dikemas' => 'secondary',
            'dikirim' => 'primary',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
        ];
        
        return $colors[$status] ?? 'secondary';
    }
}

if (!function_exists('status_text')) {
    /**
     * Get text Indonesia untuk status
     * 
     * @param string $status
     * @return string
     */
    function status_text($status)
    {
        $texts = [
            'pending' => 'Menunggu Konfirmasi',
            'diproses' => 'Sedang Diproses',
            'dikemas' => 'Sedang Dikemas',
            'dikirim' => 'Dalam Pengiriman',
            'selesai' => 'Pesanan Selesai',
            'dibatalkan' => 'Pesanan Dibatalkan',
        ];
        
        return $texts[$status] ?? 'Status Tidak Diketahui';
    }
}

if (!function_exists('calculate_growth')) {
    /**
     * Calculate growth percentage
     * 
     * @param float $current
     * @param float $previous
     * @return float
     */
    function calculate_growth($current, $previous)
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 2);
        }
        return $current > 0 ? 100 : 0;
    }
}

if (!function_exists('stock_status')) {
    /**
     * Get stock status text
     * 
     * @param int $stock
     * @return string
     */
    function stock_status($stock)
    {
        if ($stock == 0) {
            return '<span class="badge badge-danger">HABIS</span>';
        } elseif ($stock < 5) {
            return '<span class="badge badge-warning">MENIPIS (' . $stock . ')</span>';
        } else {
            return '<span class="badge badge-success">TERSEDIA (' . $stock . ')</span>';
        }
    }
}