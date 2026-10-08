<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';

$id = intval($_GET['id'] ?? 0);
$success = false;
$message = 'Gagal memproses verifikasi.';

if ($id > 0) {
    query("UPDATE absensi SET verified = 1 WHERE id = $id");
    $success = true;
    $message = 'Absensi berhasil diverifikasi.';
}

// Check if request is AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Fallback for non-AJAX requests
if ($success) {
    flash('message', $message);
}
redirect(base_url('admin/absensi/index.php'));
