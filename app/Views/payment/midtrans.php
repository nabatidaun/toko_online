<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Pembayaran Online</h5>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-cart text-primary" style="font-size: 60px;"></i>
                    </div>
                    
                    <h4 class="mb-3">Invoice #<?= $invoice['id'] ?></h4>
                    <h2 class="text-primary mb-4">
                        <strong>Total: Rp <?= number_format($total, 0, ',', '.') ?></strong>
                    </h2>

                    <div class="alert alert-info text-left">
                        <strong><i class="fas fa-info-circle"></i> Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Klik tombol "Bayar Sekarang" untuk melanjutkan ke halaman pembayaran</li>
                            <li>Pilih metode pembayaran: Kartu Kredit/Debit, Virtual Account, E-Wallet, atau QRIS</li>
                            <li>Selesaikan pembayaran dalam waktu yang ditentukan</li>
                            <li>Status pesanan akan otomatis diupdate setelah pembayaran berhasil</li>
                        </ul>
                    </div>

                    <button id="pay-button" class="btn btn-primary btn-lg btn-block mt-4">
                        <i class="fas fa-lock"></i> Bayar Sekarang
                    </button>

                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-block mt-2">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Metode Pembayaran yang Tersedia -->
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-check-circle"></i> Metode Pembayaran Tersedia</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="border p-3 rounded">
                                <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                <p class="mb-0"><small>Kartu Kredit/Debit</small></p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border p-3 rounded">
                                <i class="fas fa-university fa-2x text-info mb-2"></i>
                                <p class="mb-0"><small>Virtual Account</small></p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border p-3 rounded">
                                <i class="fas fa-mobile-alt fa-2x text-success mb-2"></i>
                                <p class="mb-0"><small>GoPay / ShopeePay</small></p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border p-3 rounded">
                                <i class="fas fa-qrcode fa-2x text-warning mb-2"></i>
                                <p class="mb-0"><small>QRIS</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS -->
<script src="https://app.<?= $environment === 'production' ? '' : 'sandbox.' ?>midtrans.com/snap/snap.js" 
        data-client-key="<?= $client_key ?>"></script>

<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        snap.pay('<?= $snap_token ?>', {
            onSuccess: function(result){
                console.log('success', result);
                window.location.href = '<?= base_url('payment/finish/' . $invoice['id']) ?>';
            },
            onPending: function(result){
                console.log('pending', result);
                window.location.href = '<?= base_url('payment/finish/' . $invoice['id']) ?>';
            },
            onError: function(result){
                console.log('error', result);
                window.location.href = '<?= base_url('payment/error/' . $invoice['id']) ?>';
            },
            onClose: function(){
                console.log('customer closed the popup without finishing the payment');
                alert('Anda menutup popup pembayaran. Silakan klik "Bayar Sekarang" lagi untuk melanjutkan.');
            }
        });
    };
</script>