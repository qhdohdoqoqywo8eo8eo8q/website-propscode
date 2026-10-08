<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$fullname = escape($_POST['fullname'] ?? '');
$username = escape($_POST['username'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($fullname === '' || $username === '' || $password === '') {
    flash('message', 'Silakan isi semua kolom yang wajib.');
    redirect(base_url('admin/admin/tambah.php'));
}
$hashed = password_hash($password, PASSWORD_DEFAULT);
query("INSERT INTO users (fullname, username, email, phone, password, role) VALUES ('$fullname', '$username', '$email', '$phone', '$hashed', 'admin')");
flash('message', 'Admin berhasil ditambahkan.');
redirect(base_url('admin/admin/index.php'));
