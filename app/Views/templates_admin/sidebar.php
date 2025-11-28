<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url('admin'); ?>">
                <div class="sidebar-brand-icon ">
                    <i class="fas fa-store"></i>
                </div>
                <div class="sidebar-brand-text mx-3">ADMIN PANEL</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo base_url('admin/dashboard_admin/'); ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard Analytics</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                MANAJEMEN
            </div>

            <!-- Nav Item - Data Barang -->
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('admin/data_barang/'); ?>">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Data Barang</span></a>
            </li>

            <!-- Nav Item - Invoice -->
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('admin/invoice'); ?>">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Invoice & Pesanan</span>
                    <?php
                    try {
                        $db = \Config\Database::connect();
                        
                        // Badge untuk pesanan pending
                        $pending = $db->table('tb_invoice')->where('status', 'pending')->countAllResults();
                        
                        // Badge untuk pembayaran pending
                        $payment_pending = $db->table('tb_invoice')
                            ->where('payment_status', 'pending')
                            ->where('payment_type', 'midtrans')
                            ->countAllResults();
                        
                        $total_badge = $pending + $payment_pending;
                        
                        if ($total_badge > 0) {
                            echo '<span class="badge badge-danger badge-counter">' . $total_badge . '</span>';
                        }
                    } catch (\Exception $e) {
                        // Database error, skip badge
                        log_message('error', 'Sidebar badge error: ' . $e->getMessage());
                    }
                    ?>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                QUICK STATS
            </div>

            <!-- Quick Stats - Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStats">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Statistik</span>
                </a>
                <div id="collapseStats" class="collapse">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <?php
                        try {
                            $db = \Config\Database::connect();
                            
                            // Total Pendapatan
                            $total_rev = $db->query("SELECT SUM(harga * jumlah) as total FROM tb_pesanan")->getRowArray();
                            $totalRevenue = $total_rev['total'] ?? 0;
                            
                            // Total Pesanan
                            $total_orders = $db->table('tb_invoice')->countAllResults();
                            
                            // Total Produk
                            $total_products = $db->table('tb_barang')->countAllResults();
                            
                            // Stok Habis
                            $out_stock = $db->table('tb_barang')->where('stock', 0)->countAllResults();
                        } catch (\Exception $e) {
                            $totalRevenue = 0;
                            $total_orders = 0;
                            $total_products = 0;
                            $out_stock = 0;
                            log_message('error', 'Quick stats error: ' . $e->getMessage());
                        }
                        ?>
                        <h6 class="collapse-header">Info Cepat:</h6>
                        <small class="collapse-item text-success">
                            <strong>Pendapatan:</strong><br>
                            Rp <?= number_format($totalRevenue, 0, ',', '.') ?>
                        </small>
                        <small class="collapse-item text-info">
                            <strong>Total Pesanan:</strong> <?= $total_orders ?>
                        </small>
                        <small class="collapse-item text-warning">
                            <strong>Total Produk:</strong> <?= $total_products ?>
                        </small>
                        <?php if ($out_stock > 0): ?>
                            <small class="collapse-item text-danger">
                                <strong>Stok Habis:</strong> <?= $out_stock ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <?php
                                try {
                                    $db = \Config\Database::connect();
                                    $low_stock = $db->table('tb_barang')->where('stock <', 5)->where('stock >', 0)->countAllResults();
                                    if ($low_stock > 0) {
                                        echo '<span class="badge badge-danger badge-counter">' . $low_stock . '</span>';
                                    }
                                } catch (\Exception $e) {
                                    log_message('error', 'Alert badge error: ' . $e->getMessage());
                                }
                                ?>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Notifikasi
                                </h6>
                                <?php
                                try {
                                    $db = \Config\Database::connect();
                                    $low_stock_items = $db->table('tb_barang')
                                        ->where('stock <', 5)
                                        ->where('stock >', 0)
                                        ->limit(3)
                                        ->get()->getResultArray();
                                    
                                    if (!empty($low_stock_items)):
                                        foreach ($low_stock_items as $item):
                                ?>
                                <a class="dropdown-item d-flex align-items-center" href="<?= base_url('admin/data_barang') ?>">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500"><?= date('d M Y') ?></div>
                                        <span class="font-weight-bold">Stok <?= esc($item['nama_brg']) ?> menipis (<?= $item['stock'] ?>)</span>
                                    </div>
                                </a>
                                <?php 
                                        endforeach;
                                    else:
                                ?>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada notifikasi</a>
                                <?php 
                                    endif;
                                } catch (\Exception $e) {
                                    echo '<a class="dropdown-item text-center small text-gray-500" href="#">Error loading notifications</a>';
                                    log_message('error', 'Notifications error: ' . $e->getMessage());
                                }
                                ?>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle"
                                    src="<?php echo base_url() ?>assets/img/cyrene.jpg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="<?= base_url('admin/dashboard_admin') ?>">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Dashboard
                                </a>
                                <a class="dropdown-item" href="<?= base_url('admin/invoice') ?>">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Pesanan
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->