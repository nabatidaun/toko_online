<?php

namespace App\Controllers;

use App\Models\Model_invoice;
use Config\Midtrans as MidtransConfig;

class Payment extends BaseController
{
    protected $session;
    protected $invoiceModel;
    protected $midtransConfig;

    public function __construct()
    {
        helper(['url']);
        $this->session = session();
        $this->invoiceModel = new Model_invoice();
        $this->midtransConfig = new MidtransConfig();
        
        // Set Midtrans Configuration
        \Midtrans\Config::$serverKey = $this->midtransConfig->serverKey;
        \Midtrans\Config::$isProduction = ($this->midtransConfig->environment === 'production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = $this->midtransConfig->is3ds;
    }

    // Proses pembayaran dengan Midtrans
    public function process($id_invoice)
    {
        // Ambil data invoice
        $invoice = $this->invoiceModel->find($id_invoice);
        
        if (!$invoice) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Invoice tidak ditemukan');
        }

        // Ambil detail pesanan
        $pesanan = $this->invoiceModel->ambil_id_pesanan($id_invoice);
        
        // Hitung total
        $total = 0;
        $item_details = [];
        
        foreach ($pesanan as $item) {
            $subtotal = $item['harga'] * $item['jumlah'];
            $total += $subtotal;
            
            $item_details[] = [
                'id' => $item['id_brg'],
                'price' => (int)$item['harga'],
                'quantity' => (int)$item['jumlah'],
                'name' => $item['nama_brg']
            ];
        }

        // Transaction details
        $transaction_details = [
            'order_id' => 'ORDER-' . $id_invoice . '-' . time(),
            'gross_amount' => (int)$total,
        ];

        // Customer details
        $customer_details = [
            'first_name' => $invoice['nama'],
            'email' => 'customer@example.com', // Bisa ditambah field email di form
            'phone' => '08123456789', // Bisa ditambah field phone di form
            'shipping_address' => [
                'address' => $invoice['alamat'],
            ]
        ];

        // Transaction data
        $transaction = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
            'enabled_payments' => [
                'credit_card', 
                'bca_va', 
                'bni_va', 
                'bri_va', 
                'permata_va',
                'other_va',
                'gopay',
                'shopeepay',
                'qris'
            ],
            'callbacks' => [
                'finish' => $this->midtransConfig->finishUrl . '/' . $id_invoice,
                'unfinish' => $this->midtransConfig->unfinishUrl . '/' . $id_invoice,
                'error' => $this->midtransConfig->errorUrl . '/' . $id_invoice,
            ]
        ];

