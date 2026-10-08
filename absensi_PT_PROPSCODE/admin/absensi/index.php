<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id ORDER BY a.tanggal DESC, a.jam_masuk DESC");
$total_records = count($records);

// Calculate Today's Attendance Rate Percentage
$total_pegawai = intval(fetch_one("SELECT COUNT(*) AS total FROM users WHERE role = 'pegawai'")['total']);
$hadir_today = intval(fetch_one("SELECT COUNT(*) AS total FROM absensi WHERE tanggal = CURDATE() AND status = 'Hadir'")['total']);
$attendance_percentage = $total_pegawai > 0 ? round(($hadir_today / $total_pegawai) * 100) : 0;
?>

<div class="row g-4 mb-4">
    <!-- Header Title & Description -->
    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-check-fill text-success me-2"></i>Data Absensi</h4>
            <p class="text-muted small mb-0">Kelola, verifikasi, dan pantau seluruh riwayat absensi masuk dan pulang pegawai.</p>
        </div>
    </div>

    <!-- Quick Stats Widgets -->
    <div class="col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white position-relative overflow-hidden" style="border-radius: 16px;">
            <div class="card-body p-4 position-relative" style="z-index: 2;">
                <h6 class="text-white-50 fw-semibold mb-1">Total Absensi Recorded</h6>
                <h2 class="fw-extrabold mb-0"><?= $total_records ?> <span class="fs-6 fw-normal opacity-75">Log</span></h2>
            </div>
            <div class="position-absolute opacity-10" style="bottom: -20px; right: -10px; font-size: 8rem; line-height: 1; pointer-events: none;">
                <i class="bi bi-calendar3"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm text-white position-relative overflow-hidden" style="border-radius: 16px; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
            <div class="card-body p-4 position-relative" style="z-index: 2;">
                <h6 class="text-white-50 fw-semibold mb-1">Persentase Kehadiran</h6>
                <h2 class="fw-extrabold mb-0"><?= $attendance_percentage ?>% <span class="fs-6 fw-normal opacity-75">Hari Ini</span></h2>
            </div>
            <div class="position-absolute opacity-10" style="bottom: -20px; right: -10px; font-size: 8rem; line-height: 1; pointer-events: none;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="w-100">
                    <h6 class="fw-bold text-dark mb-2">Cari Catatan Absen</h6>
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="attendanceSearchInput" class="form-control py-2.5 border-0 bg-light" placeholder="Masukkan nama, tanggal, status..." style="border-radius: 12px; font-size: 0.95rem;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Records Table Card -->
<div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white border-bottom py-3 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">Riwayat Catatan Absensi</h6>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 font-monospace small"><span id="filteredCount"><?= $total_records ?></span> / <?= $total_records ?> Terlihat</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 60px;">#</th>
                    <th>Pegawai</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Verifikasi</th>
                    <th class="pe-4 text-end" style="width: 220px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="attendanceTableBody">
                <?php if (empty($records)) : ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x d-block fs-1 mb-2 opacity-50"></i>
                            Belum ada data catatan absensi.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($records as $index => $row) : 
                        // Status pill colors
                        $statusClass = 'badge-hadir';
                        if ($row['status'] === 'Izin') $statusClass = 'badge-izin';
                        if ($row['status'] === 'Sakit') $statusClass = 'badge-sakit';
                        if ($row['status'] === 'Alpha') $statusClass = 'badge-alpha';

                        // Full status string for search filter matching
                        $verifiedText = $row['verified'] ? 'verified terverifikasi ya' : 'pending belum';
                    ?>
                        <tr class="attendance-row" data-search="<?= htmlspecialchars(strtolower($row['fullname'] . ' ' . $row['tanggal'] . ' ' . $row['status'] . ' ' . $verifiedText . ' ' . ($row['keterangan'] ?? ''))) ?>">
                            <td class="ps-4 text-muted font-monospace"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['fullname']) ?></div>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    <span><?= date('d M Y', strtotime($row['tanggal'])) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="small font-monospace">
                                    <i class="bi bi-box-arrow-in-right text-success me-1"></i>
                                    <?= htmlspecialchars($row['jam_masuk'] ?? '--:--') ?>
                                </span>
                            </td>
                            <td>
                                <span class="small font-monospace">
                                    <i class="bi bi-box-arrow-left text-danger me-1"></i>
                                    <?= htmlspecialchars($row['jam_pulang'] ?? '--:--') ?>
                                </span>
                            </td>
                            <td>
                                 <span class="badge-pill-modern <?= $statusClass ?>">
                                     <?= htmlspecialchars($row['status']) ?>
                                 </span>
                                 <?php if ($row['status'] !== 'Hadir' && !empty($row['keterangan'])) : ?>
                                     <div class="small text-muted mt-1" style="max-width: 200px; font-size: 0.75rem; line-height: 1.25;" title="<?= htmlspecialchars($row['keterangan']) ?>">
                                         <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($row['keterangan']) ?>
                                     </div>
                                 <?php endif; ?>
                            </td>
                            <td class="verification-badge-cell">
                                <?php if ($row['verified']) : ?>
                                    <span class="badge-pill-modern badge-verified-yes">
                                        <i class="bi bi-check-circle-fill"></i> Ya
                                    </span>
                                <?php else : ?>
                                    <span class="badge-pill-modern badge-verified-no">
                                        <i class="bi bi-exclamation-circle-fill"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end verification-actions-cell">
                                <div class="d-inline-flex gap-2">
                                    <a href="<?= base_url('admin/absensi/detail.php?id=' . $row['id']) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 py-1.5 px-3" style="border-radius: 8px;">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <?php if (!$row['verified']) : ?>
                                        <a href="#" data-id="<?= $row['id'] ?>" class="btn-ajax-verify btn btn-sm btn-success d-inline-flex align-items-center gap-1.5 py-1.5 px-3" style="border-radius: 8px;">
                                            <i class="bi bi-patch-check"></i>
                                            <span>Verifikasi</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('attendanceSearchInput');
    const tableBody = document.getElementById('attendanceTableBody');
    const rows = tableBody.getElementsByClassName('attendance-row');
    const filteredCount = document.getElementById('filteredCount');

    // Realtime search text filter
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const searchData = row.getAttribute('data-search');
            
            if (searchData.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
        
        filteredCount.textContent = visibleCount;
    });

    // AJAX Verification Handler
    tableBody.addEventListener('click', function(e) {
        const verifyBtn = e.target.closest('.btn-ajax-verify');
        if (verifyBtn) {
            e.preventDefault();
            const id = verifyBtn.getAttribute('data-id');
            const row = verifyBtn.closest('tr');
            const badgeCell = row.querySelector('.verification-badge-cell');
            
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
                    // Update badge
                    badgeCell.innerHTML = `<span class="badge-pill-modern badge-verified-yes"><i class="bi bi-check-circle-fill"></i> Ya</span>`;
                    
                    // Remove the verify button
                    verifyBtn.remove();
                    
                    // Update header counter
                    const counter = document.getElementById('pending-verification-counter');
                    if (counter) {
                        let currentCount = parseInt(counter.textContent);
                        if (currentCount > 0) {
                            counter.textContent = currentCount - 1;
                        }
                    }
                    
                    // Also update row data-search attribute so filter matches "verified" instead of "pending"
                    const newSearchData = row.getAttribute('data-search')
                        .replace('pending belum', 'verified terverifikasi ya');
                    row.setAttribute('data-search', newSearchData);
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

<?php include __DIR__ . '/../../template_footer.php'; ?>
