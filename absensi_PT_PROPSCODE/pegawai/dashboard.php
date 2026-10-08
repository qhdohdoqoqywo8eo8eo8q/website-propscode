<?php
require_once __DIR__ . '/../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../template_header.php';
$pegawai = $_SESSION['user'];
$userId = intval($pegawai['id']);
$today = date('Y-m-d');

// Fetch today's attendance record
$attendance = fetch_one("SELECT * FROM absensi WHERE user_id = $userId AND tanggal = '$today'");

// Fetch monthly statistics
$currentMonth = date('m');
$currentYear = date('Y');
$countHadir = fetch_one("SELECT COUNT(*) AS total FROM absensi WHERE user_id = $userId AND status = 'Hadir' AND MONTH(tanggal) = '$currentMonth' AND YEAR(tanggal) = '$currentYear'")['total'];
$countIzinSakit = fetch_one("SELECT COUNT(*) AS total FROM absensi WHERE user_id = $userId AND status IN ('Izin', 'Sakit') AND MONTH(tanggal) = '$currentMonth' AND YEAR(tanggal) = '$currentYear'")['total'];
$countAlpha = fetch_one("SELECT COUNT(*) AS total FROM absensi WHERE user_id = $userId AND status = 'Alpha' AND MONTH(tanggal) = '$currentMonth' AND YEAR(tanggal) = '$currentYear'")['total'];
?>

<?php 
$msg = flash('message');
if ($msg) : 
    $is_success = (strpos(strtolower($msg), 'berhasil') !== false || strpos(strtolower($msg), 'sukses') !== false);
    $alert_class = $is_success ? 'alert-success bg-success-light text-success' : 'alert-warning bg-warning-light text-warning';
    $icon_class = $is_success ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
?>
    <div class="alert <?= $alert_class ?> border-0 d-flex align-items-center justify-content-between mb-4" role="alert" style="border-radius: 12px;">
        <div class="d-flex align-items-center">
            <i class="bi <?= $icon_class ?> fs-4 me-2"></i>
            <div><?= htmlspecialchars($msg) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Welcome Banner & Live Clock -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3 mb-lg-0">
        <div class="card bg-dark text-white border-0 overflow-hidden shadow-sm h-100 position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 2; min-height: 120px; display: flex; flex-direction: column; justify-content: center;">
                <h3 class="fw-bold mb-1">Halo, <?= htmlspecialchars($pegawai['fullname']) ?>!</h3>
                <p class="text-white-50 mb-0">Selamat datang kembali di panel absensi. Jangan lupa absen masuk hari ini!</p>
            </div>
            <!-- Decorative background gradient glow -->
            <div class="position-absolute" style="top: -50px; right: -50px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, rgba(0,0,0,0) 70%); filter: blur(30px); pointer-events: none; z-index: 1;"></div>
        </div>
    </div>
    <div class="col-lg-4">
        <!-- Live Clock Widget -->
        <div class="clock-widget h-100 d-flex flex-column justify-content-center py-3">
            <div class="clock-time" id="live-time">00:00:00</div>
            <div class="clock-date text-white-50" id="live-date">Loading...</div>
        </div>
    </div>
</div>

