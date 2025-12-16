<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Toko Online</title>
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            max-width: 450px;
            width: 100%;
        }
        .brand-logo {
            font-size: 2.5rem;
            color: #4e73df;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card login-card shadow-lg border-0">
                        <div class="card-body p-5">
                            <!-- Logo/Brand -->
                            <div class="text-center mb-4">
                                <i class="fas fa-store brand-logo"></i>
                                <h4 class="font-weight-bold">TOKO ONLINE</h4>
                                <p class="text-muted">Silakan login untuk melanjutkan</p>
                            </div>

                            <!-- Alert Messages -->
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

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

                            <!-- Login Form -->
                            <form action="<?= base_url('auth/login_process') ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="form-group">
                                    <label for="email"><strong>Email</strong></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="Masukkan email Anda" 
                                               value="<?= old('email') ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password"><strong>Password</strong></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Masukkan password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                        <label class="custom-control-label" for="remember">Ingat Saya</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>
                            </form>

                            <hr class="my-4">

                            <!-- Links -->
                            <div class="text-center">
                                <p class="mb-2">
                                    <a href="<?= base_url('auth/forgot_password') ?>" class="small">Lupa Password?</a>
                                </p>
                                <p class="mb-0">
                                    Belum punya akun? 
                                    <a href="<?= base_url('auth/register') ?>" class="font-weight-bold">Daftar Sekarang</a>
                                </p>
                                <p class="mt-3">
                                    <a href="<?= base_url('dashboard') ?>" class="small text-muted">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Toko
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Demo Account Info -->
                    <div class="card mt-3 bg-info text-white">
                        <div class="card-body text-center">
                            <small>
                                <strong>Demo Account:</strong><br>
                                Admin: admin@toko.com / admin123<br>
                                User: user@toko.com / user123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>