<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$type = $_GET['type'] ?? 'all';
$date = $_GET['date'] ?? date('Y-m-d');
$month = $_GET['month'] ?? date('Y-m');
$year = $_GET['year'] ?? date('Y');
$where = '1';
$label = 'Semua laporan';
if ($type === 'harian') {
    $where = "a.tanggal = '" . escape($date) . "'";
    $label = 'Laporan Harian: ' . htmlspecialchars($date);
} elseif ($type === 'bulanan') {
    $where = "DATE_FORMAT(a.tanggal, '%Y-%m') = '" . escape($month) . "'";
    $label = 'Laporan Bulanan: ' . htmlspecialchars($month);
} elseif ($type === 'tahunan') {
    $where = "DATE_FORMAT(a.tanggal, '%Y') = '" . escape($year) . "'";
    $label = 'Laporan Tahunan: ' . htmlspecialchars($year);
}
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE $where ORDER BY a.tanggal DESC");
?>
<div class="card shadow-sm">
    <div class="card-body">
        <h4>Laporan Absensi</h4>
        <p class="text-muted"><?= $label ?></p>
        <button onclick="window.print()" class="btn btn-outline-primary mb-3">Cetak</button>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pegawai</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $index => $row) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
