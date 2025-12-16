<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Toko Online</title>
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    <style>
        .register-container {
            min-height: 100vh;
            padding: 2rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .register-card {
            max-width: 600px;
            width: 100%;
        }
        .brand-logo {
            font-size: 2.5rem;
            color: #4e73df;
            text-align: center;
            margin-bottom: 1rem;
        }
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e3e6f0;
            z-index: 0;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #e3e6f0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .step.active .step-circle {
            background: #4e73df;
            color: white;
            border-color: #4e73df;
        }
        .step.completed .step-circle {
            background: #1cc88a;
            color: white;
            border-color: #1cc88a;
        }
        .step-label {
            font-size: 0.85rem;
            color: #858796;
        }
        .step.active .step-label {
            color: #4e73df;
            font-weight: bold;
        }
        .password-strength {
            height: 5px;
            background: #e3e6f0;
            border-radius: 5px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            transition: all 0.3s;
        }
        .strength-weak { 
            width: 33%; 
            background: #e74a3b; 
        }
        .strength-medium { 
            width: 66%; 
            background: #f6c23e; 
        }
        .strength-strong { 
            width: 100%; 
            background: #1cc88a; 
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        .password-requirements {
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        .requirement {
            color: #858796;
            margin-bottom: 0.25rem;
        }
        .requirement.met {
            color: #1cc88a;
        }
        .requirement i {
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card register-card shadow-lg border-0">
                        <div class="card-body p-5">
                            <!-- Logo/Brand -->
                            <div class="text-center mb-4">
                                <i class="fas fa-store brand-logo"></i>
                                <h4 class="font-weight-bold">DAFTAR AKUN BARU</h4>
                                <p class="text-muted">Lengkapi data diri Anda untuk membuat akun</p>
                            </div>

                            <!-- Progress Steps -->
                            <div class="progress-steps">
                                <div class="step active" data-step="1">
                                    <div class="step-circle">1</div>
                                    <div class="step-label">Akun</div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-circle">2</div>
                                    <div class="step-label">Data Diri</div>
                                </div>
                                <div class="step" data-step="3">
                                    <div class="step-circle">3</div>
                                    <div class="step-label">Selesai</div>
                                </div>
                            </div>

                            <!-- Alert Messages -->
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <ul class="mb-0">
                                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Registration Form -->
                            <form action="<?= base_url('auth/register_process') ?>" method="post" id="registerForm">
                                <?= csrf_field() ?>

                                <!-- Step 1: Account Info -->
                                <div class="form-section active" id="step1">
                                    <h5 class="mb-3 font-weight-bold">Informasi Akun</h5>
                                    
                                    <div class="form-group">
                                        <label for="email"><strong>Email <span class="text-danger">*</span></strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   placeholder="contoh@email.com" 
                                                   value="<?= old('email') ?>" required>
                                        </div>
                                        <small class="form-text text-muted">Email akan digunakan untuk login</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="password"><strong>Password <span class="text-danger">*</span></strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" class="form-control" id="password" name="password" 
                                                   placeholder="Minimal 6 karakter" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Password Strength Indicator -->
                                        <div class="password-strength">
                                            <div class="password-strength-bar" id="strengthBar"></div>
                                        </div>
                                        <small class="form-text" id="strengthText"></small>
                                        
                                        <!-- Password Requirements -->
                                        <div class="password-requirements">
                                            <div class="requirement" id="req-length">
                                                <i class="fas fa-circle"></i> Minimal 6 karakter
                                            </div>
                                            <div class="requirement" id="req-letter">
                                                <i class="fas fa-circle"></i> Minimal 1 huruf
                                            </div>
                                            <div class="requirement" id="req-number">
                                                <i class="fas fa-circle"></i> Minimal 1 angka (recommended)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirm"><strong>Konfirmasi Password <span class="text-danger">*</span></strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                                   placeholder="Ketik ulang password" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-danger d-none" id="passwordMatchError">
                                            Password tidak cocok!
                                        </small>
                                    </div>

                                    <button type="button" class="btn btn-primary btn-block btn-lg" id="nextStep1">
                                        Lanjut <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>

                                <!-- Step 2: Personal Info -->
                                <div class="form-section" id="step2">
                                    <h5 class="mb-3 font-weight-bold">Data Diri</h5>
                                    
                                    <div class="form-group">
                                        <label for="nama"><strong>Nama Lengkap <span class="text-danger">*</span></strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="nama" name="nama" 
                                                   placeholder="Nama lengkap Anda" 
                                                   value="<?= old('nama') ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="no_telp"><strong>No. Telepon</strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input type="tel" class="form-control" id="no_telp" name="no_telp" 
                                                   placeholder="08xxxxxxxxxx" 
                                                   value="<?= old('no_telp') ?>">
                                        </div>
                                        <small class="form-text text-muted">Untuk informasi pengiriman</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="alamat"><strong>Alamat Lengkap</strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            </div>
                                            <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"><?= old('alamat') ?></textarea>
                                        </div>
                                        <small class="form-text text-muted">Alamat untuk pengiriman barang</small>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="terms" required>
                                            <label class="custom-control-label" for="terms">
                                                Saya setuju dengan <a href="#" data-toggle="modal" data-target="#termsModal">Syarat & Ketentuan</a> 
                                                dan <a href="#" data-toggle="modal" data-target="#privacyModal">Kebijakan Privasi</a>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-secondary btn-block" id="prevStep2">
                                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-success btn-block" id="submitRegister">
                                                <i class="fas fa-check mr-2"></i> Daftar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-4">

                            <!-- Links -->
                            <div class="text-center">
                                <p class="mb-0">
                                    Sudah punya akun? 
                                    <a href="<?= base_url('auth/login') ?>" class="font-weight-bold">Login Sekarang</a>
                                </p>
                                <p class="mt-3">
                                    <a href="<?= base_url('dashboard') ?>" class="small text-muted">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Toko
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="card mt-3 bg-light">
                        <div class="card-body text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt text-success"></i> 
                                Data Anda aman dan terenkripsi
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms & Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Syarat & Ketentuan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>1. Penerimaan Syarat</h6>
                    <p>Dengan mendaftar di Toko Online, Anda menyetujui semua syarat dan ketentuan yang berlaku.</p>
                    
                    <h6>2. Akun Pengguna</h6>
                    <p>Anda bertanggung jawab untuk menjaga kerahasiaan password dan aktivitas akun Anda.</p>
                    
                    <h6>3. Pemesanan & Pembayaran</h6>
                    <p>Semua pesanan harus dibayar sesuai dengan metode pembayaran yang tersedia.</p>
                    
                    <h6>4. Pengiriman</h6>
                    <p>Waktu pengiriman tergantung pada lokasi dan ketersediaan produk.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kebijakan Privasi</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>Pengumpulan Data</h6>
                    <p>Kami mengumpulkan data pribadi yang Anda berikan saat registrasi untuk keperluan transaksi.</p>
                    
                    <h6>Penggunaan Data</h6>
                    <p>Data Anda digunakan untuk memproses pesanan, pengiriman, dan komunikasi terkait transaksi.</p>
                    
                    <h6>Keamanan Data</h6>
                    <p>Kami menggunakan enkripsi dan sistem keamanan untuk melindungi data pribadi Anda.</p>
                    
                    <h6>Berbagi Data</h6>
                    <p>Kami tidak akan membagikan data Anda kepada pihak ketiga tanpa izin Anda.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').click(function() {
                togglePasswordField('#password', $(this));
            });

            $('#togglePasswordConfirm').click(function() {
                togglePasswordField('#password_confirm', $(this));
            });

            function togglePasswordField(fieldId, button) {
                const field = $(fieldId);
                const icon = button.find('i');
                
                if (field.attr('type') === 'password') {
                    field.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    field.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            }

            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                let strength = 0;
                
                // Check length
                if (password.length >= 6) {
                    strength++;
                    $('#req-length').addClass('met');
                } else {
                    $('#req-length').removeClass('met');
                }
                
                // Check for letters
                if (/[a-zA-Z]/.test(password)) {
                    strength++;
                    $('#req-letter').addClass('met');
                } else {
                    $('#req-letter').removeClass('met');
                }
                
                // Check for numbers
                if (/\d/.test(password)) {
                    strength++;
                    $('#req-number').addClass('met');
                } else {
                    $('#req-number').removeClass('met');
                }
                
                // Update strength bar
                const strengthBar = $('#strengthBar');
                const strengthText = $('#strengthText');
                
                strengthBar.removeClass('strength-weak strength-medium strength-strong');
                
                if (strength === 1) {
                    strengthBar.addClass('strength-weak');
                    strengthText.text('Password lemah').css('color', '#e74a3b');
                } else if (strength === 2) {
                    strengthBar.addClass('strength-medium');
                    strengthText.text('Password sedang').css('color', '#f6c23e');
                } else if (strength === 3) {
                    strengthBar.addClass('strength-strong');
                    strengthText.text('Password kuat').css('color', '#1cc88a');
                } else {
                    strengthBar.css('width', '0');
                    strengthText.text('');
                }
            });

            // Check password match
            $('#password_confirm').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();
                
                if (confirmPassword && password !== confirmPassword) {
                    $('#passwordMatchError').removeClass('d-none');
                    $(this).addClass('is-invalid');
                } else {
                    $('#passwordMatchError').addClass('d-none');
                    $(this).removeClass('is-invalid');
                }
            });

            // Multi-step form navigation
            $('#nextStep1').click(function() {
                const email = $('#email').val();
                const password = $('#password').val();
                const confirmPassword = $('#password_confirm').val();
                
                // Validation
                if (!email || !password || !confirmPassword) {
                    alert('Mohon lengkapi semua field yang wajib diisi!');
                    return;
                }
                
                if (password !== confirmPassword) {
                    alert('Password tidak cocok!');
                    return;
                }
                
                if (password.length < 6) {
                    alert('Password minimal 6 karakter!');
                    return;
                }
                
                // Move to step 2
                $('#step1').removeClass('active');
                $('#step2').addClass('active');
                $('.step[data-step="1"]').addClass('completed');
                $('.step[data-step="2"]').addClass('active');
            });

            $('#prevStep2').click(function() {
                $('#step2').removeClass('active');
                $('#step1').addClass('active');
                $('.step[data-step="1"]').removeClass('completed');
                $('.step[data-step="2"]').removeClass('active');
            });

            // Form submission validation
            $('#registerForm').submit(function(e) {
                if (!$('#terms').is(':checked')) {
                    e.preventDefault();
                    alert('Anda harus menyetujui Syarat & Ketentuan!');
                    return false;
                }
            });

            // Phone number formatting
            $('#no_telp').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
                
                // Auto-add '08' prefix if starts with '8'
                if (value.length > 0 && value[0] === '8') {
                    value = '0' + value;
                }
                
                $(this).val(value);
            });
        });
    </script>
</body>
</html>