<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_SESSION['user']['id']);
$fullname = escape($_POST['fullname'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($fullname === '') {
    flash('message', 'Nama wajib diisi.');
    redirect(base_url('pegawai/profil/index.php'));
}
$query = "UPDATE users SET fullname = '$fullname', email = '$email', phone = '$phone'";
if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $query .= ", password = '$hash'";
}
$query .= " WHERE id = $id";
query($query);
$user = fetch_one("SELECT * FROM users WHERE id = $id");
$_SESSION['user'] = $user;
flash('message', 'Profil berhasil diperbarui.');
redirect(base_url('pegawai/profil/index.php'));
