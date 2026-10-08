<?php
date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/koneksi.php';

function get_settings() {
    $file = __DIR__ . '/settings.json';
    $defaults = [
        'office_latitude' => -6.2088,
        'office_longitude' => 106.8456,
        'office_radius_meters' => 100,
        'start_time_masuk' => '08:00:00',
        'limit_time_masuk' => '09:00:00'
    ];
    if (!file_exists($file)) {
        return $defaults;
    }
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return array_merge($defaults, $data ? $data : []);
}

function save_settings($data) {
    $file = __DIR__ . '/settings.json';
    $current = get_settings();
    $new_data = array_merge($current, $data);
    return file_put_contents($file, json_encode($new_data, JSON_PRETTY_PRINT));
}

function get_distance_meters($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371000; // in meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c;
}

// Load settings into constants
$sys_settings = get_settings();
define('OFFICE_LATITUDE', floatval($sys_settings['office_latitude']));
define('OFFICE_LONGITUDE', floatval($sys_settings['office_longitude']));
define('OFFICE_RADIUS_METERS', intval($sys_settings['office_radius_meters']));
define('START_TIME_MASUK', $sys_settings['start_time_masuk']);
define('LIMIT_TIME_MASUK', $sys_settings['limit_time_masuk']);

function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
    $appRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $root = '';
    if ($docRoot !== '' && strpos($appRoot, $docRoot) === 0) {
        $root = '/' . trim(substr($appRoot, strlen($docRoot)), '/');
    }
    return rtrim($protocol . $host . $root, '/') . '/' . ltrim($path, '/');
}
function asset_url($path = '') {
    return base_url('assets/' . ltrim($path, '/'));
}
function flash($name, $message = null) {
    if ($message !== null) {
        $_SESSION[$name] = $message;
        return true;
    }
    if (isset($_SESSION[$name])) {
        $msg = $_SESSION[$name];
        unset($_SESSION[$name]);
        return $msg;
    }
    return null;
}
function old($key) {
    return $_POST[$key] ?? '';
}
function is_logged_in() {
    return isset($_SESSION['user']);
}
