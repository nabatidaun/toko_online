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
$routes->setAutoRoute(false);

/*
|--------------------------------------------------------------------------
| ROUTE HOMEPAGE (PUBLIC)
|--------------------------------------------------------------------------
*/
$routes->get('/', 'Dashboard::index');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/detail/(:num)', 'Dashboard::detail/$1');
$routes->get('/dashboard/search', 'Dashboard::search');

/*
|--------------------------------------------------------------------------
| ROUTES AUTHENTICATION
|--------------------------------------------------------------------------
*/
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login_process', 'Auth::login_process');
    $routes->get('register', 'Auth::register');
    $routes->post('register_process', 'Auth::register_process');
    $routes->get('logout', 'Auth::logout');
    $routes->get('check_auth', 'Auth::check_auth');
    $routes->get('forgot_password', 'Auth::forgot_password');
    $routes->post('reset_password', 'Auth::reset_password');
});

/*
|--------------------------------------------------------------------------
| ROUTES YANG HARUS LOGIN (USER)
|--------------------------------------------------------------------------
*/
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Keranjang & Checkout
    $routes->get('dashboard/tambah_ke_keranjang/(:num)', 'Dashboard::tambah_ke_keranjang/$1');
    $routes->get('dashboard/keranjang', 'Dashboard::keranjang'); // HTML VIEW
    $routes->get('dashboard/hapus_item/(:num)', 'Dashboard::hapus_item/$1');
    $routes->get('dashboard/hapus_semua', 'Dashboard::hapus_semua');
    $routes->get('dashboard/detail_keranjang', 'Dashboard::detail_keranjang'); // AJAX JSON
    $routes->post('dashboard/update_cart', 'Dashboard::update_cart'); // AJAX
    $routes->get('dashboard/cart_count', 'Dashboard::cart_count'); // AJAX
    $routes->get('dashboard/pembayaran', 'Dashboard::pembayaran');
    $routes->post('dashboard/proses_pesanan', 'Dashboard::proses_pesanan');
    
    // Profile User
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
    $routes->post('profile/change_password', 'Profile::change_password');
    $routes->get('profile/addresses', 'Profile::addresses');
    
    // Pesanan
    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/detail/(:num)', 'Pesanan::detail/$1');
    $routes->get('pesanan/tracking/(:num)', 'Pesanan::tracking/$1');
    $routes->get('pesanan/konfirmasi/(:num)', 'Pesanan::konfirmasi/$1');
    
    // Review (harus sudah beli)
    $routes->post('review/submit', 'Review::submit');
    $routes->post('review/helpful/(:num)', 'Review::helpful/$1');
    
    // Wishlist
    $routes->get('wishlist', 'Wishlist::index');
    $routes->post('wishlist/add/(:num)', 'Wishlist::add/$1');
    $routes->get('wishlist/remove/(:num)', 'Wishlist::remove/$1');
});

/*
|--------------------------------------------------------------------------
| ROUTES PAYMENT (MIDTRANS) - PUBLIC untuk callback
|--------------------------------------------------------------------------
*/
$routes->group('payment', function($routes) {
    $routes->get('process/(:num)', 'Payment::process/$1', ['filter' => 'auth']);
    $routes->post('notification', 'Payment::notification'); // Webhook Midtrans
    $routes->get('finish/(:num)', 'Payment::finish/$1');
    $routes->get('unfinish/(:num)', 'Payment::unfinish/$1');
    $routes->get('error/(:num)', 'Payment::error/$1');
    $routes->get('check_status/(:num)', 'Payment::check_status/$1', ['filter' => 'auth']);
    $routes->get('sync_status/(:num)', 'Payment::sync_status/$1', ['filter' => 'auth']);
    $routes->get('sync_all_pending', 'Payment::sync_all_pending', ['filter' => 'auth:admin']);
});

/*
|--------------------------------------------------------------------------
| ROUTES KATEGORI (PUBLIC)
|--------------------------------------------------------------------------
*/
$routes->get('kategori/poster', 'Kategori::poster');
$routes->get('kategori/pamflet', 'Kategori::pamflet');
$routes->get('poster', 'Dashboard::poster');
$routes->get('pamflet', 'Dashboard::pamflet');

/*
|--------------------------------------------------------------------------
| ROUTES ADMIN (HARUS LOGIN SEBAGAI ADMIN)
|--------------------------------------------------------------------------
*/
$routes->group('admin', ['filter' => 'auth:admin,superadmin'], function($routes) {
    // Dashboard Admin
    $routes->get('/', 'Admin\Dashboard_admin::index');
    $routes->get('dashboard_admin', 'Admin\Dashboard_admin::index');
    
    // Data Barang
    $routes->get('data_barang', 'Admin\Data_barang::index');
    $routes->post('data_barang/tambah_aksi', 'Admin\Data_barang::tambah_aksi');
    $routes->get('data_barang/edit/(:num)', 'Admin\Data_barang::edit/$1');
    $routes->post('data_barang/update', 'Admin\Data_barang::update');
    $routes->get('data_barang/hapus/(:num)', 'Admin\Data_barang::hapus/$1');
    
    // Invoice & Pesanan
    $routes->get('invoice', 'Admin\Invoice::index');
    $routes->post('invoice', 'Admin\Invoice::index');
    $routes->get('invoice/detail/(:num)', 'Admin\Invoice::detail/$1');
    $routes->post('invoice/update_status/(:num)', 'Admin\Invoice::update_status/$1');
    $routes->post('invoice/batalkan/(:num)', 'Admin\Invoice::batalkan/$1');
    
    // User Management (opsional)
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\Users::update/$1');
    $routes->get('users/toggle_status/(:num)', 'Admin\Users::toggle_status/$1');
});