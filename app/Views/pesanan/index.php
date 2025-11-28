<div class="container-fluid">
    <h4><i class="fas fa-shopping-bag"></i> Pesanan Saya</h4>
    <p class="text-muted">Lihat status dan tracking pesanan Anda</p>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($invoice)): ?>
        <div class="row">
            <?php foreach ($invoice as $inv): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Invoice #<?= esc($inv['id']) ?></strong>
                            <?php
                            $badge_class = '';
                            switch($inv['status']) {
                                case 'pending':
                                    $badge_class = 'badge-warning';
                                    break;
                                case 'diproses':
                                    $badge_class = 'badge-info';
                                    break;
                                case 'dikemas':
                                    $badge_class = 'badge-secondary';
                                    break;
                                case 'dikirim':
                                    $badge_class = 'badge-primary';
                                    break;
                                case 'selesai':
                                    $badge_class = 'badge-success';
                                    break;
                                case 'dibatalkan':
                                    $badge_class = 'badge-danger';
                                    break;
                            }
                            ?>
                            <span class="badge <?= $badge_class ?>">
                                <?= strtoupper(esc($inv['status'])) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Nama:</strong> <?= esc($inv['nama']) ?></p>
                            <p class="mb-1"><strong>Alamat:</strong> <?= esc($inv['alamat']) ?></p>
                            <p class="mb-1"><strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($inv['tgl_pesan'])) ?></p>
                            
                            <?php if (!empty($inv['no_resi'])): ?>
                                <p class="mb-1"><strong>No. Resi:</strong> 
                                    <span class="text-primary"><?= esc($inv['no_resi']) ?></span>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($inv['catatan_admin'])): ?>
                                <div class="alert alert-info mt-2 mb-2">
                                    <small><strong>Catatan:</strong> <?= esc($inv['catatan_admin']) ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= base_url('pesanan/detail/' . $inv['id']) ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="<?= base_url('pesanan/tracking/' . $inv['id']) ?>" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-truck"></i> Tracking
                            </a>
                            <?php if ($inv['status'] == 'dikirim' && $inv['konfirmasi_user'] == 0): ?>
                                <a href="<?= base_url('pesanan/konfirmasi/' . $inv['id']) ?>" 
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima?')">
                                    <i class="fas fa-check"></i> Terima
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            Anda belum memiliki pesanan. 
            <a href="<?= base_url('dashboard') ?>">Mulai belanja sekarang</a>
        </div>
    <?php endif; ?>
</div>