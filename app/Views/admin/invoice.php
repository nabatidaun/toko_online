<div class="container-fluid">
    <h4>Invoice Pemesanan Produk</h4>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter Status -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="<?= base_url('admin/invoice?status=semua') ?>" 
                   class="btn btn-sm <?= ($filter_status == 'semua') ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Semua (<?= array_sum($count_status) ?>)
                </a>
                <a href="<?= base_url('admin/invoice?status=pending') ?>" 
                   class="btn btn-sm <?= ($filter_status == 'pending') ? 'btn-warning' : 'btn-outline-warning' ?>">
                    Pending (<?= $count_status['pending'] ?>)
                </a>
                <a href="<?= base_url('admin/invoice?status=diproses') ?>" 
                   class="btn btn-sm <?= ($filter_status == 'diproses') ? 'btn-info' : 'btn-outline-info' ?>">
                    Diproses (<?= $count_status['diproses'] ?>)
                </a>
                <a href="<?= base_url('admin/invoice?status=dikemas') ?>" 
                   class="btn btn-sm <?= ($filter_status == 'dikemas') ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                    Dikemas (<?= $count_status['dikemas'] ?>)
                </a>
                <a href="<?= base_url('admin/invoice?status=dikirim') ?>" 
                   class="btn btn-sm <?= ($filter_status == 'dikirim') ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Dikirim (<?= $count_status['dikirim'] ?>)
                </a>
                <a href="<?= base_url('admin/invoice?status=selesai') ?>" 
                   class="btn btn-sm <?= ($filter_status == 'selesai') ? 'btn-success' : 'btn-outline-success' ?>">
                    Selesai (<?= $count_status['selesai'] ?>)
                </a>
                <a href="<?= base_url('admin/invoice?status=dibatalkan') ?>" 
                   class="btn btn-sm <?= ($filter_status == 'dibatalkan') ? 'btn-danger' : 'btn-outline-danger' ?>">
                    Dibatalkan (<?= $count_status['dibatalkan'] ?>)
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Invoice -->
    <table class="table table-bordered table-hover table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID Invoice</th>
                <th>Nama Pemesan</th>
                <th>Alamat Pengiriman</th>
                <th>Tanggal Pemesanan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($invoice)): ?>
                <?php foreach ($invoice as $inv): ?>
                    <tr>
                        <td><strong>#<?= esc($inv['id']) ?></strong></td>
                        <td><?= esc($inv['nama']) ?></td>
                        <td><?= esc($inv['alamat']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($inv['tgl_pesan'])) ?></td>
                        <td>
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
                                default:
                                    $badge_class = 'badge-secondary';
                            }
                            ?>
                            <span class="badge <?= $badge_class ?>">
                                <?= strtoupper(esc($inv['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/invoice/detail/'.$inv['id']) ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada data invoice.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>