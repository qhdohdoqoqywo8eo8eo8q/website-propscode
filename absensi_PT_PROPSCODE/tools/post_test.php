<?php
$url = 'http://127.0.0.1:8000/auth/proses_login.php';
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query(['username'=>'','password'=>'']),
        'ignore_errors' => true,
    ],
];
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "HTTP response headers:\n";
foreach ($http_response_header as $h) echo $h . "\n";
echo "\nBody (truncated 1024 chars):\n" . substr($result,0,1024) . "\n";