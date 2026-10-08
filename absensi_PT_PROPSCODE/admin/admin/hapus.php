<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    query("DELETE FROM users WHERE id = $id AND role = 'admin'");
    flash('message', 'Admin dihapus.');
}
redirect(base_url('admin/admin/index.php'));
