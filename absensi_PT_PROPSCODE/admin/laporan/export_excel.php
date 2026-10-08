<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$type = $_GET['type'] ?? 'all';
$date = $_GET['date'] ?? date('Y-m-d');
$month = $_GET['month'] ?? date('Y-m');
$year = $_GET['year'] ?? date('Y');
$where = '1';
$label = 'Laporan Semua absensi';
$filename = 'laporan-absensi.xls';
if ($type === 'harian') {
    $where = "a.tanggal = '" . escape($date) . "'";
    $label = 'Laporan Harian: ' . htmlspecialchars($date);
    $filename = 'laporan-absensi-harian-' . $date . '.xls';
} elseif ($type === 'bulanan') {
    $where = "DATE_FORMAT(a.tanggal, '%Y-%m') = '" . escape($month) . "'";
    $label = 'Laporan Bulanan: ' . htmlspecialchars($month);
    $filename = 'laporan-absensi-bulanan-' . $month . '.xls';
} elseif ($type === 'tahunan') {
    $where = "DATE_FORMAT(a.tanggal, '%Y') = '" . escape($year) . "'";
    $label = 'Laporan Tahunan: ' . htmlspecialchars($year);
    $filename = 'laporan-absensi-tahunan-' . $year . '.xls';
}
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE $where ORDER BY a.tanggal DESC");
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
?>
<table border="1">
<tr>
    <th colspan="7"><?= $label ?></th>
</tr>
<tr>
    <th>#</th>
        <th>Pegawai</th>
        <th>Tanggal</th>
        <th>Masuk</th>
        <th>Pulang</th>
        <th>Status</th>
        <th>Verified</th>
    </tr>
    <?php foreach ($records as $index => $row) : ?>
    <tr>
        <td><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($row['fullname']) ?></td>
        <td><?= htmlspecialchars($row['tanggal']) ?></td>
        <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td><?= $row['verified'] ? 'Ya' : 'Belum' ?></td>
    </tr>
    <?php endforeach; ?>
</table>