<!-- Employee Stats Grid -->
<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card stat-card success h-100">
            <div class="card-body py-3.5">
                <div class="stat-icon"><i class="bi bi-calendar2-check"></i></div>
                <h6 class="text-muted fw-semibold">Hadir (Bulan Ini)</h6>
                <h2 class="fw-extrabold mb-0"><?= $countHadir ?> <span class="fs-6 fw-normal text-muted">Hari</span></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card stat-card info h-100">
            <div class="card-body py-3.5">
                <div class="stat-icon"><i class="bi bi-chat-left-heart"></i></div>
                <h6 class="text-muted fw-semibold">Izin & Sakit</h6>
                <h2 class="fw-extrabold mb-0"><?= $countIzinSakit ?> <span class="fs-6 fw-normal text-muted">Hari</span></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card danger h-100">
            <div class="card-body py-3.5">
                <div class="stat-icon"><i class="bi bi-calendar2-x"></i></div>
                <h6 class="text-muted fw-semibold">Alpha</h6>
                <h2 class="fw-extrabold mb-0 text-danger"><?= $countAlpha ?> <span class="fs-6 fw-normal text-muted">Hari</span></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Attendance Control Card -->
    <div class="col-lg-7 mb-4 mb-lg-0">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Status Absensi Hari Ini</h5>
                
                <?php if (!$attendance) : ?>
                    <div class="alert alert-warning border-0 bg-warning-light text-warning d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                        <div>Anda belum mengisi absensi masuk untuk hari ini.</div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('pegawai/absensi/absen_masuk.php') ?>" class="btn btn-primary py-3 fs-5">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Mulai Absen Masuk
                        </a>
                    </div>
                <?php elseif ($attendance && !$attendance['jam_pulang']) : ?>
                    <?php if ($attendance['status'] === 'Hadir') : ?>
                        <div class="alert alert-success border-0 bg-success-light text-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                            <div>Absen masuk Anda tercatat pada pukul <strong><?= htmlspecialchars($attendance['jam_masuk']) ?></strong>.</div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('pegawai/absensi/absen_pulang.php') ?>" class="btn btn-success py-3 fs-5">
                                <i class="bi bi-box-arrow-left me-2"></i> Lakukan Absen Pulang
                            </a>
                        </div>
                    <?php else : ?>
                        <?php 
                        $statusLabel = $attendance['status'];
                        if ($statusLabel === 'Alpha') {
                            $statusLabel = 'Tanpa Keterangan';
                        }
                        ?>
                        <div class="alert alert-info border-0 bg-info-light text-info d-flex align-items-center mb-0" role="alert" style="border-radius: 12px;">
                            <i class="bi bi-info-circle-fill fs-4 me-2"></i>
                            <div>Status absensi Anda hari ini: <strong><?= htmlspecialchars($statusLabel) ?></strong>. Anda tidak perlu melakukan absen pulang.</div>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="alert alert-info border-0 bg-info-light text-info d-flex align-items-center mb-0" role="alert">
                        <i class="bi bi-check-all fs-3 me-2"></i>
                        <div>
                            Absensi hari ini selesai!<br>
                            <span class="small">Masuk: <strong><?= htmlspecialchars($attendance['jam_masuk']) ?></strong> | Pulang: <strong><?= htmlspecialchars($attendance['jam_pulang']) ?></strong></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($attendance) : ?>
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-muted mb-2">Detail Kehadiran Hari Ini:</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Lokasi Masuk: <span class="text-dark fw-semibold"><?= htmlspecialchars($attendance['lokasi_masuk'] ?? '-') ?></span></li>
                            <?php if ($attendance['jam_pulang']) : ?>
                                <li><i class="bi bi-geo-alt text-muted me-1"></i> Lokasi Pulang: <span class="text-dark fw-semibold"><?= htmlspecialchars($attendance['lokasi_pulang'] ?? '-') ?></span></li>
                            <?php endif; ?>
                            <li class="mt-2">
                                <i class="bi bi-patch-check-fill text-primary me-1"></i> Verifikasi Admin: 
                                <?php if ($attendance['verified']) : ?>
                                    <span class="badge-pill-modern badge-verified-yes py-0.5"><i class="bi bi-check-circle-fill"></i> Sudah Terverifikasi</span>
                                <?php else : ?>
                                    <span class="badge-pill-modern badge-verified-no py-0.5"><i class="bi bi-exclamation-circle-fill"></i> Menunggu Verifikasi</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Options & Links -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold mb-3">Menu Pegawai</h5>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('pegawai/riwayat/index.php') ?>" class="btn btn-outline-primary text-start py-2.5">
                            <i class="bi bi-clock-history me-2"></i> Lihat Riwayat Absensi
                        </a>
                        <a href="<?= base_url('pegawai/profil/index.php') ?>" class="btn btn-outline-primary text-start py-2.5">
                            <i class="bi bi-person-gear me-2"></i> Pengaturan Profil Saya
                        </a>
                    </div>
                </div>
                
                <div class="bg-light p-3 rounded-12 mt-4 text-center">
                    <p class="small text-muted mb-0">PT PROPSCODE Studio Teknologi &copy; <?= date('Y') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateClock() {
        const now = new Date();
        
        // Time
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('live-time').textContent = `${hours}:${minutes}:${seconds}`;
        
        // Date
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('live-date').textContent = now.toLocaleDateString('id-ID', options);
    }
    
    updateClock();
    setInterval(updateClock, 1000);
});
</script>

<?php include __DIR__ . '/../template_footer.php'; ?>

