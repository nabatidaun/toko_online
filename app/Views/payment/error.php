<?php
/**
 * FILE 2: app/Views/payment/error.php
 * Halaman untuk pembayaran yang gagal / error
 */
?>

<!-- ERROR VIEW -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 80px;"></i>
                    </div>
                    
                    <h2 class="text-danger mb-3">Pembayaran Gagal</h2>
                    <p class="lead mb-4">
                        Maaf, pembayaran Anda tidak dapat diproses.<br>
                        Invoice #<?= $invoice_id ?? '-' ?>
                    </p>

                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        <strong>Status: Pembayaran Ditolak</strong><br>
                        Terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi dengan metode pembayaran yang lain atau hubungi customer support.
                    </div>

                    <hr class="my-4">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-warning text-left">
                                <strong><i class="fas fa-exclamation-triangle"></i> Kemungkinan Penyebab:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Saldo/limit kartu kredit tidak cukup</li>
                                    <li>Kartu kredit/debit terblokir</li>
                                    <li>Koneksi internet tidak stabil</li>
                                    <li>Data yang dimasukkan tidak sesuai</li>
                                    <li>Transaksi ditolak oleh bank</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group" role="group" style="width: 100%;">
                        <a href="<?= base_url('payment/process/' . ($invoice_id ?? '')) ?>" 
                           class="btn btn-danger btn-lg" style="flex: 1;">
                            <i class="fas fa-redo"></i> Coba Lagi
                        </a>
                        <a href="<?= base_url('dashboard/pembayaran') ?>" 
                           class="btn btn-primary btn-lg" style="flex: 1;">
                            <i class="fas fa-credit-card"></i> Pilih Metode Lain
                        </a>
                    </div>

                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-lg btn-block mt-2">
                        <i class="fas fa-home"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-headset"></i> Hubungi Customer Support</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        Jika masalah terus berlanjut, hubungi tim customer support kami:
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary"></i> 
                            <strong>Email:</strong> <a href="mailto:support@toko.com">support@toko.com</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone text-success"></i> 
                            <strong>WhatsApp:</strong> <a href="https://wa.me/628123456789">+62 812 3456 789</a>
                        </li>
                        <li>
                            <i class="fas fa-comments text-info"></i> 
                            <strong>Live Chat:</strong> Tersedia Senin-Jumat 08:00-17:00 WIB
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> FAQ - Pembayaran Gagal</h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne">
                                        Mengapa pembayaran saya ditolak?
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseOne" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Pembayaran dapat ditolak karena beberapa alasan: saldo tidak cukup, kartu terblokir, data yang dimasukkan salah, atau transaksi mencurigakan menurut bank. Cek dengan bank Anda untuk informasi lebih detail.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingTwo">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                        Apakah pesanan saya dibatalkan?
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseTwo" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Tidak, pesanan Anda masih aktif dan menunggu pembayaran. Anda dapat mencoba membayar lagi dengan metode pembayaran yang berbeda.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingThree">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree">
                                        Berapa lama saya memiliki waktu untuk membayar?
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseThree" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Anda memiliki waktu 24 jam untuk menyelesaikan pembayaran. Jika melewati batas waktu, pesanan akan otomatis dibatalkan dan Anda dapat membuat pesanan baru.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>