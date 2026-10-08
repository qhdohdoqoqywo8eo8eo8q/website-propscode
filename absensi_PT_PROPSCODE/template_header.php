<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';
function active_menu($path) {
    return strpos($_SERVER['REQUEST_URI'], $path) !== false ? 'active' : '';
}
$is_admin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi PT PROPSCODE</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>?v=<?= time() ?>">
</head>
<body class="<?= $is_admin ? 'has-admin-sidebar' : '' ?>">

<?php if ($is_admin) : ?>
<!-- ========== ADMIN SIDEBAR ========== -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="<?= base_url('admin/dashboard.php') ?>" class="d-flex align-items-center text-decoration-none">
            <span class="brand-icon me-2"><i class="bi bi-fingerprint"></i></span>
            <span class="brand-text fw-bold text-white">PROPSCODE</span>
        </a>
        <button class="sidebar-close-btn d-lg-none" id="sidebarCloseBtn" aria-label="Close sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    
    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Menu Utama</div>
        <a href="<?= base_url('admin/dashboard.php') ?>" class="sidebar-link <?= active_menu('/admin/dashboard.php') ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <div class="sidebar-section-label">Data Master</div>
        <a href="<?= base_url('admin/admin/index.php') ?>" class="sidebar-link <?= active_menu('/admin/admin/') ?>">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Admin</span>
        </a>
        <a href="<?= base_url('admin/pegawai/index.php') ?>" class="sidebar-link <?= active_menu('/admin/pegawai/') ?>">
            <i class="bi bi-people-fill"></i>
            <span>Pegawai</span>
        </a>
        <a href="<?= base_url('admin/absensi/index.php') ?>" class="sidebar-link <?= active_menu('/admin/absensi/') ?>">
            <i class="bi bi-calendar-check-fill"></i>
            <span>Absensi</span>
        </a>

        <div class="sidebar-section-label">Laporan</div>
        <a href="<?= base_url('admin/laporan/harian.php') ?>" class="sidebar-link <?= active_menu('/laporan/harian') ?>">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Laporan Harian</span>
        </a>
        <a href="<?= base_url('admin/laporan/bulanan.php') ?>" class="sidebar-link <?= active_menu('/laporan/bulanan') ?>">
            <i class="bi bi-file-earmark-calendar"></i>
            <span>Laporan Bulanan</span>
        </a>
        <a href="<?= base_url('admin/laporan/tahunan.php') ?>" class="sidebar-link <?= active_menu('/laporan/tahunan') ?>">
            <i class="bi bi-file-earmark-post"></i>
            <span>Laporan Tahunan</span>
        </a>
        <a href="<?= base_url('admin/laporan/cetak_pdf.php') ?>" class="sidebar-link <?= active_menu('/laporan/cetak_pdf') ?>">
            <i class="bi bi-file-earmark-pdf"></i>
            <span>Cetak Semua</span>
        </a>

    </nav>

    <div class="sidebar-user-info dropup border-top border-secondary border-opacity-25 mt-auto">
        <div class="d-flex align-items-center dropdown-toggle cursor-pointer" id="sidebarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
            <div class="sidebar-avatar me-2">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="overflow-hidden me-auto">
                <div class="fw-semibold text-white small text-truncate"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></div>
                <div class="text-white-50" style="font-size: 0.7rem;">Administrator</div>
            </div>
            <i class="bi bi-chevron-up text-white-50 ms-2 small"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-dark shadow w-100" aria-labelledby="sidebarUserDropdown">
            <li>
                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="<?= base_url('admin/pengaturan/index.php') ?>">
                    <i class="bi bi-gear-fill me-2 text-white-50"></i>
                    <span>Sistem</span>
                </a>
            </li>
            <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
            <li>
                <a class="dropdown-item py-2 px-3 text-danger-subtle d-flex align-items-center" href="<?= base_url('auth/logout.php') ?>">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Admin Top Bar (slim) -->
<nav class="admin-topbar">
    <div class="d-flex align-items-center justify-content-between w-100 px-3 px-lg-4">
        <button class="sidebar-toggle-btn d-lg-none" id="sidebarToggleBtn" aria-label="Open sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="d-none d-lg-block"></div>
        <div class="d-flex align-items-center gap-3">
            <div class="topbar-badge d-none d-sm-flex align-items-center">
                <i class="bi bi-person-circle me-2"></i>
                <span class="small fw-semibold"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></span>
            </div>
            <a class="btn btn-outline-secondary btn-sm px-3 rounded-pill" href="<?= base_url('auth/logout.php') ?>">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="admin-main-content">
    <div class="container-fluid px-3 px-lg-4 py-4">

<?php else : ?>
<!-- ========== REGULAR NAVBAR (Pegawai / Guest) ========== -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top modern-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= base_url('') ?>">
            <span class="brand-icon me-2"><i class="bi bi-fingerprint"></i></span>
            <span class="brand-text fw-bold">PROPSCODE <span class="fw-light">Absensi</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user'])) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?= active_menu('/pegawai/dashboard.php') ?>" href="<?= base_url('pegawai/dashboard.php') ?>">
                                <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= active_menu('/pegawai/absensi/absen_masuk.php') ?>" href="<?= base_url('pegawai/absensi/absen_masuk.php') ?>">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Absen Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= active_menu('/pegawai/absensi/absen_pulang.php') ?>" href="<?= base_url('pegawai/absensi/absen_pulang.php') ?>">
                                <i class="bi bi-box-arrow-left me-1"></i> Absen Pulang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= active_menu('/pegawai/riwayat/index.php') ?>" href="<?= base_url('pegawai/riwayat/index.php') ?>">
                                <i class="bi bi-clock-history me-1"></i> Riwayat
                            </a>
                        </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user'])) : ?>
                    <div class="user-profile-badge d-flex align-items-center bg-dark-translucent text-white py-1 px-3 rounded-pill me-3 border border-secondary border-opacity-25">
                        <i class="bi bi-person-circle me-2 text-primary-light"></i>
                        <span class="small fw-semibold"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></span>
                    </div>
                    <a class="btn btn-outline-light btn-sm px-3 rounded-pill logout-btn" href="<?= base_url('auth/logout.php') ?>"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                <?php else: ?>
                    <a class="btn btn-light btn-sm px-3 rounded-pill login-btn" href="<?= base_url('auth/login.php') ?>"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container main-content py-4">
<?php endif; ?>
