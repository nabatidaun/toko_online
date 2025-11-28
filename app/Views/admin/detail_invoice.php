<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Detail Pesanan 
            <span class="badge badge-lg badge-success">Invoice #<?= $invoice['id'] ?></span>
        </h4>
        <a href="<?= base_url('admin/invoice') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Info Pesanan -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>Informasi Pemesan</strong>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Nama</strong></td>
                            <td>: <?= esc($invoice['nama']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>: <?= esc($invoice['alamat']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pesan</strong></td>
                            <td>: <?= date('d M Y H:i', strtotime($invoice['tgl_pesan'])) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Batas Bayar</strong></td>
                            <td>: <?= date('d M Y H:i', strtotime($invoice['batas_bayar'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status & Tracking -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <strong>Status Pesanan</strong>
                </div>
                <div class="card-body">
                    <?php
                    $badge_class = '';
                    switch($invoice['status']) {
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
                    <h4>
                        <span class="badge <?= $badge_class ?>">
                            <?= strtoupper(esc($invoice['status'])) ?>
                        </span>
                    </h4>

                    <?php if (!empty($invoice['no_resi'])): ?>
                        <hr>
                        <p class="mb-1"><strong>No. Resi:</strong></p>
                        <p class="text-primary"><strong><?= esc($invoice['no_resi']) ?></strong></p>
                    <?php endif; ?>

                    <?php if (!empty($invoice['catatan_admin'])): ?>
                        <hr>
                        <p class="mb-1"><strong>Catatan Admin:</strong></p>
                        <p class="text-muted"><?= nl2br(esc($invoice['catatan_admin'])) ?></p>
                    <?php endif; ?>

                    <?php if ($invoice['konfirmasi_user'] == 1): ?>
                        <hr>
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i> 
                            Dikonfirmasi diterima oleh customer pada 
                            <?= date('d M Y H:i', strtotime($invoice['tgl_konfirmasi'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Info -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <strong><i class="fas fa-credit-card"></i> Informasi Pembayaran</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Metode Pembayaran:</strong><br>
                            <?php
                            $paymentType = $invoice['payment_type'] ?? 'manual';
                            if ($paymentType === 'midtrans') {
                                echo '<span class="badge badge-primary">💳 Online (Midtrans)</span>';
                            } else {
                                echo '<span class="badge badge-info">🏦 Transfer Bank Manual</span>';
                            }
                            ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Status Pembayaran:</strong><br>
                            <?php
                            $paymentStatus = $invoice['payment_status'] ?? 'pending';
                            $paymentBadge = '';
                            switch($paymentStatus) {
                                case 'paid':
                                    $paymentBadge = 'badge-success';
                                    $text = 'LUNAS';
                                    break;
                                case 'pending':
                                    $paymentBadge = 'badge-warning';
                                    $text = 'MENUNGGU';
                                    break;
                                case 'failed':
                                    $paymentBadge = 'badge-danger';
                                    $text = 'GAGAL';
                                    break;
                                case 'expired':
                                    $paymentBadge = 'badge-secondary';
                                    $text = 'EXPIRED';
                                    break;
                                default:
                                    $paymentBadge = 'badge-secondary';
                                    $text = strtoupper($paymentStatus);
                            }
                            ?>
                            <span class="badge <?= $paymentBadge ?>"><?= $text ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Jumlah Pembayaran:</strong><br>
                            <?php if (!empty($invoice['gross_amount'])): ?>
                                <strong>Rp <?= number_format($invoice['gross_amount'], 0, ',', '.') ?></strong>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Waktu Pembayaran:</strong><br>
                            <?php if (!empty($invoice['payment_time'])): ?>
                                <?= date('d M Y H:i', strtotime($invoice['payment_time'])) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($invoice['payment_method'])): ?>
                        <hr>
                        <strong>Metode yang Digunakan:</strong> <?= esc($invoice['payment_method']) ?>
                    <?php endif; ?>
                    <?php if (!empty($invoice['transaction_id'])): ?>
                        <br>
                        <strong>ID Transaksi:</strong> <code><?= esc($invoice['transaction_id']) ?></code>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Produk -->
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <strong>Detail Produk Pesanan</strong>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Sub-Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $total = 0;
                    foreach ($pesanan as $psn):
                        $subtotal = $psn['jumlah'] * $psn['harga'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($psn['nama_brg']) ?></td>
                        <td class="text-center"><?= esc($psn['jumlah']) ?></td>
                        <td class="text-right">Rp <?= number_format($psn['harga'], 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="thead-dark">
                    <tr>
                        <td colspan="4" class="text-right"><strong>GRAND TOTAL</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Form Update Status -->
    <?php if ($invoice['status'] != 'selesai' && $invoice['status'] != 'dibatalkan'): ?>
    <div class="card">
        <div class="card-header bg-warning text-white">
            <strong><i class="fas fa-sync"></i> Update Status Pesanan</strong>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/invoice/update_status/' . $invoice['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status"><strong>Status</strong></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="pending" <?= ($invoice['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="diproses" <?= ($invoice['status'] == 'diproses') ? 'selected' : '' ?>>Diproses</option>
                                <option value="dikemas" <?= ($invoice['status'] == 'dikemas') ? 'selected' : '' ?>>Dikemas</option>
                                <option value="dikirim" <?= ($invoice['status'] == 'dikirim') ? 'selected' : '' ?>>Dikirim</option>
                                <option value="selesai" <?= ($invoice['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="no_resi"><strong>No. Resi (Opsional)</strong></label>
                            <input type="text" name="no_resi" id="no_resi" class="form-control" 
                                   value="<?= esc($invoice['no_resi'] ?? '') ?>"
                                   placeholder="Masukkan nomor resi jika sudah dikirim">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="catatan_admin"><strong>Catatan Admin (Opsional)</strong></label>
                    <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="3"
                              placeholder="Tambahkan catatan untuk customer..."><?= esc($invoice['catatan_admin'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                    
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#batalkanModal">
                        <i class="fas fa-times"></i> Batalkan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Batalkan Pesanan -->
<div class="modal fade" id="batalkanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Batalkan Pesanan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/invoice/batalkan/' . $invoice['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pesanan ini?</p>
                    <div class="form-group">
                        <label>Alasan Pembatalan:</label>
                        <textarea name="alasan_batal" class="form-control" rows="3" 
                                  placeholder="Masukkan alasan pembatalan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>