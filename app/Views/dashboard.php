<div class="container-fluid mt-4">

    <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        </ol>

        <div class="carousel-inner">
            <div class="carousel-item active">
            <img src="<?= base_url('assets/img/slider1.jpg'); ?>" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
            <img src="<?= base_url('assets/img/slider2.jpg'); ?>" class="d-block w-100" alt="Slide 2">
            </div>
        </div>

        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="row text-center mt-4"></div>

    <div class="row">
        <?php foreach ($barang as $brg) : ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 d-flex align-items-stretch">
            <div class="card w-100 d-flex flex-column text-center">
            <img src="<?= base_url('uploads/'.$brg['gambar']); ?>"
                class="card-img-top img-fluid"
                style="height: 200px; object-fit: cover;" alt="...">

            <div class="card-body d-flex flex-column">
                <h5 class="card-title mb-1"><?= $brg['nama_brg'] ?></h5>
                <small class="text-muted"><?= $brg['keterangan'] ?></small>

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
</div>