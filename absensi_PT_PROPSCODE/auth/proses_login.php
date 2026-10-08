<?php
require_once __DIR__ . '/../config/config.php';
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
if ($username === '' || $password === '') {
    flash('message', 'Username dan password wajib diisi.');
    redirect(base_url('auth/login.php'));
}
$sql = sprintf("SELECT * FROM users WHERE username = '%s' LIMIT 1", escape($username));
$user = fetch_one($sql);
if (!$user || !password_verify($password, $user['password'])) {
    flash('message', 'Username atau password salah.');
    redirect(base_url('auth/login.php'));
}
$_SESSION['user'] = $user;
$target = $user['role'] === 'admin' ? base_url('admin/dashboard.php') : base_url('pegawai/dashboard.php');
redirect($target);
