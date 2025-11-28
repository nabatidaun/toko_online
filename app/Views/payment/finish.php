<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Pembayaran Berhasil!</h2>
                    <p class="lead mb-4">
                        Terima kasih telah melakukan pembayaran.<br>
                        Invoice #<?= $invoice['id'] ?? '-' ?>
                    </p>

                    <?php if (isset($invoice['payment_status']) && $invoice['payment_status'] === 'paid'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Status: Pembayaran Berhasil</strong><br>
                            Pesanan Anda sedang diproses oleh admin.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i>
                            <strong>Status: Menunggu Konfirmasi</strong><br>
                            Pembayaran Anda sedang diverifikasi. Mohon tunggu beberapa saat.
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <div class="btn-group" role="group">
                        <a href="<?= base_url('pesanan/detail/' . ($invoice['id'] ?? '')) ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-file-invoice"></i> Lihat Detail Pesanan
                        </a>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-lg">
                            <i class="fas fa-home"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>