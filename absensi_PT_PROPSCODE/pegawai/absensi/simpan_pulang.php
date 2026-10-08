<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';
$userId = intval($_SESSION['user']['id']);
$foto = $_POST['foto_pulang'] ?? '';
$lokasi = escape($_POST['lokasi_pulang'] ?? '');
$lat = escape($_POST['latitude_pulang'] ?? '');
$lng = escape($_POST['longitude_pulang'] ?? '');
$date = date('Y-m-d');
$record = fetch_one("SELECT id, status, jam_pulang FROM absensi WHERE user_id = $userId AND tanggal = '$date'");
if (!$record) {
    flash('message', 'Anda harus melakukan Absen Masuk terlebih dahulu sebelum dapat mengakses Absen Pulang.');
    redirect(base_url('pegawai/dashboard.php'));
} elseif (in_array($record['status'], ['Izin', 'Sakit', 'Alpha'])) {
    $statusLabel = $record['status'] === 'Alpha' ? 'Tanpa Keterangan' : $record['status'];
    flash('message', 'Anda tidak perlu melakukan Absen Pulang karena status kehadiran Anda hari ini adalah ' . $statusLabel . '.');
    redirect(base_url('pegawai/dashboard.php'));
} elseif ($record['jam_pulang']) {
    flash('message', 'Anda telah melakukan absensi pulang hari ini.');
    redirect(base_url('pegawai/dashboard.php'));
}
if ($foto === '' || $lokasi === '') {
    flash('message', 'Silakan ambil foto dan lokasi terlebih dahulu.');
    redirect(base_url('pegawai/absensi/absen_pulang.php'));
}
query("UPDATE absensi SET jam_pulang = CURTIME(), lokasi_pulang = '$lokasi', latitude_pulang = '$lat', longitude_pulang = '$lng', foto_pulang = '$foto', verified = 1, updated_at = NOW() WHERE id = " . intval($record['id']));
flash('message', 'Absensi pulang berhasil dicatat.');
redirect(base_url('pegawai/dashboard.php'));
