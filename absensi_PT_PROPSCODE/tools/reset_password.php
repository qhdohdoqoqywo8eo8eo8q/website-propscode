<?php
require_once __DIR__ . '/../config/koneksi.php';
$username = 'pegawai1';
$new = 'pegawai123';
$hash = password_hash($new, PASSWORD_BCRYPT);
$sql = sprintf("UPDATE users SET password = '%s' WHERE username = '%s'", mysqli_real_escape_string($koneksi, $hash), mysqli_real_escape_string($koneksi, $username));
$res = mysqli_query($koneksi, $sql);
if ($res) {
    echo "Password for $username updated to $new\n";
} else {
    echo "Failed: " . mysqli_error($koneksi) . "\n";
}
