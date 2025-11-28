<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-truck"></i> Tracking Pesanan #<?= $invoice['id'] ?></h4>
        <a href="<?= base_url('pesanan/detail/' . $invoice['id']) ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <!-- Timeline Tracking -->
                    <div class="tracking-timeline">
                        <!-- Status: Pending -->
                        <div class="tracking-item <?= in_array($invoice['status'], ['pending', 'diproses', 'dikemas', 'dikirim', 'selesai']) ? 'active' : '' ?>">
                            <div class="tracking-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="tracking-content">
                                <h5>Pesanan Diterima</h5>
                                <p class="text-muted mb-0">Pesanan Anda telah diterima dan menunggu konfirmasi</p>
                                <?php if ($invoice['status'] == 'pending' || in_array($invoice['status'], ['diproses', 'dikemas', 'dikirim', 'selesai'])): ?>
                                    <small class="text-success">✓ <?= date('d M Y H:i', strtotime($invoice['tgl_pesan'])) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status: Diproses -->
                        <div class="tracking-item <?= in_array($invoice['status'], ['diproses', 'dikemas', 'dikirim', 'selesai']) ? 'active' : '' ?>">
                            <div class="tracking-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="tracking-content">
                                <h5>Sedang Diproses</h5>
                                <p class="text-muted mb-0">Pesanan Anda sedang diproses oleh penjual</p>
                                <?php if (in_array($invoice['status'], ['diproses', 'dikemas', 'dikirim', 'selesai'])): ?>
                                    <small class="text-success">✓ Diproses</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status: Dikemas -->
                        <div class="tracking-item <?= in_array($invoice['status'], ['dikemas', 'dikirim', 'selesai']) ? 'active' : '' ?>">
                            <div class="tracking-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="tracking-content">
                                <h5>Sedang Dikemas</h5>
                                <p class="text-muted mb-0">Pesanan Anda sedang dikemas untuk pengiriman</p>
                                <?php if (in_array($invoice['status'], ['dikemas', 'dikirim', 'selesai'])): ?>
                                    <small class="text-success">✓ Dikemas</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status: Dikirim -->
                        <div class="tracking-item <?= in_array($invoice['status'], ['dikirim', 'selesai']) ? 'active' : '' ?>">
                            <div class="tracking-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="tracking-content">
                                <h5>Dalam Pengiriman</h5>
                                <p class="text-muted mb-0">Paket sedang dalam perjalanan</p>
                                <?php if (!empty($invoice['no_resi'])): ?>
                                    <p class="mb-0"><strong>No. Resi:</strong> 
                                        <span class="text-primary"><?= esc($invoice['no_resi']) ?></span>
                                    </p>
                                <?php endif; ?>
                                <?php if (in_array($invoice['status'], ['dikirim', 'selesai'])): ?>
                                    <small class="text-success">✓ Dikirim</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status: Selesai -->
                        <div class="tracking-item <?= ($invoice['status'] == 'selesai') ? 'active' : '' ?>">
                            <div class="tracking-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="tracking-content">
                                <h5>Pesanan Selesai</h5>
                                <p class="text-muted mb-0">Pesanan telah diterima dengan baik</p>
                                <?php if ($invoice['status'] == 'selesai'): ?>
                                    <small class="text-success">✓ <?= date('d M Y H:i', strtotime($invoice['tgl_konfirmasi'])) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status: Dibatalkan -->
                        <?php if ($invoice['status'] == 'dibatalkan'): ?>
                        <div class="tracking-item active cancelled">
                            <div class="tracking-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="tracking-content">
                                <h5>Pesanan Dibatalkan</h5>
                                <?php if (!empty($invoice['catatan_admin'])): ?>
                                    <div class="alert alert-danger mt-2">
                                        <strong>Alasan:</strong> <?= nl2br(esc($invoice['catatan_admin'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol Konfirmasi -->
                    <?php if ($invoice['status'] == 'dikirim' && $invoice['konfirmasi_user'] == 0): ?>
                        <div class="text-center mt-4">
                            <hr>
                            <p class="text-muted">Sudah menerima pesanan Anda?</p>
                            <a href="<?= base_url('pesanan/konfirmasi/' . $invoice['id']) ?>" 
                               class="btn btn-success btn-lg"
                               onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima dengan baik?')">
                                <i class="fas fa-check-circle"></i> Konfirmasi Pesanan Diterima
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Tracking Timeline CSS */
.tracking-timeline {
    position: relative;
    padding-left: 20px;
}

.tracking-timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.tracking-item {
    position: relative;
    padding-left: 60px;
    padding-bottom: 30px;
    opacity: 0.5;
}

.tracking-item.active {
    opacity: 1;
}

.tracking-item.cancelled {
    opacity: 1;
}

.tracking-icon {
    position: absolute;
    left: 15px;
    top: 0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #999;
    z-index: 1;
}

.tracking-item.active .tracking-icon {
    background: #28a745;
    color: white;
}

.tracking-item.cancelled .tracking-icon {
    background: #dc3545;
    color: white;
}

.tracking-content h5 {
    margin-bottom: 5px;
    font-weight: 600;
}

.tracking-content p {
    font-size: 14px;
}

.tracking-content small {
    font-size: 12px;
}
</style>