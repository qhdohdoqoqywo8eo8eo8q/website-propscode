<?php
require_once __DIR__ . '/../auth/session.php';
auth_admin();
require_once __DIR__ . '/../template_header.php';

// Fetch Live Statistics
$countAdmin = fetch_one("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'")['total'];
$countPegawai = fetch_one("SELECT COUNT(*) AS total FROM users WHERE role = 'pegawai'")['total'];
$countAbsenToday = fetch_one("SELECT COUNT(*) AS total FROM absensi WHERE tanggal = CURDATE()")['total'];
$countAbsensi = fetch_one("SELECT COUNT(*) AS total FROM absensi")['total'];

// Fetch Recent Attendance Today
$recentAttendance = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE a.tanggal = CURDATE() ORDER BY a.jam_masuk DESC LIMIT 5");
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-dark text-white border-0 overflow-hidden shadow-sm position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 2;">
                <h3 class="fw-bold mb-1">Dashboard Admin</h3>
                <p class="text-white-50 mb-0">Selamat datang kembali di pusat manajemen absensi PT PROPSCODE Studio Teknologi.</p>
            </div>
            <!-- Decorative background gradient glow -->
            <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(79, 70, 229, 0.4) 0%, rgba(0,0,0,0) 70%); filter: blur(30px); pointer-events: none; z-index: 1;"></div>
        </div>
    </div>
</div>

<!-- Dynamic Live Stats Cards -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="card stat-card primary h-100" onclick="location.href='<?= base_url('admin/admin/index.php') ?>'">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-shield-lock"></i></div>
                <h6 class="text-muted fw-semibold">Administrator</h6>
                <h2 class="fw-extrabold mb-0"><?= $countAdmin ?> <span class="fs-6 fw-normal text-muted">Akun</span></h2>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="card stat-card success h-100" onclick="location.href='<?= base_url('admin/pegawai/index.php') ?>'">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <h6 class="text-muted fw-semibold">Total Pegawai</h6>
                <h2 class="fw-extrabold mb-0"><?= $countPegawai ?> <span class="fs-6 fw-normal text-muted">Orang</span></h2>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="card stat-card info h-100" onclick="location.href='<?= base_url('admin/absensi/index.php') ?>'">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                <h6 class="text-muted fw-semibold">Hadir Hari Ini</h6>
                <h2 class="fw-extrabold mb-0"><?= $countAbsenToday ?> <span class="fs-6 fw-normal text-muted">Absen</span></h2>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="card stat-card warning h-100" onclick="location.href='<?= base_url('admin/absensi/index.php') ?>'">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
                <h6 class="text-muted fw-semibold">Data Absensi</h6>
                <h2 class="fw-extrabold mb-0 text-warning"><?= $countAbsensi ?> <span class="fs-6 fw-normal text-muted">Log</span></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Live Attendance Feed -->
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Absensi Hari Ini</h5>
                    <span class="badge bg-primary-light text-primary rounded-pill px-2 py-1 small">Terbaru</span>
                </div>
                <div class="table-responsive">
                    <?php if (empty($recentAttendance)) : ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clipboard2-x d-block fs-1 mb-2"></i>
                            Belum ada aktivitas absensi masuk hari ini.
                        </div>
                    <?php else : ?>
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Status</th>
                                    <th>Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAttendance as $row) : ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($row['fullname']) ?></div>
                                        </td>
                                        <td><i class="bi bi-clock me-1 text-primary"></i> <?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                                        <td><i class="bi bi-clock-history me-1 text-muted"></i> <?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                                        <td>
                                            <?php 
                                            $statusClass = 'badge-hadir';
                                            if ($row['status'] === 'Izin') $statusClass = 'badge-izin';
                                            if ($row['status'] === 'Sakit') $statusClass = 'badge-sakit';
                                            if ($row['status'] === 'Alpha') $statusClass = 'badge-alpha';
                                            ?>
                                            <span class="badge-pill-modern <?= $statusClass ?>"><?= htmlspecialchars($row['status']) ?></span>
                                            <?php if ($row['status'] !== 'Hadir' && !empty($row['keterangan'])) : ?>
                                                <div class="small text-muted mt-1" style="max-width: 180px; font-size: 0.75rem; line-height: 1.25;" title="<?= htmlspecialchars($row['keterangan']) ?>">
                                                    <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($row['keterangan']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="verification-cell">
                                            <?php if ($row['verified']) : ?>
                                                <span class="badge-pill-modern badge-verified-yes"><i class="bi bi-check-circle-fill"></i> Ok</span>
                                            <?php else : ?>
                                                <a href="#" data-id="<?= $row['id'] ?>" class="btn-ajax-verify badge-pill-modern badge-verified-no text-decoration-none"><i class="bi bi-exclamation-circle-fill"></i> Verifikasi</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & Links -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Akses Cepat</h5>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/laporan/harian.php') ?>" class="btn btn-outline-primary text-start py-2.5">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Harian
                    </a>
                    <a href="<?= base_url('admin/laporan/bulanan.php') ?>" class="btn btn-outline-primary text-start py-2.5">
                        <i class="bi bi-file-earmark-calendar me-2"></i> Laporan Bulanan
                    </a>
                    <a href="<?= base_url('admin/laporan/tahunan.php') ?>" class="btn btn-outline-primary text-start py-2.5">
                        <i class="bi bi-file-earmark-post me-2"></i> Laporan Tahunan
                    </a>
                    <hr class="my-2 opacity-50">
                    <a href="<?= base_url('admin/pengaturan/index.php') ?>" class="btn btn-outline-secondary text-start py-2.5">
                        <i class="bi bi-gear me-2"></i> Pengaturan Sistem
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        const verifyBtn = e.target.closest('.btn-ajax-verify');
        if (verifyBtn) {
            e.preventDefault();
            const id = verifyBtn.getAttribute('data-id');
            const cell = verifyBtn.closest('.verification-cell');
            
            // Disable button during request
            verifyBtn.style.pointerEvents = 'none';
            verifyBtn.style.opacity = '0.6';
            
            fetch('<?= base_url("admin/absensi/verifikasi.php") ?>?id=' + id, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update verification status cell
                    cell.innerHTML = `<span class="badge-pill-modern badge-verified-yes"><i class="bi bi-check-circle-fill"></i> Ok</span>`;
                    
                    // Update dashboard stat counter
                    const counter = document.getElementById('pending-counter');
                    if (counter) {
                        let currentCount = parseInt(counter.textContent);
                        if (currentCount > 0) {
                            counter.textContent = currentCount - 1;
                        }
                    }
                } else {
                    alert(data.message || 'Gagal memproses verifikasi.');
                    verifyBtn.style.pointerEvents = '';
                    verifyBtn.style.opacity = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
                verifyBtn.style.pointerEvents = '';
                verifyBtn.style.opacity = '';
            });
        }
    });
});
</script>

<?php include __DIR__ . '/../template_footer.php'; ?>
