<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';

$userId = intval($_SESSION['user']['id']);
$foto = $_POST['foto_masuk'] ?? '';
$lokasi = escape($_POST['lokasi_masuk'] ?? '');
$lat = escape($_POST['latitude_masuk'] ?? '');
$lng = escape($_POST['longitude_masuk'] ?? '');
$status = escape($_POST['status'] ?? 'Hadir');
$keterangan = escape($_POST['keterangan'] ?? '');
$date = date('Y-m-d');
$currentTime = date('H:i:s');

// Check if clock-in window has opened yet
if ($currentTime < START_TIME_MASUK) {
    $startTimeFormatted = date('H:i', strtotime(START_TIME_MASUK));
    flash('message', "Absensi masuk belum dibuka. Silakan melakukan absensi setelah pukul {$startTimeFormatted} WIB.");
    redirect(base_url('pegawai/absensi/absen_masuk.php'));
}

if ($status === 'Hadir') {
    // If status is Hadir, check if late
    if ($currentTime > LIMIT_TIME_MASUK) {
        flash('message', 'Batas waktu absen masuk telah berakhir. Anda tidak dapat melakukan absen Hadir.');
        redirect(base_url('pegawai/absensi/absen_masuk.php'));
    }
    
    if ($foto === '' || $lokasi === '') {
        flash('message', 'Silakan ambil foto dan lokasi terlebih dahulu.');
        redirect(base_url('pegawai/absensi/absen_masuk.php'));
    }
} else {
    // If status is Izin or Sakit, check if keterangan is empty
    if ($keterangan === '') {
        flash('message', 'Keterangan alasan tidak hadir wajib diisi.');
        redirect(base_url('pegawai/absensi/absen_masuk.php'));
    }
}

$existing = fetch_one("SELECT id FROM absensi WHERE user_id = $userId AND tanggal = '$date'");
if ($existing) {
    flash('message', 'anda telah absen hari ini');
    redirect(base_url('pegawai/dashboard.php'));
}

// Auto verify only if status is Hadir (on-time clock in)
$verified = ($status === 'Hadir') ? 1 : 0;

query("INSERT INTO absensi (user_id, tanggal, jam_masuk, lokasi_masuk, latitude_masuk, longitude_masuk, foto_masuk, status, verified, keterangan) 
       VALUES ($userId, '$date', CURTIME(), '$lokasi', '$lat', '$lng', '$foto', '$status', $verified, '$keterangan')");

if ($status === 'Hadir') {
    flash('message', 'Absensi masuk berhasil dicatat dan diverifikasi secara otomatis.');
} else {
    flash('message', 'Pengajuan ' . $status . ' berhasil dikirim, menunggu verifikasi administrator.');
}

redirect(base_url('pegawai/dashboard.php'));
