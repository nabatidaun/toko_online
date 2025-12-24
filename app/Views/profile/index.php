<div class="container-fluid">
    <h4><i class="fas fa-user-circle"></i> Profile Saya</h4>
    <p class="text-muted">Kelola informasi profile Anda</p>

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

    <div class="row">
        <!-- Profile Picture -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-camera"></i> Foto Profile</h6>
                </div>
                <div class="card-body text-center">
                    <?php
                    $fotoProfile = $user['foto_profile'] ?? 'default.png';
                    $photoPath = base_url('uploads/profiles/' . $fotoProfile);
                    
                    // Cek apakah file ada, jika tidak gunakan default
                    if ($fotoProfile == 'default.png' || empty($fotoProfile)) {
                        $photoPath = base_url('assets/img/user.png');
                    }
                    ?>
                    
                    <img src="<?= $photoPath ?>" 
                         alt="Profile" 
                         class="img-fluid rounded-circle mb-3"
                         style="width: 200px; height: 200px; object-fit: cover; border: 5px solid #f8f9fc;"
                         id="profilePreview">
                    
                    <h5 class="mb-1"><?= esc($user['nama']) ?></h5>
                    <p class="text-muted mb-3">
                        <span class="badge badge-<?= $user['role'] == 'admin' ? 'danger' : 'primary' ?>">
                            <?= ucfirst(esc($user['role'])) ?>
                        </span>
                    </p>
                    
                    <!-- Form Upload Foto -->
                    <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <!-- Hidden fields untuk data lain -->
                        <input type="hidden" name="nama" value="<?= esc($user['nama']) ?>">
                        <input type="hidden" name="email" value="<?= esc($user['email']) ?>">
                        <input type="hidden" name="no_telp" value="<?= esc($user['no_telp'] ?? '') ?>">
                        <input type="hidden" name="alamat" value="<?= esc($user['alamat'] ?? '') ?>">
                        
                        <div class="custom-file mb-3">
                            <input type="file" 
                                   class="custom-file-input" 
                                   id="foto_profile" 
                                   name="foto_profile" 
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            <label class="custom-file-label" for="foto_profile">Pilih Foto</label>
                        </div>
                        
                        <small class="text-muted d-block mb-3">
                            Format: JPG, JPEG, PNG (Max: 2MB)
                        </small>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-upload"></i> Upload Foto
                        </button>
                    </form>
                    
                    <?php if (!empty($user['foto_profile']) && $user['foto_profile'] != 'default.png'): ?>
                        <a href="<?= base_url('profile/delete_photo') ?>" 
                           class="btn btn-danger btn-sm btn-block mt-2"
                           onclick="return confirm('Hapus foto profile?')">
                            <i class="fas fa-trash"></i> Hapus Foto
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="col-md-8">
            <!-- Form Edit Profile -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-edit"></i> Edit Informasi Profile</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('profile/update') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="nama"><strong>Nama Lengkap</strong> <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nama" 
                                   name="nama" 
                                   value="<?= esc($user['nama']) ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><strong>Email</strong> <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= esc($user['email']) ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="no_telp"><strong>No. Telepon</strong></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="no_telp" 
                                   name="no_telp" 
                                   value="<?= esc($user['no_telp'] ?? '') ?>"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat"><strong>Alamat Lengkap</strong></label>
                            <textarea class="form-control" 
                                      id="alamat" 
                                      name="alamat" 
                                      rows="4"
                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"><?= esc($user['alamat'] ?? '') ?></textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Alamat ini akan otomatis digunakan saat checkout
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </form>
                </div>
            </div>

            <!-- Form Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-key"></i> Ubah Password</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('profile/change_password') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="current_password"><strong>Password Lama</strong> <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password"><strong>Password Baru</strong> <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   required
                                   minlength="6">
                            <small class="form-text text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password"><strong>Konfirmasi Password Baru</strong> <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-lock"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Akun</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Status Akun</strong></td>
                            <td>
                                <span class="badge badge-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst(esc($user['status'])) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Terdaftar Sejak</strong></td>
                            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <?php if (!empty($user['last_login'])): ?>
                        <tr>
                            <td><strong>Login Terakhir</strong></td>
                            <td><?= date('d M Y H:i', strtotime($user['last_login'])) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image before upload
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
        
        // Update label
        const fileName = file.name;
        const label = event.target.nextElementSibling;
        label.textContent = fileName;
    }
}

// Validate password match
document.querySelector('form[action*="change_password"]').addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('Password baru dan konfirmasi password tidak cocok!');
        return false;
    }
});
</script>