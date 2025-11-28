<?php
/**
 * FILE 1: app/Views/payment/unfinish.php
 * Halaman untuk pembayaran yang belum selesai / ditunda
 */
?>

<!-- UNFINISH VIEW -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-hourglass-half text-warning" style="font-size: 80px;"></i>
                    </div>
                    
                    <h2 class="text-warning mb-3">Pembayaran Tertunda</h2>
                    <p class="lead mb-4">
                        Pembayaran Anda belum diselesaikan.<br>
                        Invoice #<?= $invoice_id ?? '-' ?>
                    </p>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        <strong>Status: Menunggu Pembayaran</strong><br>
                        Anda menutup jendela pembayaran sebelum menyelesaikan transaksi. 
                        Silakan lanjutkan pembayaran Anda.
                    </div>

                    <hr class="my-4">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info text-left">
                                <strong><i class="fas fa-clock"></i> Batas Waktu Pembayaran:</strong><br>
                                <small>Pembayaran harus diselesaikan dalam 24 jam. Jika melewati batas waktu, transaksi akan otomatis dibatalkan.</small>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group" role="group" style="width: 100%;">
                        <a href="<?= base_url('payment/process/' . ($invoice_id ?? '')) ?>" 
                           class="btn btn-warning btn-lg" style="flex: 1;">
                            <i class="fas fa-redo"></i> Lanjutkan Pembayaran
                        </a>
                        <a href="<?= base_url('pesanan/detail/' . ($invoice_id ?? '')) ?>" 
                           class="btn btn-info btn-lg" style="flex: 1;">
                            <i class="fas fa-file-invoice"></i> Lihat Detail Pesanan
                        </a>
                    </div>

                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-lg btn-block mt-2">
                        <i class="fas fa-home"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-question-circle"></i> Butuh Bantuan?</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Mengapa pembayaran tertunda?</strong><br>
                            <small>Anda telah menutup jendela pembayaran sebelum menyelesaikan transaksi.</small>
                        </li>
                        <li class="mb-2">
                            <strong>Apa yang harus saya lakukan?</strong><br>
                            <small>Klik tombol "Lanjutkan Pembayaran" untuk melanjutkan proses pembayaran Anda.</small>
                        </li>
                        <li class="mb-2">
                            <strong>Apakah dana saya aman?</strong><br>
                            <small>Ya, dana Anda aman. Belum ada transaksi yang diproses. Silakan lanjutkan pembayaran kapan saja sebelum batas waktu.</small>
                        </li>
                        <li>
                            <strong>Bagaimana jika saya lupa untuk menyelesaikan pembayaran?</strong><br>
                            <small>Transaksi akan otomatis dibatalkan setelah 24 jam. Anda dapat membuat pesanan baru.</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>