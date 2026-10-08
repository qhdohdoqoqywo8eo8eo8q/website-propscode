<?php
$loginUrl = 'http://127.0.0.1:8000/auth/proses_login.php';
$dashboardUrl = 'http://127.0.0.1:8000/admin/dashboard.php';
$cookieFile = sys_get_temp_dir() . '/absensi_cookie.txt';
// login
$ch = curl_init($loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username'=>'admin','password'=>'admin123']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$res = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
echo "Login request HTTP code: " . $info['http_code'] . "\n";
echo "Effective URL after redirects: " . $info['url'] . "\n\n";
// fetch dashboard
$ch = curl_init($dashboardUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$body = curl_exec($ch);
$info2 = curl_getinfo($ch);
curl_close($ch);
echo "Dashboard HTTP code: " . $info2['http_code'] . "\n";
$snippet = substr($body,0,1000);
echo "Dashboard body snippet:\n" . $snippet . "\n";
