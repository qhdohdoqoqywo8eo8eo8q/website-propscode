<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'absensi_propscode';
$koneksi = mysqli_connect($host, $user, $password, $database);
if (!$koneksi) {
    die('Database connection failed: ' . mysqli_connect_error());
}
function query($sql) {
    global $koneksi;
    $result = mysqli_query($koneksi, $sql);
    if (!$result) {
        die('SQL Error: ' . mysqli_error($koneksi));
    }
    return $result;
}
function fetch_all($sql) {
    $result = query($sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
function fetch_one($sql) {
    $result = query($sql);
    return mysqli_fetch_assoc($result);
}
function escape($value) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, trim($value));
}
function redirect($url) {
    header('Location: ' . $url);
    exit;
}
