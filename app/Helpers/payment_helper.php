<?php

/**
 * Payment Helper Functions
 * File: app/Helpers/payment_helper.php
 */

if (!function_exists('get_payment_type_text')) {
    /**
     * Get text untuk tipe pembayaran
     * 
     * @param string $type
     * @return string
     */
    function get_payment_type_text($type)
    {
        $types = [
            'midtrans' => '💳 Online (Midtrans)',
            'manual' => '🏦 Transfer Manual',
        ];
        
        return $types[$type] ?? $type;
    }
}

if (!function_exists('get_payment_status_badge')) {
    /**
     * Get badge HTML untuk payment status
     * 
     * @param string $status
     * @return string
     */
    function get_payment_status_badge($status)
    {
        $badges = [
            'paid' => '<span class="badge badge-success">LUNAS</span>',
            'pending' => '<span class="badge badge-warning">MENUNGGU</span>',
            'failed' => '<span class="badge badge-danger">GAGAL</span>',
            'expired' => '<span class="badge badge-secondary">EXPIRED</span>',
        ];
        
        return $badges[$status] ?? '<span class="badge badge-secondary">' . strtoupper($status) . '</span>';
    }
}

if (!function_exists('is_payment_pending')) {
    /**
     * Cek apakah pembayaran masih pending
     * 
     * @param array $invoice
     * @return bool
     */
    function is_payment_pending($invoice)
    {
        return ($invoice['payment_type'] === 'midtrans') && 
               ($invoice['payment_status'] === 'pending');
    }
}

if (!function_exists('can_retry_payment')) {
    /**
     * Cek apakah bisa retry pembayaran
     * 
     * @param array $invoice
     * @return bool
     */
    function can_retry_payment($invoice)
    {
        return ($invoice['payment_type'] === 'midtrans') && 
               in_array($invoice['payment_status'], ['pending', 'failed']);
    }
}

if (!function_exists('format_payment_method')) {
    /**
     * Format payment method dari Midtrans
     * 
     * @param string $method
     * @return string
     */
    function format_payment_method($method)
    {
        $methods = [
            'credit_card' => 'Kartu Kredit/Debit',
            'bank_transfer' => 'Transfer Bank',
            'bca_va' => 'Virtual Account BCA',
            'bni_va' => 'Virtual Account BNI',
            'bri_va' => 'Virtual Account BRI',
            'permata_va' => 'Virtual Account Permata',
            'other_va' => 'Virtual Account Lain',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'manual_transfer' => 'Transfer Manual',
        ];
        
        return $methods[$method] ?? $method;
    }
}