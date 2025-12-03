<div class="container-fluid">
    <div class="card">
        <h5 class="card-header">Detail Produk</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="<?= base_url('uploads/' . esc($barang->gambar)) ?>" 
                         alt="<?= esc($barang->nama_brg) ?>" 
                         class="card-img-top">
                </div>
                <div class="col-md-8">
                    <h3 class="mb-3"><?= esc($barang->nama_brg) ?></h3>
                    
                    <!-- Rating Summary -->
                    <?php if ($rating_summary['total'] > 0): ?>
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <h2 class="mb-0"><?= $rating_summary['average'] ?></h2>
                                    <div class="stars-display">
                                        <?php
                                        $avg = $rating_summary['average'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= floor($avg)) {
                                                echo '<span class="text-warning">★</span>';
                                            } elseif ($i == ceil($avg) && $avg - floor($avg) >= 0.5) {
                                                echo '<span class="text-warning">⯨</span>';
                                            } else {
                                                echo '<span class="text-muted">★</span>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <small class="text-muted"><?= $rating_summary['total'] ?> Review</small>
                                </div>
                                
                                <!-- Rating Distribution -->
                                <div class="flex-grow-1">
                                    <?php foreach ([5,4,3,2,1] as $star): ?>
                                        <div class="d-flex align-items-center mb-1">
                                            <small class="mr-2"><?= $star ?>★</small>
                                            <div class="progress flex-grow-1" style="height: 10px;">
                                                <?php
                                                $percentage = $rating_summary['total'] > 0 
                                                    ? ($rating_distribution[$star] / $rating_summary['total']) * 100 
                                                    : 0;
                                                ?>
                                                <div class="progress-bar bg-warning" 
                                                     style="width: <?= $percentage ?>%"></div>
                                            </div>
                                            <small class="ml-2"><?= $rating_distribution[$star] ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <table class="table">
                        <tr>
                            <td width="150">Keterangan</td>
                            <td><strong><?= esc($barang->keterangan) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td><strong><?= esc($barang->kategori) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Stok</td>
                            <td><strong><?= esc($barang->stock) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td><strong><div class="btn btn-sm btn-success">Rp <?= number_format($barang->harga, 0, ',', '.') ?></div></strong></td>
                        </tr>
                    </table>
                    
                    <a href="<?= base_url('dashboard/tambah_ke_keranjang/'.$barang->id_brg); ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                    </a>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-comments"></i> Review Pelanggan</h5>
            <select class="form-control form-control-sm" style="width: auto;" onchange="window.location.href='?sort='+this.value">
                <option value="newest">Terbaru</option>
                <option value="rating_high">Rating Tertinggi</option>
                <option value="rating_low">Rating Terendah</option>
                <option value="helpful">Paling Membantu</option>
            </select>
        </div>
        <div class="card-body">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?= esc($review['user_name'] ?? 'User') ?></strong>
                                <?php if ($review['verified_purchase']): ?>
                                    <span class="badge badge-success badge-sm ml-2">
                                        <i class="fas fa-check-circle"></i> Verified Purchase
                                    </span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= date('d M Y', strtotime($review['created_at'])) ?>
                            </small>
                        </div>
                        
                        <div class="mt-2">
                            <div class="stars-display mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $review['rating']): ?>
                                        <span class="text-warning">★</span>
                                    <?php else: ?>
                                        <span class="text-muted">★</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="mb-2"><?= nl2br(esc($review['comment'])) ?></p>
                            <button class="btn btn-sm btn-outline-secondary" 
                                    onclick="markHelpful(<?= $review['id'] ?>, this)">
                                <i class="fas fa-thumbs-up"></i> 
                                Membantu (<span class="helpful-count"><?= $review['helpful_count'] ?></span>)
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada review untuk produk ini.</p>
                    <p class="text-muted">Jadilah yang pertama memberikan review!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function markHelpful(reviewId, button) {
    fetch('<?= base_url('review/helpful/') ?>' + reviewId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.querySelector('.helpful-count').textContent = data.count;
            button.disabled = true;
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-secondary');
        }
    });
}
</script>

<style>
.stars-display {
    font-size: 1.2rem;
}

.review-item:last-child {
    border-bottom: none !important;
}
</style>