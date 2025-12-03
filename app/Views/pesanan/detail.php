<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Detail Pesanan #<?= $invoice['id'] ?></h4>
        <a href="<?= base_url('pesanan') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Info Pesanan -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>Informasi Pesanan</strong>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Nama Pemesan</strong></td>
                            <td>: <?= esc($invoice['nama']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Alamat Pengiriman</strong></td>
                            <td>: <?= esc($invoice['alamat']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pesan</strong></td>
                            <td>: <?= date('d M Y H:i', strtotime($invoice['tgl_pesan'])) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Batas Pembayaran</strong></td>
                            <td>: <?= date('d M Y H:i', strtotime($invoice['batas_bayar'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Pembayaran -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <strong><i class="fas fa-credit-card"></i> Informasi Pembayaran</strong>
                </div>
                <div class="card-body">
                    <?php
                    $paymentType = $invoice['payment_type'] ?? 'manual';
                    $paymentStatus = $invoice['payment_status'] ?? 'pending';
                    ?>
                    
                    <!-- Payment Method -->
                    <p class="mb-1"><strong>Metode Pembayaran:</strong></p>
                    <?php if ($paymentType === 'midtrans'): ?>
                        <p class="mb-3">
                            <span class="badge badge-primary">💳 Online (Midtrans)</span>
                        </p>
                    <?php else: ?>
                        <p class="mb-3">
                            <span class="badge badge-info">🏦 Transfer Bank Manual</span>
                        </p>
                    <?php endif; ?>

                    <!-- Payment Status -->
                    <p class="mb-1"><strong>Status Pembayaran:</strong></p>
                    <?php
                    $paymentBadge = 'badge-secondary';
                    $paymentText = 'Menunggu';
                    $paymentIcon = '⏳';
                    
                    switch($paymentStatus) {
                        case 'paid':
                            $paymentBadge = 'badge-success';
                            $paymentText = 'LUNAS';
                            $paymentIcon = '✅';
                            break;
                        case 'pending':
                            $paymentBadge = 'badge-warning';
                            $paymentText = 'MENUNGGU PEMBAYARAN';
                            $paymentIcon = '⏳';
                            break;
                        case 'failed':
                            $paymentBadge = 'badge-danger';
                            $paymentText = 'PEMBAYARAN GAGAL';
                            $paymentIcon = '❌';
                            break;
                        case 'expired':
                            $paymentBadge = 'badge-secondary';
                            $paymentText = 'TRANSAKSI EXPIRED';
                            $paymentIcon = '⚠️';
                            break;
                    }
                    ?>
                    <p class="mb-3">
                        <span class="badge <?= $paymentBadge ?> p-2" style="font-size: 14px;">
                            <?= $paymentIcon ?> <?= $paymentText ?>
                        </span>
                    </p>

                    <!-- Payment Amount -->
                    <?php if (!empty($invoice['gross_amount'])): ?>
                        <p class="mb-1"><strong>Jumlah Pembayaran:</strong></p>
                        <p class="mb-3">
                            <strong class="text-primary" style="font-size: 18px;">
                                Rp <?= number_format($invoice['gross_amount'], 0, ',', '.') ?>
                            </strong>
                        </p>
                    <?php endif; ?>

                    <!-- Payment Time -->
                    <?php if (!empty($invoice['payment_time'])): ?>
                        <p class="mb-1"><strong>Waktu Pembayaran:</strong></p>
                        <p class="mb-3"><?= date('d M Y H:i', strtotime($invoice['payment_time'])) ?> WIB</p>
                    <?php endif; ?>

                    <!-- Payment Method Detail -->
                    <?php if (!empty($invoice['payment_method'])): ?>
                        <p class="mb-1"><strong>Metode yang Digunakan:</strong></p>
                        <p class="mb-3">
                            <?php
                            $methods = [
                                'qris' => '📱 QRIS',
                                'gopay' => '🟢 GoPay',
                                'shopeepay' => '🟠 ShopeePay',
                                'bca_va' => '🏦 Virtual Account BCA',
                                'bni_va' => '🏦 Virtual Account BNI',
                                'bri_va' => '🏦 Virtual Account BRI',
                                'permata_va' => '🏦 Virtual Account Permata',
                                'credit_card' => '💳 Kartu Kredit/Debit',
                                'bank_transfer' => '🏦 Transfer Bank'
                            ];
                            echo $methods[$invoice['payment_method']] ?? '💳 ' . ucfirst($invoice['payment_method']);
                            ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($invoice['transaction_id'])): ?>
                        <hr>
                        <p class="mb-0">
                            <small class="text-muted">
                                <strong>Transaction ID:</strong><br>
                                <code><?= esc($invoice['transaction_id']) ?></code>
                            </small>
                        </p>
                    <?php endif; ?>

                    <!-- Action Buttons for Pending Midtrans Payment -->
                    <?php if ($paymentStatus === 'pending' && $paymentType === 'midtrans'): ?>
                        <hr>
                        
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <small>
                                <strong>Sudah melakukan pembayaran?</strong><br>
                                Klik tombol <strong>"Cek Status Pembayaran"</strong> untuk update otomatis dari Midtrans.
                            </small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="<?= base_url('payment/sync_status/' . $invoice['id']) ?>" 
                                class="btn btn-success btn-block">
                                    <i class="fas fa-sync-alt"></i> Cek Status
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="<?= base_url('payment/process/' . $invoice['id']) ?>" 
                                class="btn btn-primary btn-block">
                                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                                </a>
                            </div>
                        </div>
                        
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> 
                            Batas pembayaran: <strong><?= date('d M Y H:i', strtotime($invoice['batas_bayar'])) ?> WIB</strong>
                        </small>
                        
                    <?php elseif ($paymentStatus === 'expired'): ?>
                        <hr>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Pembayaran Expired</strong><br>
                            Batas waktu pembayaran telah habis. Silakan buat pesanan baru untuk melanjutkan.
                        </div>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-shopping-cart"></i> Belanja Lagi
                        </a>
                        
                    <?php elseif ($paymentStatus === 'failed'): ?>
                        <hr>
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i>
                            <strong>Pembayaran Gagal</strong><br>
                            Silakan coba lagi dengan metode pembayaran yang berbeda.
                        </div>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-shopping-cart"></i> Belanja Lagi
                        </a>
                        
                    <?php elseif ($paymentStatus === 'paid'): ?>
                        <hr>
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i>
                            <strong>Pembayaran Berhasil!</strong><br>
                            Terima kasih. Pesanan Anda sedang diproses.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
            <div class="card mb-3 ml-2 ">
                <div class="card-header bg-info text-white">
                    <strong>Status Pesanan</strong>
                </div>
                <div class="card-body text-center">
                    <?php
                    $badge_class = '';
                    $status_text = '';
                    switch($invoice['status']) {
                        case 'pending':
                            $badge_class = 'badge-warning';
                            $status_text = 'Menunggu Konfirmasi';
                            break;
                        case 'diproses':
                            $badge_class = 'badge-info';
                            $status_text = 'Sedang Diproses';
                            break;
                        case 'dikemas':
                            $badge_class = 'badge-secondary';
                            $status_text = 'Sedang Dikemas';
                            break;
                        case 'dikirim':
                            $badge_class = 'badge-primary';
                            $status_text = 'Dalam Pengiriman';
                            break;
                        case 'selesai':
                            $badge_class = 'badge-success';
                            $status_text = 'Pesanan Selesai';
                            break;
                        case 'dibatalkan':
                            $badge_class = 'badge-danger';
                            $status_text = 'Pesanan Dibatalkan';
                            break;
                    }
                    ?>
                    <h3>
                        <span class="badge <?= $badge_class ?>">
                            <?= $status_text ?>
                        </span>
                    </h3>

                    <?php if (!empty($invoice['no_resi'])): ?>
                        <hr>
                        <p class="mb-1"><strong>Nomor Resi:</strong></p>
                        <h5 class="text-primary"><?= esc($invoice['no_resi']) ?></h5>
                    <?php endif; ?>

                    <?php if (!empty($invoice['catatan_admin'])): ?>
                        <hr>
                        <div class="alert alert-info">
                            <strong>Catatan dari Admin:</strong><br>
                            <?= nl2br(esc($invoice['catatan_admin'])) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($invoice['status'] == 'dikirim' && $invoice['konfirmasi_user'] == 0): ?>
                        <hr>
                        <a href="<?= base_url('pesanan/konfirmasi/' . $invoice['id']) ?>" 
                           class="btn btn-success btn-block"
                           onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima dengan baik?')">
                            <i class="fas fa-check-circle"></i> Konfirmasi Pesanan Diterima
                        </a>
                    <?php endif; ?>

                    <?php if ($invoice['konfirmasi_user'] == 1): ?>
                        <hr>
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Pesanan Telah Dikonfirmasi</strong><br>
                            <small>Pada <?= date('d M Y H:i', strtotime($invoice['tgl_konfirmasi'])) ?></small>
                        </div>
                    <?php endif; ?>

                    <hr>
                    <a href="<?= base_url('pesanan/tracking/' . $invoice['id']) ?>" 
                       class="btn btn-info btn-block">
                        <i class="fas fa-truck"></i> Tracking Pengiriman
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Produk -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>Detail Produk</strong>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
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
                        <td colspan="4" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if ($invoice['status'] == 'selesai' && $invoice['konfirmasi_user'] == 1): ?>
    <!-- Review Section -->
    <div class="card mt-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-star"></i> Berikan Review Produk</h5>
        </div>
        <div class="card-body">
            <?php
            $reviewModel = new \App\Models\Model_review();
            $user_id = session()->get('user_id') ?? 1; // Temporary until auth implemented
            ?>
            
            <?php foreach ($pesanan as $psn): ?>
                <?php
                $hasReviewed = $reviewModel->hasUserReviewed($user_id, $psn['id_brg']);
                ?>
                
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6><?= esc($psn['nama_brg']) ?></h6>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if ($hasReviewed): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Sudah Direview
                                    </span>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-warning" 
                                            data-toggle="modal" 
                                            data-target="#reviewModal<?= $psn['id_brg'] ?>">
                                        <i class="fas fa-star"></i> Tulis Review
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!$hasReviewed): ?>
                    <!-- Review Modal -->
                    <div class="modal fade" id="reviewModal<?= $psn['id_brg'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title">Review: <?= esc($psn['nama_brg']) ?></h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <form action="<?= base_url('review/submit') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $psn['id_brg'] ?>">
                                    <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
                                    
                                    <div class="modal-body">
                                        <!-- Rating Stars -->
                                        <div class="form-group">
                                            <label><strong>Rating:</strong></label>
                                            <div class="rating-input">
                                                <input type="radio" name="rating" value="5" id="star5-<?= $psn['id_brg'] ?>" required>
                                                <label for="star5-<?= $psn['id_brg'] ?>">★</label>
                                                
                                                <input type="radio" name="rating" value="4" id="star4-<?= $psn['id_brg'] ?>">
                                                <label for="star4-<?= $psn['id_brg'] ?>">★</label>
                                                
                                                <input type="radio" name="rating" value="3" id="star3-<?= $psn['id_brg'] ?>">
                                                <label for="star3-<?= $psn['id_brg'] ?>">★</label>
                                                
                                                <input type="radio" name="rating" value="2" id="star2-<?= $psn['id_brg'] ?>">
                                                <label for="star2-<?= $psn['id_brg'] ?>">★</label>
                                                
                                                <input type="radio" name="rating" value="1" id="star1-<?= $psn['id_brg'] ?>">
                                                <label for="star1-<?= $psn['id_brg'] ?>">★</label>
                                            </div>
                                        </div>
                                        
                                        <!-- Comment -->
                                        <div class="form-group">
                                            <label><strong>Komentar:</strong></label>
                                            <textarea name="comment" 
                                                    class="form-control" 
                                                    rows="4" 
                                                    placeholder="Ceritakan pengalaman Anda dengan produk ini... (minimal 10 karakter)"
                                                    required
                                                    minlength="10"
                                                    maxlength="500"></textarea>
                                            <small class="text-muted">Minimal 10 karakter, maksimal 500 karakter</small>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-paper-plane"></i> Kirim Review
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <style>
    /* Star Rating CSS */
    .rating-input {
        direction: rtl;
        display: inline-flex;
        font-size: 2rem;
    }

    .rating-input input[type="radio"] {
        display: none;
    }

    .rating-input label {
        color: #ddd;
        cursor: pointer;
        padding: 0 5px;
    }

    .rating-input label:hover,
    .rating-input label:hover ~ label,
    .rating-input input[type="radio"]:checked ~ label {
        color: #ffc107;
    }
    </style>
</div>