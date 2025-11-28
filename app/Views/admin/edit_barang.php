<div class="container-fluid">
    <h3><i class="fa fa-edit"></i></I>EDIT DATA BARANG</h3>

        <form method="post" action="<?=  base_url(). 'admin/data_barang/update' ?>" >
            <div class="form-group">
                <label for="">Nama Barang</label>
                <input type="text" name="nama_brg" class="form-control" value="<?= $barang['nama_brg'] ?>">
            </div>

            <div class="form-group">
                <label for="">Keterangan</label>
                <input type="hidden" name="id_brg" class="form-control" value="<?= $barang['id_brg'] ?>">
                <input type="text" name="keterangan" class="form-control" value="<?= $barang['keterangan'] ?>">
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" class="form-control">
                    <!-- Pilihan kategori -->
                    <option value="poster" <?= ($barang['kategori'] == 'poster') ? 'selected' : ''; ?>>Poster</option>
                    <option value="pamflet" <?= ($barang['kategori'] == 'pamflet') ? 'selected' : ''; ?>>Pamflet</option>
                </select>
            </div>

            <div class="form-group">
                <label for="">Harga</label>
                <input type="text" name="harga" class="form-control" value="<?= $barang['harga'] ?>">
            </div>

            <div class="form-group">
                <label for="">Stock</label>
                <input type="text" name="stock" class="form-control" value="<?= $barang['stock'] ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-sm mt-3">Simpan</button>

        </form>

</div>