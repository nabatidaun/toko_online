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
                    <table class="table">
                        <tr>
                            <td>NAMA PRODUK</td>
                            <td><strong><?= esc($barang->nama_brg) ?></strong></td>
                        </tr>

                        <tr>
                            <td>Keterangan</td>
                            <td><strong><?= esc($barang->keterangan) ?></td>
                        </tr>

                        <tr>
                            <td>Kategori</td>
                            <td><strong><?= esc($barang->kategori) ?></td>
                        </tr>

                        <tr>
                            <td>Stok</td>
                            <td><strong><?= esc($barang->stock) ?></td>
                        </tr>

                        <tr>
                            <td>Harga</td>
                            <td><strong><div class="btn btn-sm btn-success">Rp <?= number_format($barang->harga, 0, ',', '.') ?></div></td>
                        </tr>
                    </table>
                    <a href="<?= base_url('dashboard/tambah_ke_keranjang/'.$barang->id_brg); ?>" class="btn btn-sm btn-primary">Tambah ke Keranjang</a>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>

        </div>
    </div>
</div>
