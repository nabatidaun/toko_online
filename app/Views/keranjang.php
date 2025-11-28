<div class="container-fluid">
    <h4>Keranjang Belanja</h4>

    <table class="table table-bordered table-striped table-hover">
        <tr>
            <th>NO</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>harga</th>
            <th>Sub-Total</th>
            <th>Aksi</th>
        </tr>

        <?php if (!empty($cart)): ?>
            <?php
            $no = 1;
            $total = 0;
            ?>
            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($item['nama']) ?></td>
                    <td><?= esc($item['qty']) ?></td>
                    <td align="right">Rp. <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td align="right">Rp. <?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></td>
                    <td>
                        <a href="<?= base_url('dashboard/hapus_item/' . $item['id']) ?>" 
                           class="btn btn-sm btn-danger">
                            Hapus
                        </a>
                    </td>
                </tr>
                <?php $total += $item['harga'] * $item['qty']; ?>
            <?php endforeach; ?>

            <tr>
                <td colspan="4"></td>
                <td align="right">Rp. <?= number_format($total, 0, ',', '.') ?></strong></td>
            </tr>

        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">Keranjang masih kosong.</td>
            </tr>
        <?php endif; ?>

    </table>

    <div align="right">
        <a href="<?= base_url('dashboard/hapus_semua') ?>"><div 
        class="btn btn-sm btn-danger">Hapus Keranjang</div></a>
        <a href="<?= base_url('dashboard') ?>"><div 
        class="btn btn-sm btn-primary">Lanjutkan Belanja</div></a>
        <a href="<?= base_url('dashboard/pembayaran') ?>"><div 
        class="btn btn-sm btn-success">Pembayaran</div></a>
    </div>
</div>