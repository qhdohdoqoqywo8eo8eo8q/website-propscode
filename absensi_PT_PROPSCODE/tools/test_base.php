<?php
$_SERVER['HTTP_HOST'] = '127.0.0.1:8000';
$_SERVER['DOCUMENT_ROOT'] = 'C:/xampp3/htdocs';
require __DIR__ . '/../config/config.php';
echo base_url('auth/proses_login.php');
