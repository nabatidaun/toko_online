<?php

use CodeIgniter\Router\RouteCollection;
use Config\Services;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
|--------------------------------------------------------------------------
| ROUTE HOMEPAGE
|--------------------------------------------------------------------------
*/
$routes->get('/', 'Dashboard::index');

/*
|--------------------------------------------------------------------------
| ROUTES UTAMA (FRONTEND)
|--------------------------------------------------------------------------
*/
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('dashboard/tambah_ke_keranjang/(:num)', 'Dashboard::tambah_ke_keranjang/$1');
$routes->get('/dashboard/detail/(:num)', 'Dashboard::detail/$1');
$routes->get('dashboard/keranjang', 'Dashboard::keranjang');
$routes->get('/dashboard/hapus_item/(:num)', 'Dashboard::hapus_item/$1');
$routes->get('dashboard/hapus_semua', 'Dashboard::hapus_semua');
$routes->get('/dashboard/detail_keranjang', 'Dashboard::detail_keranjang');
$routes->get('dashboard/pembayaran', 'Dashboard::pembayaran');
$routes->post('dashboard/proses_pesanan', 'Dashboard::proses_pesanan');

/*
|--------------------------------------------------------------------------
| ROUTES PAYMENT GATEWAY (MIDTRANS) - JANGAN LUPA BAGIAN INI!
|--------------------------------------------------------------------------
*/
$routes->get('payment/process/(:num)', 'Payment::process/$1');
$routes->post('payment/notification', 'Payment::notification');
$routes->get('payment/finish/(:num)', 'Payment::finish/$1');
$routes->get('payment/unfinish/(:num)', 'Payment::unfinish/$1');
$routes->get('payment/error/(:num)', 'Payment::error/$1');
$routes->get('payment/check_status/(:num)', 'Payment::check_status/$1');
$routes->get('payment/sync_status/(:num)', 'Payment::sync_status/$1');
$routes->get('payment/sync_all_pending', 'Payment::sync_all_pending');

/*
|--------------------------------------------------------------------------
| ROUTES PESANAN (USER SIDE)
|--------------------------------------------------------------------------
*/
$routes->get('pesanan', 'Pesanan::index');
$routes->get('pesanan/detail/(:num)', 'Pesanan::detail/$1');
$routes->get('pesanan/tracking/(:num)', 'Pesanan::tracking/$1');
$routes->get('pesanan/konfirmasi/(:num)', 'Pesanan::konfirmasi/$1');

/*
|--------------------------------------------------------------------------
| ROUTES ADMIN
|--------------------------------------------------------------------------
*/
$routes->get('/admin/dashboard_admin', 'Admin\Dashboard_admin::index');
$routes->get('/admin', 'Admin\Dashboard_admin::index');

// CRUD Data Barang
$routes->get('admin/data_barang', 'Admin\Data_barang::index');
$routes->get('admin/data_barang/edit/(:num)', 'Admin\Data_barang::edit/$1');
$routes->get('admin/data_barang/hapus/(:num)', 'Admin\Data_barang::hapus/$1');
$routes->post('admin/data_barang/tambah_aksi', 'Admin\Data_barang::tambah_aksi');
$routes->post('admin/data_barang/update', 'Admin\Data_barang::update');

// Invoice & Pesanan
$routes->get('admin/invoice', 'Admin\Invoice::index');
$routes->post('admin/invoice', 'Admin\Invoice::index');
$routes->get('admin/invoice/detail/(:num)', 'Admin\Invoice::detail/$1');
$routes->post('admin/invoice/update_status/(:num)', 'Admin\Invoice::update_status/$1');
$routes->post('admin/invoice/batalkan/(:num)', 'Admin\Invoice::batalkan/$1');

/*
|--------------------------------------------------------------------------
| ROUTES KATEGORI PRODUK
|--------------------------------------------------------------------------
*/
$routes->get('poster', 'Dashboard::poster');
$routes->get('pamflet', 'Dashboard::pamflet');
$routes->get('kategori/poster', 'Kategori::poster');
$routes->get('kategori/pamflet', 'Kategori::pamflet');