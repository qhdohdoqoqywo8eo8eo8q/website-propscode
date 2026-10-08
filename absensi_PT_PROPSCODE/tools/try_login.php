<?php
$site = 'http://127.0.0.1:8000/auth/proses_login.php';
$user = 'admin';
$pw_candidates = ['admin','admin123','password','123456','propscode','password123','secret','admin@123','12345678'];
foreach ($pw_candidates as $pw) {
    $opts = ['http' => ['method'=>'POST', 'header'=>"Content-Type: application/x-www-form-urlencoded\r\n", 'content'=>http_build_query(['username'=>$user,'password'=>$pw]), 'ignore_errors'=>true, 'timeout'=>10]];
    $ctx = stream_context_create($opts);
    $res = @file_get_contents($site, false, $ctx);
    echo "Tried: $pw\n";
    if (!empty($http_response_header)) {
        foreach ($http_response_header as $h) {
            echo "  $h\n";
            if (stripos($h, 'Location:') === 0) {
                echo "    -> Redirect to: " . trim(substr($h,9)) . "\n";
            }
        }
    }
    echo "---\n";
}
