<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Analytics</h1>
        <span class="text-muted">Data Real-Time dari Database</span>
    </div>

    <!-- Content Row - Revenue Cards -->
    <div class="row">

        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($total_revenue ?? 0, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendapatan Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($monthly_revenue ?? 0, 0, ',', '.') ?>
                            </div>
                            <?php 
                            $growth = $monthly_growth ?? 0;
                            $growthClass = $growth >= 0 ? 'success' : 'danger';
                            $arrow = $growth >= 0 ? 'up' : 'down';
                            ?>
                            <small class="text-<?= $growthClass ?>">
                                <i class="fas fa-arrow-<?= $arrow ?>"></i>
                                <?= abs($growth) ?>% vs bulan lalu
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pendapatan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($today_revenue ?? 0, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Pesanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_orders ?? 0 ?></div>
                            <small class="text-muted">
                                Bulan ini: <?= $monthly_orders ?? 0 ?> | Hari ini: <?= $today_orders ?? 0 ?>
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row - Status Pesanan -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pesanan</h6>
                    <a href="<?= base_url('admin/invoice') ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <?php 
                    $orderStatus = $order_status ?? [
                        'pending' => 0,
                        'diproses' => 0,
                        'dikemas' => 0,
                        'dikirim' => 0,
                        'selesai' => 0,
                        'dibatalkan' => 0
                    ];
                    ?>
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="card bg-warning text-white mb-2">
                                <div class="card-body py-3">
                                    <h2 class="mb-0"><?= $orderStatus['pending'] ?></h2>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white mb-2">
                                <div class="card-body py-3">
                                    <h2 class="mb-0"><?= $orderStatus['diproses'] ?></h2>
                                    <small>Diproses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white mb-2">
                                <div class="card-body py-3">
                                    <h2 class="mb-0"><?= $orderStatus['dikemas'] ?></h2>
                                    <small>Dikemas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-primary text-white mb-2">
                                <div class="card-body py-3">
                                    <h2 class="mb-0"><?= $orderStatus['dikirim'] ?></h2>
                                    <small>Dikirim</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white mb-2">
                                <div class="card-body py-3">
                                    <h2 class="mb-0"><?= $orderStatus['selesai'] ?></h2>
                                    <small>Selesai</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white mb-2">
                                <div class="card-body py-3">
                                    <h2 class="mb-0"><?= $orderStatus['dibatalkan'] ?></h2>
                                    <small>Dibatalkan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts -->
    <div class="row">

        <!-- Chart 7 Hari Terakhir -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Penjualan 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <canvas id="chart7Days" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Produk & Stok Info -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Produk</h6>
                </div>
                <div class="card-body">
                    <?php
                    $totalProd = $total_products ?? 0;
                    $lowStock = $low_stock_products ?? 0;
                    $outStock = $out_of_stock_products ?? 0;
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-xs font-weight-bold text-uppercase">Total Produk</span>
                            <span class="h5 mb-0 font-weight-bold"><?= $totalProd ?></span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-xs font-weight-bold text-uppercase">Stok Menipis</span>
                            <span class="h5 mb-0 font-weight-bold text-warning"><?= $lowStock ?></span>
                        </div>
                        <div class="progress">
                            <?php 
                            $lowPercent = $totalProd > 0 ? ($lowStock / $totalProd * 100) : 0;
                            ?>
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: <?= $lowPercent ?>%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-xs font-weight-bold text-uppercase">Stok Habis</span>
                            <span class="h5 mb-0 font-weight-bold text-danger"><?= $outStock ?></span>
                        </div>
                        <div class="progress">
                            <?php 
                            $outPercent = $totalProd > 0 ? ($outStock / $totalProd * 100) : 0;
                            ?>
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: <?= $outPercent ?>%"></div>
                        </div>
                    </div>

                    <?php if ($lowStock > 0 || $outStock > 0): ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian!</strong> Ada produk yang perlu direstock.
                            <a href="<?= base_url('admin/data_barang') ?>" class="alert-link">Lihat detail</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row - Top Products & Category Sales -->
    <div class="row">

        <!-- Top 5 Produk Terlaris -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Produk Terlaris</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $topProducts = $top_products ?? [];
                    if (!empty($topProducts)): 
                    ?>
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Terjual</th>
                                    <th class="text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topProducts as $index => $product): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">#<?= $index + 1 ?></span>
                                            <?= esc($product['nama_brg']) ?>
                                        </td>
                                        <td class="text-center">
                                            <strong><?= $product['total_terjual'] ?></strong> pcs
                                        </td>
                                        <td class="text-right">
                                            Rp <?= number_format($product['total_pendapatan'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center py-4">Belum ada data penjualan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Penjualan per Kategori -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Penjualan per Kategori</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $categorySales = $category_sales ?? [];
                    if (!empty($categorySales)): 
                    ?>
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center">Pesanan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorySales as $cat): ?>
                                    <tr>
                                        <td>
                                            <strong><?= ucfirst(esc($cat['kategori'])) ?></strong>
                                        </td>
                                        <td class="text-center"><?= $cat['total_pesanan'] ?></td>
                                        <td class="text-center"><?= $cat['total_qty'] ?> pcs</td>
                                        <td class="text-right">
                                            Rp <?= number_format($cat['total_pendapatan'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center py-4">Belum ada data penjualan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart 12 Bulan -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Penjualan 12 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMonthly" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk chart 7 hari
    <?php 
    $chart7Days = $chart_7days ?? [];
    $labels7Days = [];
    $data7Days = [];
    foreach ($chart7Days as $day) {
        $labels7Days[] = $day['date'];
        $data7Days[] = $day['total'];
    }
    ?>
    
    // Chart 7 Hari Terakhir
    const ctx7Days = document.getElementById('chart7Days');
    if (ctx7Days) {
        const chart7Days = new Chart(ctx7Days.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($labels7Days) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($data7Days) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Data untuk chart 12 bulan
    <?php 
    $chartMonthly = $chart_monthly ?? [];
    $labelsMonthly = [];
    $dataMonthly = [];
    foreach ($chartMonthly as $month) {
        $labelsMonthly[] = $month['month'];
        $dataMonthly[] = $month['total'];
    }
    ?>

    // Chart 12 Bulan
    const ctxMonthly = document.getElementById('chartMonthly');
    if (ctxMonthly) {
        const chartMonthly = new Chart(ctxMonthly.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($labelsMonthly) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($dataMonthly) ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>