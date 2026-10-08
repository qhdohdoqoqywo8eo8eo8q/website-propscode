<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_POST['id'] ?? 0);
$fullname = escape($_POST['fullname'] ?? '');
$username = escape($_POST['username'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($id === 0 || $fullname === '' || $username === '') {
    flash('message', 'Data tidak lengkap.');
    redirect(base_url('admin/admin/index.php'));
}
$update = "UPDATE users SET fullname = '$fullname', username = '$username', email = '$email', phone = '$phone'";
if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $update .= ", password = '$hash'";
}
$update .= " WHERE id = $id AND role = 'admin'";
query($update);
flash('message', 'Admin berhasil diperbarui.');
redirect(base_url('admin/admin/index.php'));
