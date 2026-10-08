<?php
require_once __DIR__ . '/../config/config.php';
if (isset($_SESSION['user'])) {
    $redirect = $_SESSION['user']['role'] === 'admin' ? base_url('admin/dashboard.php') : base_url('pegawai/dashboard.php');
    header('Location: ' . $redirect);
    exit;
}
$message = flash('message');
?>
<?php include __DIR__ . '/../template_header.php'; ?>
<div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 200px);">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
            <div class="card-header bg-dark text-white text-center py-4 border-0 position-relative" style="background: linear-gradient(135deg, var(--primary), var(--secondary)) !important;">
                <div class="mb-2" style="font-size: 2.5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));"><i class="bi bi-fingerprint"></i></div>
                <h4 class="fw-bold mb-1">PT PROPSCODE</h4>
                <p class="text-white-50 small mb-0">Studio Teknologi - Sistem Absensi</p>
            </div>
            <div class="card-body p-4 p-sm-5">
                <h5 class="fw-bold text-center mb-4 text-dark">Silakan Login</h5>
                <?php if ($message) : ?>
                    <div class="alert alert-danger border-0 bg-danger-light text-danger small py-2.5 mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                <form action="proses_login.php" method="post">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Masukkan username" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2.5 fs-6"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk Aplikasi</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../template_footer.php'; ?>

