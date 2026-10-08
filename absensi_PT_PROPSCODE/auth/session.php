<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function auth_admin() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../auth/login.php');
        exit;
    }
}
function auth_pegawai() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pegawai') {
        header('Location: ../auth/login.php');
        exit;
    }
}
