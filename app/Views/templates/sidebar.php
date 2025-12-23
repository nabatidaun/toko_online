<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('dashboard') ?>">
                <div class="sidebar-brand-icon ">
                    <i class="fas fa-store"></i>
                </div>
                <div class="sidebar-brand-text mx-3">TOKO ONLINE</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?= base_url('dashboard') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                KATEGORI
            </div>

            <!-- Nav Item - Poster -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('kategori/poster') ?>">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Poster</span></a>
            </li>

            <!-- Nav Item - Pamflet -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('kategori/pamflet') ?>">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Pamflet</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                TRANSAKSI
            </div>

            <!-- Nav Item - Pesanan Saya -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('pesanan') ?>">
                    <i class="fas fa-fw fa-shopping-bag"></i>
                    <span>Pesanan Saya</span>
                    <?php
                    // Hitung pesanan yang belum selesai
                    try {
                        $db = \Config\Database::connect();
                        $builder = $db->table('tb_invoice');
                        $pending_count = $builder->whereIn('status', ['pending', 'diproses', 'dikemas', 'dikirim'])->countAllResults();
                        
                        if ($pending_count > 0) {
                            echo '<span class="badge badge-danger badge-counter">' . $pending_count . '</span>';
                        }
                    } catch (\Exception $e) {
                        // Skip if error
                    }
                    ?>
                </a>
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
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search"
                        action="<?= base_url('dashboard/search') ?>" method="get">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control bg-light border-0 small" 
                                   placeholder="Cari produk..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search" 
                                      action="<?= base_url('dashboard/search') ?>" method="get">
                                    <div class="input-group">
                                        <input type="text" name="q" class="form-control bg-light border-0 small"
                                            placeholder="Cari produk..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- CART ICON - PERBAIKAN DI SINI -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('dashboard/keranjang') ?>">
                                <i class="fas fa-shopping-cart fa-fw"></i>
                                <?php
                                // Ambil cart dari session
                                $cart = session()->get('cart') ?? [];
                                $totalItems = count($cart);
                                
                                if ($totalItems > 0) {
                                    echo '<span class="badge badge-danger badge-counter">' . $totalItems . '</span>';
                                }
                                ?>
                                <span class="d-none d-md-inline-block ml-2">
                                    Keranjang (<?= $totalItems ?>)
                                </span>
                            </a>
                        </li>
                        
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <?php if (session()->get('logged_in')): ?>
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                                role="button" data-toggle="dropdown">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        <?= esc(session()->get('nama')) ?>
                                    </span>
                                    <img class="img-profile rounded-circle" src="<?= base_url('assets/img/user.png') ?>">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                    <a class="dropdown-item" href="<?= base_url('profile') ?>">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('pesanan') ?>">
                                        <i class="fas fa-shopping-bag fa-sm fa-fw mr-2 text-gray-400"></i> Pesanan
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('dashboard/keranjang') ?>">
                                        <i class="fas fa-shopping-cart fa-sm fa-fw mr-2 text-gray-400"></i> Keranjang
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                                    </a>
                                </div>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('auth/login') ?>">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('auth/register') ?>">
                                    <i class="fas fa-user-plus"></i> Register
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>

                </nav>
                <!-- End of Topbar -->