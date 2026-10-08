<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../template_header.php';
$userId = intval($_SESSION['user']['id']);
$records = fetch_all("SELECT * FROM absensi WHERE user_id = $userId ORDER BY tanggal DESC, jam_masuk DESC");
?>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4 class="fw-bold text-dark mb-1">Riwayat Absensi</h4>
        <p class="text-muted small mb-3">Catatan lengkap riwayat absensi Anda.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                        <th>Verified</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $index => $row) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><i class="bi bi-calendar3 me-1 text-muted"></i> <?= htmlspecialchars($row['tanggal']) ?></td>
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
                            </td>
                            <td>
                                <?php if ($row['verified']) : ?>
                                    <span class="badge-pill-modern badge-verified-yes"><i class="bi bi-check-circle-fill"></i> Ya</span>
                                <?php else : ?>
                                    <span class="badge-pill-modern badge-verified-no"><i class="bi bi-exclamation-circle-fill"></i> Belum</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