        try {
            // Get Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($transaction);
            
            // Update invoice dengan snap token
            $this->invoiceModel->update($id_invoice, [
                'snap_token' => $snapToken,
                'transaction_id' => $transaction_details['order_id'],
                'payment_type' => 'midtrans',
                'payment_status' => 'pending',
                'gross_amount' => $total
            ]);
            
            // Kirim ke view payment
            $data = [
                'title' => 'Pembayaran',
                'snap_token' => $snapToken,
                'invoice' => $invoice,
                'total' => $total,
                'client_key' => $this->midtransConfig->clientKey,
                'environment' => $this->midtransConfig->environment
            ];

            return view('templates/header', $data)
                . view('templates/sidebar')
                . view('payment/midtrans', $data)
                . view('templates/footer');
                
        } catch (\Exception $e) {
            log_message('error', 'Midtrans Error: ' . $e->getMessage());
            return redirect()->to(base_url('dashboard/pembayaran'))
                ->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    // Callback dari Midtrans (Notification)
    public function notification()
    {
        try {
            // Log incoming request
            log_message('info', '=== MIDTRANS NOTIFICATION START ===');
            log_message('info', 'POST Data: ' . json_encode($_POST));
            log_message('info', 'RAW Input: ' . file_get_contents('php://input'));
            
            $notification = new \Midtrans\Notification();
            
            log_message('info', 'Notification Object: ' . json_encode($notification));
            
            $transaction_status = $notification->transaction_status;
            $fraud_status = $notification->fraud_status;
            $order_id = $notification->order_id;
            $payment_type = $notification->payment_type;
            
            log_message('info', 'Transaction Status: ' . $transaction_status);
            log_message('info', 'Order ID: ' . $order_id);
            
            // Ambil invoice ID dari order_id
            $order_parts = explode('-', $order_id);
            $invoice_id = isset($order_parts[1]) ? (int)$order_parts[1] : 0;
            
            log_message('info', 'Invoice ID: ' . $invoice_id);
            
            if ($invoice_id == 0) {
                log_message('error', 'Invalid order ID: ' . $order_id);
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid order ID']);
            }

            $invoice = $this->invoiceModel->find($invoice_id);
            
            if (!$invoice) {
                log_message('error', 'Invoice not found: ' . $invoice_id);
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invoice not found']);
            }

            // Update data berdasarkan status
            $update_data = [
                'payment_method' => $payment_type,
                'payment_time' => date('Y-m-d H:i:s')
            ];

            if ($transaction_status == 'capture') {
                if ($fraud_status == 'accept') {
                    $update_data['payment_status'] = 'paid';
                    $update_data['status'] = 'diproses';
                }
            } elseif ($transaction_status == 'settlement') {
                $update_data['payment_status'] = 'paid';
                $update_data['status'] = 'diproses';
            } elseif ($transaction_status == 'pending') {
                $update_data['payment_status'] = 'pending';
            } elseif ($transaction_status == 'deny' || $transaction_status == 'cancel' || $transaction_status == 'expire') {
                $update_data['payment_status'] = 'failed';
            }

            log_message('info', 'Update Data: ' . json_encode($update_data));
            
            $result = $this->invoiceModel->update($invoice_id, $update_data);
            
            log_message('info', 'Update Result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            log_message('info', '=== MIDTRANS NOTIFICATION END ===');
            
            return $this->response->setJSON(['status' => 'success']);
            
        } catch (\Exception $e) {
            log_message('error', 'Notification Error: ' . $e->getMessage());
            log_message('error', 'Stack Trace: ' . $e->getTraceAsString());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Halaman finish setelah pembayaran
    public function finish($id_invoice)
    {
        $invoice = $this->invoiceModel->find($id_invoice);
        
        $data = [
            'title' => 'Pembayaran Selesai',
            'invoice' => $invoice
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('payment/finish', $data)
            . view('templates/footer');
    }

    // Halaman unfinish
    public function unfinish($id_invoice)
    {
        $data = [
            'title' => 'Pembayaran Belum Selesai',
            'invoice_id' => $id_invoice
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('payment/unfinish', $data)
            . view('templates/footer');
    }

    // Halaman error
    public function error($id_invoice)
    {
        $data = [
            'title' => 'Pembayaran Error',
            'invoice_id' => $id_invoice
        ];

        return view('templates/header', $data)
            . view('templates/sidebar')
            . view('payment/error', $data)
            . view('templates/footer');
    }

    // Cek status pembayaran
    public function check_status($id_invoice)
    {
        $invoice = $this->invoiceModel->find($id_invoice);
        
        if (!$invoice || !$invoice['transaction_id']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaction not found'
            ]);
        }

        try {
            $status = \Midtrans\Transaction::status($invoice['transaction_id']);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $status
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function sync_status($invoice_id)
    {
        $invoice = $this->invoiceModel->find($invoice_id);
        
        if (!$invoice) {
            session()->setFlashdata('error', 'Invoice tidak ditemukan');
            return redirect()->to(base_url('pesanan'));
        }
        
        if ($invoice['payment_type'] !== 'midtrans') {
            session()->setFlashdata('error', 'Bukan pembayaran Midtrans');
            return redirect()->to(base_url('pesanan/detail/' . $invoice_id));
        }
        
        if (empty($invoice['transaction_id'])) {
            session()->setFlashdata('error', 'Transaction ID tidak ada');
            return redirect()->to(base_url('pesanan/detail/' . $invoice_id));
        }
        
        try {
            // Get status dari Midtrans
            $status = \Midtrans\Transaction::status($invoice['transaction_id']);
            
            // Convert to array
            $statusData = json_decode(json_encode($status), true);
            
            log_message('info', '=== SYNC STATUS START ===');
            log_message('info', 'Invoice ID: ' . $invoice_id);
            log_message('info', 'Transaction ID: ' . $invoice['transaction_id']);
            log_message('info', 'Midtrans Response: ' . json_encode($statusData));
            
            $transaction_status = $statusData['transaction_status'] ?? 'unknown';
            $payment_type = $statusData['payment_type'] ?? 'unknown';
            $settlement_time = $statusData['settlement_time'] ?? date('Y-m-d H:i:s');
            $fraud_status = $statusData['fraud_status'] ?? 'accept';
            
            $update_data = [];
            $message = '';
            $alert_type = 'info';
            
            // Handle different transaction statuses
            if ($transaction_status == 'settlement') {
                $update_data = [
                    'payment_status' => 'paid',
                    'payment_time' => $settlement_time,
                    'status' => 'diproses',
                    'payment_method' => $payment_type
                ];
                $message = '✅ Pembayaran BERHASIL! Pesanan Anda sedang diproses.';
                $alert_type = 'success';
                
            } elseif ($transaction_status == 'capture') {
                if ($fraud_status == 'accept') {
                    $update_data = [
                        'payment_status' => 'paid',
                        'payment_time' => $settlement_time,
                        'status' => 'diproses',
                        'payment_method' => $payment_type
                    ];
                    $message = '✅ Pembayaran BERHASIL! Pesanan Anda sedang diproses.';
                    $alert_type = 'success';
                } else {
                    $update_data = [
                        'payment_status' => 'pending'
                    ];
                    $message = '⏳ Pembayaran sedang dalam review.';
                    $alert_type = 'warning';
                }
                
            } elseif ($transaction_status == 'pending') {
                $message = '⏳ Pembayaran masih pending. Silakan selesaikan pembayaran Anda.';
                $alert_type = 'info';
                
            } elseif ($transaction_status == 'deny') {
                $update_data = [
                    'payment_status' => 'failed'
                ];
                $message = '❌ Pembayaran ditolak. Silakan coba metode pembayaran lain.';
                $alert_type = 'danger';
                
            } elseif ($transaction_status == 'expire') {
                $update_data = [
                    'payment_status' => 'expired',
                    'status' => 'dibatalkan'
                ];
                $message = '⚠️ Pembayaran expired. Silakan buat pesanan baru.';
                $alert_type = 'warning';
                
            } elseif ($transaction_status == 'cancel') {
                $update_data = [
                    'payment_status' => 'failed',
                    'status' => 'dibatalkan'
                ];
                $message = '❌ Pembayaran dibatalkan.';
                $alert_type = 'danger';
                
            } else {
                $message = '❓ Status pembayaran: ' . strtoupper($transaction_status);
                $alert_type = 'info';
            }
            
            // Update database jika ada perubahan
            if (!empty($update_data)) {
                $this->invoiceModel->update($invoice_id, $update_data);
                log_message('info', 'Database updated: ' . json_encode($update_data));
            }
            
            log_message('info', 'Message: ' . $message);
            log_message('info', '=== SYNC STATUS END ===');
            
            session()->setFlashdata($alert_type, $message);
            
        } catch (\Exception $e) {
            log_message('error', 'Sync Status Error: ' . $e->getMessage());
            log_message('error', 'Stack Trace: ' . $e->getTraceAsString());
            
            // Check if it's Midtrans API error (transaction not found)
            if (strpos($e->getMessage(), 'API error') !== false || 
                strpos($e->getMessage(), "doesn't exist") !== false ||
                strpos($e->getMessage(), '404') !== false) {
                session()->setFlashdata('error', '❌ Transaction tidak ditemukan di Midtrans. Mungkin sudah expired atau belum dibuat.');
            } else {
                session()->setFlashdata('error', '❌ Terjadi kesalahan: ' . $e->getMessage());
            }
        }
        
        return redirect()->to(base_url('pesanan/detail/' . $invoice_id));
    }

    public function sync_all_pending()
    {
        $pendingInvoices = $this->invoiceModel
            ->where('payment_type', 'midtrans')
            ->where('payment_status', 'pending')
            ->whereNotNull('transaction_id')
            ->findAll();
        
        $synced = 0;
        $errors = 0;
        
        foreach ($pendingInvoices as $invoice) {
            try {
                $status = \Midtrans\Transaction::status($invoice['transaction_id']);
                
                // Convert to array
                $statusData = json_decode(json_encode($status), true);
                
                $transaction_status = $statusData['transaction_status'] ?? 'unknown';
                
                if ($transaction_status == 'settlement' || $transaction_status == 'capture') {
                    $update_data = [
                        'payment_status' => 'paid',
                        'payment_time' => $statusData['settlement_time'] ?? date('Y-m-d H:i:s'),
                        'status' => 'diproses',
                        'payment_method' => $statusData['payment_type'] ?? 'unknown'
                    ];
                    
                    $this->invoiceModel->update($invoice['id'], $update_data);
                    $synced++;
                    
                } elseif ($transaction_status == 'expire') {
                    $this->invoiceModel->update($invoice['id'], [
                        'payment_status' => 'expired',
                        'status' => 'dibatalkan'
                    ]);
                    $synced++;
                }
                
            } catch (\Exception $e) {
                log_message('error', 'Bulk Sync Error for Invoice #' . $invoice['id'] . ': ' . $e->getMessage());
                $errors++;
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'synced' => $synced,
            'errors' => $errors,
            'total' => count($pendingInvoices)
        ]);
    }

}