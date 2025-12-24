<div class="container-fluid">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <!-- Total Belanja -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Total Belanja</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $grand_total = 0;
                    $keranjang = session()->get('cart') ?? [];

                    if (!empty($keranjang)) {
                        foreach ($keranjang as $item) {
                            $grand_total += $item['harga'] * $item['qty'];
                        }
                    }
                    
                    // Ambil data user dari database untuk auto-fill
                    $userModel = new \App\Models\Model_user();
                    $userData = $userModel->find(session()->get('user_id'));
                    ?>
                    
                    <?php if (!empty($keranjang)): ?>
                        <h2 class="text-success mb-0">
                            <strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong>
                        </h2>
                        <p class="text-muted mb-0">
                            Total <?= count($keranjang) ?> item | 
                            <?= array_sum(array_column($keranjang, 'qty')) ?> produk
                        </p>
                    <?php else: ?>
                        <h4 class="text-danger">Keranjang belanja Anda masih kosong!</h4>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-shopping-bag"></i> Mulai Belanja
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($keranjang)): ?>
                <!-- Info Alert -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Data otomatis diisi dari profile Anda.</strong> 
                    Anda bisa mengubahnya di halaman <a href="<?= base_url('profile') ?>" class="alert-link">Profile</a>
                </div>

                <!-- Form Pembayaran -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Informasi Pengiriman & Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= base_url('dashboard/proses_pesanan') ?>" id="formPembayaran">
                            <?= csrf_field() ?>
                            
                            <div class="form-group">
                                <label for="nama"><strong>Nama Lengkap</strong> <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="nama" 
                                       id="nama" 
                                       class="form-control" 
                                       placeholder="Masukkan nama lengkap Anda" 
                                       value="<?= esc($userData['nama'] ?? '') ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="alamat"><strong>Alamat Lengkap Pengiriman</strong> <span class="text-danger">*</span></label>
                                <textarea name="alamat" 
                                          id="alamat" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="Masukkan alamat lengkap" 
                                          required><?= esc($userData['alamat'] ?? '') ?></textarea>
                                <?php if (empty($userData['alamat'])): ?>
                                    <small class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Alamat belum diisi di profile. <a href="<?= base_url('profile') ?>">Update sekarang</a>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_telp"><strong>No. Telepon</strong> <span class="text-danger">*</span></label>
                                        <input type="tel" 
                                               name="no_telp" 
                                               id="no_telp" 
                                               class="form-control" 
                                               placeholder="08xxxxxxxxxx" 
                                               value="<?= esc($userData['no_telp'] ?? '') ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jasa"><strong>Jasa Pengiriman</strong> <span class="text-danger">*</span></label>
                                        <select class="form-control" name="jasa" id="jasa" required>
                                            <option value="">-- Pilih Jasa Pengiriman --</option>
                                            <option value="JNE">JNE</option>
                                            <option value="JNT">J&T Express</option>
                                            <option value="POS">POS Indonesia</option>
                                            <option value="SICEPAT">SiCepat</option>
                                            <option value="NINJA">Ninja Express</option>
                                            <option value="ANTERAJA">AnterAja</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="payment_method"><strong>Pilih Metode Pembayaran</strong> <span class="text-danger">*</span></label>
                                <select class="form-control" name="payment_method" id="payment_method" required onchange="togglePaymentInfo()">
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                    <option value="midtrans">💳 Pembayaran Online (Kartu Kredit, VA, E-Wallet, QRIS)</option>
                                    <option value="manual">🏦 Transfer Bank Manual</option>
                                </select>
                            </div>

                            <div class="form-group" id="bank_field" style="display: none;">
                                <label for="bank"><strong>Pilih Bank Transfer</strong></label>
                                <select class="form-control" name="bank" id="bank">
                                    <option value="">-- Pilih Bank --</option>
                                    <option value="BCA - 1234567890">BCA - 1234567890</option>
                                    <option value="BNI - 0987654321">BNI - 0987654321</option>
                                    <option value="BRI - 5555666677">BRI - 5555666677</option>
                                    <option value="Mandiri - 1122334455">Mandiri - 1122334455</option>
                                </select>
                            </div>

                            <div class="alert alert-info" id="info_midtrans" style="display: none;">
                                <i class="fas fa-info-circle"></i>
                                <strong>Pembayaran Online:</strong><br>
                                Anda akan diarahkan ke halaman pembayaran Midtrans yang aman.
                            </div>

                            <div class="alert alert-info" id="info_manual" style="display: none;">
                                <i class="fas fa-info-circle"></i>
                                <strong>Transfer Bank Manual:</strong><br>
                                Setelah membuat pesanan, transfer ke rekening pilihan Anda.
                            </div>

                            <hr>

                            <!-- Summary -->
                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title"><strong>Ringkasan Pesanan</strong></h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td>Subtotal Produk</td>
                                            <td class="text-right"><strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Ongkos Kirim</td>
                                            <td class="text-right"><strong>Gratis</strong></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>TOTAL</strong></td>
                                            <td class="text-right"><h5 class="text-success mb-0"><strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong></h5></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('dashboard/keranjang') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle"></i> Buat Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Tambahan -->
                <div class="card shadow mt-4">
                    <div class="card-body">
                        <h6 class="font-weight-bold"><i class="fas fa-shield-alt text-success"></i> Keamanan Transaksi</h6>
                        <p class="text-muted mb-0">
                            <small>
                                <i class="fas fa-check text-success"></i> Data Anda aman dengan enkripsi SSL<br>
                                <i class="fas fa-check text-success"></i> Pembayaran dilindungi oleh Midtrans<br>
                                <i class="fas fa-check text-success"></i> Privasi Anda terjaga
                            </small>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<script>
function togglePaymentInfo() {
    var paymentMethod = document.getElementById('payment_method').value;
    var bankField = document.getElementById('bank_field');
    var infoMidtrans = document.getElementById('info_midtrans');
    var infoManual = document.getElementById('info_manual');
    var bankSelect = document.getElementById('bank');
    
    if (paymentMethod === 'manual') {
        bankField.style.display = 'block';
        bankSelect.required = true;
        infoManual.style.display = 'block';
        infoMidtrans.style.display = 'none';
    } else if (paymentMethod === 'midtrans') {
        bankField.style.display = 'none';
        bankSelect.required = false;
        infoMidtrans.style.display = 'block';
        infoManual.style.display = 'none';
    } else {
        bankField.style.display = 'none';
        bankSelect.required = false;
        infoMidtrans.style.display = 'none';
        infoManual.style.display = 'none';
    }
}
</script>