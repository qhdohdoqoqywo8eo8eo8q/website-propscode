<?php
require_once __DIR__ . '/../config/config.php';
session_destroy();
redirect(base_url('auth/login.php'));
