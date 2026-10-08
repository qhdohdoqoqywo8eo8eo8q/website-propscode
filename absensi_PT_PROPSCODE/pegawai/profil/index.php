<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../template_header.php';
$pegawai = $_SESSION['user'];
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4>Profil Pegawai</h4>
                <form action="<?= base_url('pegawai/profil/update.php') ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($pegawai['fullname']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($pegawai['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($pegawai['phone']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                    <button type="submit" class="btn btn-primary">Perbarui Profil</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
