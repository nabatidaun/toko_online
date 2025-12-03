<div class="row">
    <?php 
    $reviewModel = new \App\Models\Model_review();
    foreach ($barang as $brg) : 
        $rating = $reviewModel->getAverageRating($brg['id_brg']);
    ?>
    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 d-flex align-items-stretch">
        <div class="card w-100 d-flex flex-column text-center">
            <img src="<?= base_url('uploads/'.$brg['gambar']); ?>"
                class="card-img-top img-fluid"
                style="height: 200px; object-fit: cover;" alt="...">

            <div class="card-body d-flex flex-column">
                <h5 class="card-title mb-1"><?= $brg['nama_brg'] ?></h5>
                
                <!-- Rating Display -->
                <?php if ($rating['total'] > 0): ?>
                    <div class="mt-2 mb-2">
                        <div class="stars-mini">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($rating['average'])): ?>
                                    <span class="text-warning">★</span>
                                <?php else: ?>
                                    <span class="text-muted">★</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <small class="text-muted">
                            <?= $rating['average'] ?> (<?= $rating['total'] ?> review)
                        </small>
                    </div>
                <?php endif; ?>

                <div class="mt-auto">
                    <span class="badge badge-pill badge-success mb-3 d-block">
                        Rp. <?= number_format($brg['harga'], 0, ',', '.') ?>
                    </span>
                    <a href="<?= base_url('dashboard/tambah_ke_keranjang/'.$brg['id_brg']); ?>"
                        class="btn btn-sm btn-primary mb-1">Tambah ke Keranjang</a>
                    <a href="<?= base_url('dashboard/detail/'.$brg['id_brg']); ?>"
                        class="btn btn-sm btn-success mb-1">Detail</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
.stars-mini {
    font-size: 0.9rem;
}
</style>