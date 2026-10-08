import os

files = {
    'config/config.php': """<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/koneksi.php';
function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = str_replace('\\\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $root = rtrim($script, '/');
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
""",
    'config/koneksi.php': """<?php
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
""",
    'template_header.php': """<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';
function active_menu($path) {
    return strpos($_SERVER['REQUEST_URI'], $path) !== false ? 'active' : '';
}
?>
<!doctype html>
<html lang=\"en\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>Absensi PT PROPSCODE</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"<?= asset_url('css/style.css') ?>\">
</head>
<body class=\"bg-light\">
<nav class=\"navbar navbar-expand-lg navbar-dark bg-primary\">
    <div class=\"container-fluid\">
        <a class=\"navbar-brand\" href=\"<?= base_url('') ?>\">PROPSCODE Absensi</a>
        <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#topNav\" aria-controls=\"topNav\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
            <span class=\"navbar-toggler-icon\"></span>
        </button>
        <div class=\"collapse navbar-collapse\" id=\"topNav\">
            <ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">
                <?php if (isset($_SESSION['user'])) : ?>
                    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                        <li class=\"nav-item\"><a class=\"nav-link <?= active_menu('/admin/dashboard.php') ?>\" href=\"<?= base_url('admin/dashboard.php') ?>\">Dashboard</a></li>
                        <li class=\"nav-item dropdown\">
                            <a class=\"nav-link dropdown-toggle\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\">Master</a>
                            <ul class=\"dropdown-menu\">
                                <li><a class=\"dropdown-item\" href=\"<?= base_url('admin/admin/index.php') ?>\">Admin</a></li>
                                <li><a class=\"dropdown-item\" href=\"<?= base_url('admin/pegawai/index.php') ?>\">Pegawai</a></li>
                                <li><a class=\"dropdown-item\" href=\"<?= base_url('admin/absensi/index.php') ?>\">Absensi</a></li>
                                <li><a class=\"dropdown-item\" href=\"<?= base_url('admin/laporan/harian.php') ?>\">Laporan</a></li>
                                <li><a class=\"dropdown-item\" href=\"<?= base_url('admin/pengaturan/index.php') ?>\">Pengaturan</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class=\"nav-item\"><a class=\"nav-link <?= active_menu('/pegawai/dashboard.php') ?>\" href=\"<?= base_url('pegawai/dashboard.php') ?>\">Dashboard</a></li>
                        <li class=\"nav-item\"><a class=\"nav-link <?= active_menu('/pegawai/absensi/absen_masuk.php') ?>\" href=\"<?= base_url('pegawai/absensi/absen_masuk.php') ?>\">Absensi Masuk</a></li>
                        <li class=\"nav-item\"><a class=\"nav-link <?= active_menu('/pegawai/absensi/absen_pulang.php') ?>\" href=\"<?= base_url('pegawai/absensi/absen_pulang.php') ?>\">Absensi Pulang</a></li>
                        <li class=\"nav-item\"><a class=\"nav-link <?= active_menu('/pegawai/riwayat/index.php') ?>\" href=\"<?= base_url('pegawai/riwayat/index.php') ?>\">Riwayat</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class=\"d-flex align-items-center\">
                <?php if (isset($_SESSION['user'])) : ?>
                    <span class=\"text-white me-3\">Halo, <?= htmlspecialchars($_SESSION['user']['fullname']) ?></span>
                    <a class=\"btn btn-outline-light btn-sm\" href=\"<?= base_url('auth/logout.php') ?>\">Logout</a>
                <?php else: ?>
                    <a class=\"btn btn-light btn-sm\" href=\"<?= base_url('auth/login.php') ?>\">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class=\"container py-4\">
""",
    'template_footer.php': """    </div>
<footer class=\"bg-white border-top py-3 mt-4\">
    <div class=\"container text-center text-muted\">&copy; <?= date('Y') ?> PT PROPSCODE Studio Teknologi</div>
</footer>
<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js\"></script>
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js\"></script>
<script src=\"<?= asset_url('js/gps.js') ?>\"></script>
<script src=\"<?= asset_url('js/script.js') ?>\"></script>
</body>
</html>
""",
    'auth/session.php': """<?php
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
""",
    'auth/login.php': """<?php
require_once __DIR__ . '/../config/config.php';
if (isset($_SESSION['user'])) {
    $redirect = $_SESSION['user']['role'] === 'admin' ? base_url('admin/dashboard.php') : base_url('pegawai/dashboard.php');
    header('Location: ' . $redirect);
    exit;
}
$message = flash('message');
?>
<?php include __DIR__ . '/../template_header.php'; ?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-5\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4 class=\"card-title mb-4\">Login Absensi</h4>
                <?php if ($message) : ?>
                    <div class=\"alert alert-warning\"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <form action=\"<?= base_url('auth/proses_login.php') ?>\" method=\"post\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Username</label>
                        <input type=\"text\" name=\"username\" class=\"form-control\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Password</label>
                        <input type=\"password\" name=\"password\" class=\"form-control\" required>
                    </div>
                    <button type=\"submit\" class=\"btn btn-primary w-100\">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../template_footer.php'; ?>
""",
    'auth/proses_login.php': """<?php
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
""",
    'auth/logout.php': """<?php
require_once __DIR__ . '/../config/config.php';
session_destroy();
redirect(base_url('auth/login.php'));
""",
    'admin/dashboard.php': """<?php
require_once __DIR__ . '/../auth/session.php';
auth_admin();
require_once __DIR__ . '/../template_header.php';
?>
<div class=\"row\">
    <div class=\"col-lg-12\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h3>Dashboard Admin</h3>
                <p>Selamat datang di sistem absensi PT PROPSCODE Studio Teknologi.</p>
                <div class=\"row\">
                    <div class=\"col-md-4\">
                        <div class=\"card border-primary mb-3\">
                            <div class=\"card-body\">
                                <h5 class=\"card-title\">Admin</h5>
                                <p class=\"card-text\">Kelola akun administrator.</p>
                                <a href=\"<?= base_url('admin/admin/index.php') ?>\" class=\"btn btn-primary btn-sm\">Lihat Admin</a>
                            </div>
                        </div>
                    </div>
                    <div class=\"col-md-4\">
                        <div class=\"card border-success mb-3\">
                            <div class=\"card-body\">
                                <h5 class=\"card-title\">Pegawai</h5>
                                <p class=\"card-text\">Kelola data pegawai dan absensi.</p>
                                <a href=\"<?= base_url('admin/pegawai/index.php') ?>\" class=\"btn btn-success btn-sm\">Lihat Pegawai</a>
                            </div>
                        </div>
                    </div>
                    <div class=\"col-md-4\">
                        <div class=\"card border-warning mb-3\">
                            <div class=\"card-body\">
                                <h5 class=\"card-title\">Absensi</h5>
                                <p class=\"card-text\">Verifikasi dan laporan absensi.</p>
                                <a href=\"<?= base_url('admin/absensi/index.php') ?>\" class=\"btn btn-warning btn-sm\">Lihat Absensi</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../template_footer.php'; ?>
""",
    'admin/admin/index.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$admins = fetch_all("SELECT * FROM users WHERE role = 'admin' ORDER BY created_at DESC");
?>
<div class=\"d-flex justify-content-between align-items-center mb-3\">
    <div>
        <h4>Data Admin</h4>
        <p class=\"text-muted\">Kelola akun administrator.</p>
    </div>
    <a href=\"<?= base_url('admin/admin/tambah.php') ?>\" class=\"btn btn-primary\">Tambah Admin</a>
</div>
<div class=\"card shadow-sm\">
    <div class=\"card-body table-responsive\">
        <table class=\"table table-bordered table-hover mb-0\">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $index => $admin) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($admin['fullname']) ?></td>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                        <td><?= htmlspecialchars($admin['phone']) ?></td>
                        <td><?= htmlspecialchars($admin['created_at']) ?></td>
                        <td>
                            <a href=\"<?= base_url('admin/admin/edit.php?id=' . $admin['id']) ?>\" class=\"btn btn-sm btn-warning\">Edit</a>
                            <a href=\"<?= base_url('admin/admin/hapus.php?id=' . $admin['id']) ?>\" class=\"btn btn-sm btn-danger\" onclick=\"return confirm('Hapus admin ini?')\">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/admin/tambah.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-7\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Tambah Admin</h4>
                <form action=\"<?= base_url('admin/admin/simpan.php') ?>\" method=\"post\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Nama Lengkap</label>
                        <input type=\"text\" name=\"fullname\" class=\"form-control\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Username</label>
                        <input type=\"text\" name=\"username\" class=\"form-control\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Email</label>
                        <input type=\"email\" name=\"email\" class=\"form-control\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Telepon</label>
                        <input type=\"text\" name=\"phone\" class=\"form-control\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Password</label>
                        <input type=\"password\" name=\"password\" class=\"form-control\" required>
                    </div>
                    <button type=\"submit\" class=\"btn btn-primary\">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/admin/simpan.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$fullname = escape($_POST['fullname'] ?? '');
$username = escape($_POST['username'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($fullname === '' || $username === '' || $password === '') {
    flash('message', 'Silakan isi semua kolom yang wajib.');
    redirect(base_url('admin/admin/tambah.php'));
}
$hashed = password_hash($password, PASSWORD_DEFAULT);
query("INSERT INTO users (fullname, username, email, phone, password, role) VALUES ('$fullname', '$username', '$email', '$phone', '$hashed', 'admin')");
flash('message', 'Admin berhasil ditambahkan.');
redirect(base_url('admin/admin/index.php'));
""",
    'admin/admin/edit.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_GET['id'] ?? 0);
$admin = fetch_one("SELECT * FROM users WHERE id = $id AND role = 'admin'");
if (!$admin) {
    redirect(base_url('admin/admin/index.php'));
}
require_once __DIR__ . '/../../template_header.php';
?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-7\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Edit Admin</h4>
                <form action=\"<?= base_url('admin/admin/update.php') ?>\" method=\"post\">
                    <input type=\"hidden\" name=\"id\" value=\"<?= $admin['id'] ?>\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Nama Lengkap</label>
                        <input type=\"text\" name=\"fullname\" class=\"form-control\" value=\"<?= htmlspecialchars($admin['fullname']) ?>\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Username</label>
                        <input type=\"text\" name=\"username\" class=\"form-control\" value=\"<?= htmlspecialchars($admin['username']) ?>\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Email</label>
                        <input type=\"email\" name=\"email\" class=\"form-control\" value=\"<?= htmlspecialchars($admin['email']) ?>\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Telepon</label>
                        <input type=\"text\" name=\"phone\" class=\"form-control\" value=\"<?= htmlspecialchars($admin['phone']) ?>\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Password Baru (biarkan kosong jika tidak diubah)</label>
                        <input type=\"password\" name=\"password\" class=\"form-control\">
                    </div>
                    <button type=\"submit\" class=\"btn btn-warning\">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/admin/update.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_POST['id'] ?? 0);
$fullname = escape($_POST['fullname'] ?? '');
$username = escape($_POST['username'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($id === 0 || $fullname === '' || $username === '') {
    flash('message', 'Data tidak lengkap.');
    redirect(base_url('admin/admin/index.php'));
}
$update = "UPDATE users SET fullname = '$fullname', username = '$username', email = '$email', phone = '$phone'";
if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $update .= ", password = '$hash'";
}
$update .= " WHERE id = $id AND role = 'admin'";
query($update);
flash('message', 'Admin berhasil diperbarui.');
redirect(base_url('admin/admin/index.php'));
""",
    'admin/admin/hapus.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    query("DELETE FROM users WHERE id = $id AND role = 'admin'");
    flash('message', 'Admin dihapus.');
}
redirect(base_url('admin/admin/index.php'));
""",
    'admin/pegawai/index.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$employees = fetch_all("SELECT * FROM users WHERE role = 'pegawai' ORDER BY created_at DESC");
?>
<div class=\"d-flex justify-content-between align-items-center mb-3\">
    <div>
        <h4>Data Pegawai</h4>
        <p class=\"text-muted\">Kelola data karyawan untuk absensi.</p>
    </div>
    <a href=\"<?= base_url('admin/pegawai/tambah.php') ?>\" class=\"btn btn-success\">Tambah Pegawai</a>
</div>
<div class=\"card shadow-sm\">
    <div class=\"card-body table-responsive\">
        <table class=\"table table-bordered table-hover mb-0\">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $index => $employee) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($employee['fullname']) ?></td>
                        <td><?= htmlspecialchars($employee['username']) ?></td>
                        <td><?= htmlspecialchars($employee['email']) ?></td>
                        <td><?= htmlspecialchars($employee['phone']) ?></td>
                        <td><?= htmlspecialchars($employee['created_at']) ?></td>
                        <td>
                            <a href=\"<?= base_url('admin/pegawai/edit.php?id=' . $employee['id']) ?>\" class=\"btn btn-sm btn-warning\">Edit</a>
                            <a href=\"<?= base_url('admin/pegawai/hapus.php?id=' . $employee['id']) ?>\" class=\"btn btn-sm btn-danger\" onclick=\"return confirm('Hapus pegawai ini?')\">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/pegawai/tambah.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-7\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Tambah Pegawai</h4>
                <form action=\"<?= base_url('admin/pegawai/simpan.php') ?>\" method=\"post\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Nama Lengkap</label>
                        <input type=\"text\" name=\"fullname\" class=\"form-control\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Username</label>
                        <input type=\"text\" name=\"username\" class=\"form-control\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Email</label>
                        <input type=\"email\" name=\"email\" class=\"form-control\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Telepon</label>
                        <input type=\"text\" name=\"phone\" class=\"form-control\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Password</label>
                        <input type=\"password\" name=\"password\" class=\"form-control\" required>
                    </div>
                    <button type=\"submit\" class=\"btn btn-success\">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/pegawai/simpan.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$fullname = escape($_POST['fullname'] ?? '');
$username = escape($_POST['username'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($fullname === '' || $username === '' || $password === '') {
    flash('message', 'Silakan isi semua kolom yang wajib.');
    redirect(base_url('admin/pegawai/tambah.php'));
}
$hashed = password_hash($password, PASSWORD_DEFAULT);
query("INSERT INTO users (fullname, username, email, phone, password, role) VALUES ('$fullname', '$username', '$email', '$phone', '$hashed', 'pegawai')");
flash('message', 'Pegawai berhasil ditambahkan.');
redirect(base_url('admin/pegawai/index.php'));
""",
    'admin/pegawai/edit.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_GET['id'] ?? 0);
$employee = fetch_one("SELECT * FROM users WHERE id = $id AND role = 'pegawai'");
if (!$employee) {
    redirect(base_url('admin/pegawai/index.php'));
}
require_once __DIR__ . '/../../template_header.php';
?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-7\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Edit Pegawai</h4>
                <form action=\"<?= base_url('admin/pegawai/update.php') ?>\" method=\"post\">
                    <input type=\"hidden\" name=\"id\" value=\"<?= $employee['id'] ?>\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Nama Lengkap</label>
                        <input type=\"text\" name=\"fullname\" class=\"form-control\" value=\"<?= htmlspecialchars($employee['fullname']) ?>\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Username</label>
                        <input type=\"text\" name=\"username\" class=\"form-control\" value=\"<?= htmlspecialchars($employee['username']) ?>\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Email</label>
                        <input type=\"email\" name=\"email\" class=\"form-control\" value=\"<?= htmlspecialchars($employee['email']) ?>\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Telepon</label>
                        <input type=\"text\" name=\"phone\" class=\"form-control\" value=\"<?= htmlspecialchars($employee['phone']) ?>\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Password Baru (biarkan kosong jika tidak diubah)</label>
                        <input type=\"password\" name=\"password\" class=\"form-control\">
                    </div>
                    <button type=\"submit\" class=\"btn btn-warning\">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/pegawai/update.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_POST['id'] ?? 0);
$fullname = escape($_POST['fullname'] ?? '');
$username = escape($_POST['username'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($id === 0 || $fullname === '' || $username === '') {
    flash('message', 'Data tidak lengkap.');
    redirect(base_url('admin/pegawai/index.php'));
}
$update = "UPDATE users SET fullname = '$fullname', username = '$username', email = '$email', phone = '$phone'";
if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $update .= ", password = '$hash'";
}
$update .= " WHERE id = $id AND role = 'pegawai'";
query($update);
flash('message', 'Pegawai berhasil diperbarui.');
redirect(base_url('admin/pegawai/index.php'));
""",
    'admin/pegawai/hapus.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    query("DELETE FROM users WHERE id = $id AND role = 'pegawai'");
    flash('message', 'Pegawai dihapus.');
}
redirect(base_url('admin/pegawai/index.php'));
""",
    'admin/absensi/index.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id ORDER BY a.tanggal DESC, a.jam_masuk DESC");
?>
<div class=\"d-flex justify-content-between align-items-center mb-3\">
    <div>
        <h4>Data Absensi</h4>
        <p class=\"text-muted\">Catatan absensi pegawai.</p>
    </div>
</div>
<div class=\"card shadow-sm\">
    <div class=\"card-body table-responsive\">
        <table class=\"table table-bordered table-hover mb-0\">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pegawai</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $index => $row) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['verified'] ? 'Ya' : 'Belum' ?></td>
                        <td>
                            <a href=\"<?= base_url('admin/absensi/detail.php?id=' . $row['id']) ?>\" class=\"btn btn-sm btn-primary\">Detail</a>
                            <a href=\"<?= base_url('admin/absensi/verifikasi.php?id=' . $row['id']) ?>\" class=\"btn btn-sm btn-success\">Verifikasi</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/absensi/detail.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$id = intval($_GET['id'] ?? 0);
$record = fetch_one("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE a.id = $id");
if (!$record) {
    redirect(base_url('admin/absensi/index.php'));
}
?>
<div class=\"card shadow-sm\">
    <div class=\"card-body\">
        <h4>Detail Absensi</h4>
        <p><strong>Pegawai:</strong> <?= htmlspecialchars($record['fullname']) ?></p>
        <p><strong>Tanggal:</strong> <?= htmlspecialchars($record['tanggal']) ?></p>
        <p><strong>Jam Masuk:</strong> <?= htmlspecialchars($record['jam_masuk'] ?? '-') ?></p>
        <p><strong>Jam Pulang:</strong> <?= htmlspecialchars($record['jam_pulang'] ?? '-') ?></p>
        <p><strong>Lokasi Masuk:</strong> <?= htmlspecialchars($record['lokasi_masuk'] ?? '-') ?></p>
        <p><strong>Lokasi Pulang:</strong> <?= htmlspecialchars($record['lokasi_pulang'] ?? '-') ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($record['status']) ?></p>
        <p><strong>Verifikasi:</strong> <?= $record['verified'] ? 'Sudah' : 'Belum' ?></p>
        <div class=\"row\">
            <div class=\"col-md-6\">
                <h5>Foto Masuk</h5>
                <?php if ($record['foto_masuk']) : ?>
                    <img src=\"<?= htmlspecialchars($record['foto_masuk']) ?>\" class=\"img-fluid rounded border\" alt=\"Foto Masuk\">
                <?php else : ?>
                    <p>Tidak ada foto masuk.</p>
                <?php endif; ?>
            </div>
            <div class=\"col-md-6\">
                <h5>Foto Pulang</h5>
                <?php if ($record['foto_pulang']) : ?>
                    <img src=\"<?= htmlspecialchars($record['foto_pulang']) ?>\" class=\"img-fluid rounded border\" alt=\"Foto Pulang\">
                <?php else : ?>
                    <p>Tidak ada foto pulang.</p>
                <?php endif; ?>
            </div>
        </div>
        <a href=\"<?= base_url('admin/absensi/index.php') ?>\" class=\"btn btn-secondary mt-3\">Kembali</a>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/absensi/verifikasi.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    query("UPDATE absensi SET verified = 1 WHERE id = $id");
    flash('message', 'Absensi berhasil diverifikasi.');
}
redirect(base_url('admin/absensi/index.php'));
""",
    'admin/laporan/harian.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$date = $_GET['date'] ?? date('Y-m-d');
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE a.tanggal = '" . escape($date) . "' ORDER BY a.jam_masuk DESC");
?>
<div class=\"row mb-3\">
    <div class=\"col-md-8\">
        <h4>Laporan Harian</h4>
        <p class=\"text-muted\">Tampilkan absensi berdasarkan tanggal.</p>
    </div>
    <div class=\"col-md-4\">
        <form method=\"get\" class=\"d-flex gap-2\">
            <input type=\"date\" name=\"date\" class=\"form-control\" value=\"<?= htmlspecialchars($date) ?>\">
            <button class=\"btn btn-primary\">Tampilkan</button>
        </form>
    </div>
</div>
<div class=\"card shadow-sm\">
    <div class=\"card-body table-responsive\">
        <table class=\"table table-bordered table-hover mb-0\">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pegawai</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $index => $row) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/laporan/bulanan.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$month = $_GET['month'] ?? date('Y-m');
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE DATE_FORMAT(a.tanggal, '%Y-%m') = '" . escape($month) . "' ORDER BY a.tanggal DESC");
?>
<div class=\"row mb-3\">
    <div class=\"col-md-8\">
        <h4>Laporan Bulanan</h4>
        <p class=\"text-muted\">Tampilkan absensi berdasarkan bulan.</p>
    </div>
    <div class=\"col-md-4\">
        <form method=\"get\" class=\"d-flex gap-2\">
            <input type=\"month\" name=\"month\" class=\"form-control\" value=\"<?= htmlspecialchars($month) ?>\">
            <button class=\"btn btn-primary\">Tampilkan</button>
        </form>
    </div>
</div>
<div class=\"card shadow-sm\">
    <div class=\"card-body table-responsive\">
        <table class=\"table table-bordered table-hover mb-0\">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pegawai</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $index => $row) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/laporan/tahunan.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
year = $_GET['year'] ?? date('Y');
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE DATE_FORMAT(a.tanggal, '%Y') = '" . escape($year) . "' ORDER BY a.tanggal DESC");
?>
<div class=\"row mb-3\">
    <div class=\"col-md-8\">
        <h4>Laporan Tahunan</h4>
        <p class=\"text-muted\">Tampilkan absensi berdasarkan tahun.</p>
    </div>
    <div class=\"col-md-4\">
        <form method=\"get\" class=\"d-flex gap-2\">
            <input type=\"number\" name=\"year\" class=\"form-control\" value=\"<?= htmlspecialchars($year) ?>\" min=\"2000\" max=\"2100\">
            <button class=\"btn btn-primary\">Tampilkan</button>
        </form>
    </div>
</div>
<div class=\"card shadow-sm\">
    <div class=\"card-body table-responsive\">
        <table class=\"table table-bordered table-hover mb-0\">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pegawai</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $index => $row) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/laporan/export_excel.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id ORDER BY a.tanggal DESC");
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan-absensi.xls"');
?>
<table border=\"1\">
    <tr>
        <th>#</th>
        <th>Pegawai</th>
        <th>Tanggal</th>
        <th>Masuk</th>
        <th>Pulang</th>
        <th>Status</th>
        <th>Verified</th>
    </tr>
    <?php foreach ($records as $index => $row) : ?>
    <tr>
        <td><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($row['fullname']) ?></td>
        <td><?= htmlspecialchars($row['tanggal']) ?></td>
        <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td><?= $row['verified'] ? 'Ya' : 'Belum' ?></td>
    </tr>
    <?php endforeach; ?>
</table>
""",
    'admin/laporan/cetak_pdf.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id ORDER BY a.tanggal DESC");
?>
<div class=\"card shadow-sm\">
    <div class=\"card-body\">
        <h4>Laporan Absensi</h4>
        <p class=\"text-muted\">Cetak / simpan sebagai PDF menggunakan browser.</p>
        <button onclick=\"window.print()\" class=\"btn btn-outline-primary mb-3\">Cetak</button>
        <div class=\"table-responsive\">
            <table class=\"table table-bordered\">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pegawai</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $index => $row) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'admin/pengaturan/index.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
?>
<div class=\"card shadow-sm\">
    <div class=\"card-body\">
        <h4>Pengaturan Sistem</h4>
        <p>Sistem absensi ini menggunakan PHP Native, MySQL, Bootstrap 5, WebcamJS, dan Geolocation API.</p>
        <ul>
            <li>Nama aplikasi: Absensi PT PROPSCODE Studio Teknologi</li>
            <li>Base URL: <?= htmlspecialchars(base_url('')) ?></li>
            <li>Versi: 1.0</li>
        </ul>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'pegawai/dashboard.php': """<?php
require_once __DIR__ . '/../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../template_header.php';
$pegawai = $_SESSION['user'];
$today = date('Y-m-d');
$attendance = fetch_one("SELECT * FROM absensi WHERE user_id = " . intval($pegawai['id']) . " AND tanggal = '$today'");
?>
<div class=\"row\">
    <div class=\"col-lg-12\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Dashboard Pegawai</h4>
                <p>Selamat datang, <?= htmlspecialchars($pegawai['fullname']) ?>.</p>
                <div class=\"row\">
                    <div class=\"col-md-4\">
                        <div class=\"card border-success mb-3\">
                            <div class=\"card-body\">
                                <h5>Absensi Hari Ini</h5>
                                <p><?= htmlspecialchars($attendance ? ($attendance['jam_masuk'] ? 'Sudah hadir' : 'Belum absen') : 'Belum absen') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class=\"col-md-4\">
                        <div class=\"card border-primary mb-3\">
                            <div class=\"card-body\">
                                <h5>Profil</h5>
                                <p><a href=\"<?= base_url('pegawai/profil/index.php') ?>\">Perbarui data Anda</a></p>
                            </div>
                        </div>
                    </div>
                    <div class=\"col-md-4\">
                        <div class=\"card border-info mb-3\">
                            <div class=\"card-body\">
                                <h5>Riwayat</h5>
                                <p><a href=\"<?= base_url('pegawai/riwayat/index.php') ?>\">Lihat riwayat absensi</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../template_footer.php'; ?>
""",
    'pegawai/absensi/absen_masuk.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../template_header.php';
?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-8\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Absensi Masuk</h4>
                <p>Silakan ambil foto dan kirim lokasi untuk absen masuk.</p>
                <form action=\"<?= base_url('pegawai/absensi/simpan_masuk.php') ?>\" method=\"post\">
                    <div id=\"camera\" class=\"mb-3\"></div>
                    <button type=\"button\" id=\"capture-photo\" class=\"btn btn-primary mb-3\">Ambil Foto</button>
                    <div id=\"photoPreview\" class=\"mb-3\"></div>
                    <input type=\"hidden\" name=\"foto_masuk\" id=\"foto_masuk\">
                    <input type=\"hidden\" name=\"latitude_masuk\" id=\"latitude_masuk\">
                    <input type=\"hidden\" name=\"longitude_masuk\" id=\"longitude_masuk\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Lokasi</label>
                        <div class=\"input-group\">
                            <input type=\"text\" class=\"form-control\" id=\"lokasi_masuk\" name=\"lokasi_masuk\" readonly>
                            <button type=\"button\" class=\"btn btn-outline-secondary\" id=\"get-location\">Dapatkan Lokasi</button>
                        </div>
                    </div>
                    <button type=\"submit\" class=\"btn btn-success\">Simpan Absensi Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
window.addEventListener('load', function() {
    if (typeof Webcam !== 'undefined') {
        Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });
        Webcam.attach('#camera');
    }
    document.getElementById('capture-photo').addEventListener('click', function() {
        Webcam.snap(function(data_uri) {
            document.getElementById('photoPreview').innerHTML = '<img src="' + data_uri + '" class="img-fluid rounded border">';
            document.getElementById('foto_masuk').value = data_uri;
        });
    });
    document.getElementById('get-location').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation tidak didukung browser Anda.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude_masuk').value = position.coords.latitude;
            document.getElementById('longitude_masuk').value = position.coords.longitude;
            document.getElementById('lokasi_masuk').value = position.coords.latitude + ', ' + position.coords.longitude;
        }, function(err) {
            alert('Gagal mendapatkan lokasi: ' + err.message);
        }, { enableHighAccuracy: true });
    });
});
</script>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'pegawai/absensi/absen_pulang.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../template_header.php';
?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-8\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Absensi Pulang</h4>
                <p>Silakan ambil foto dan kirim lokasi untuk absen pulang.</p>
                <form action=\"<?= base_url('pegawai/absensi/simpan_pulang.php') ?>\" method=\"post\">
                    <div id=\"camera\" class=\"mb-3\"></div>
                    <button type=\"button\" id=\"capture-photo\" class=\"btn btn-primary mb-3\">Ambil Foto</button>
                    <div id=\"photoPreview\" class=\"mb-3\"></div>
                    <input type=\"hidden\" name=\"foto_pulang\" id=\"foto_pulang\">
                    <input type=\"hidden\" name=\"latitude_pulang\" id=\"latitude_pulang\">
                    <input type=\"hidden\" name=\"longitude_pulang\" id=\"longitude_pulang\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Lokasi</label>
                        <div class=\"input-group\">
                            <input type=\"text\" class=\"form-control\" id=\"lokasi_pulang\" name=\"lokasi_pulang\" readonly>
                            <button type=\"button\" class=\"btn btn-outline-secondary\" id=\"get-location\">Dapatkan Lokasi</button>
                        </div>
                    </div>
                    <button type=\"submit\" class=\"btn btn-success\">Simpan Absensi Pulang</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
window.addEventListener('load', function() {
    if (typeof Webcam !== 'undefined') {
        Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });
        Webcam.attach('#camera');
    }
    document.getElementById('capture-photo').addEventListener('click', function() {
        Webcam.snap(function(data_uri) {
            document.getElementById('photoPreview').innerHTML = '<img src="' + data_uri + '" class="img-fluid rounded border">';
            document.getElementById('foto_pulang').value = data_uri;
        });
    });
    document.getElementById('get-location').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation tidak didukung browser Anda.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude_pulang').value = position.coords.latitude;
            document.getElementById('longitude_pulang').value = position.coords.longitude;
            document.getElementById('lokasi_pulang').value = position.coords.latitude + ', ' + position.coords.longitude;
        }, function(err) {
            alert('Gagal mendapatkan lokasi: ' + err.message);
        }, { enableHighAccuracy: true });
    });
});
</script>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'pegawai/absensi/simpan_masuk.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';
$userId = intval($_SESSION['user']['id']);
$foto = $_POST['foto_masuk'] ?? '';
$lokasi = escape($_POST['lokasi_masuk'] ?? '');
$lat = escape($_POST['latitude_masuk'] ?? '');
$lng = escape($_POST['longitude_masuk'] ?? '');
$date = date('Y-m-d');
if ($foto === '' || $lokasi === '') {
    flash('message', 'Silakan ambil foto dan lokasi terlebih dahulu.');
    redirect(base_url('pegawai/absensi/absen_masuk.php'));
}
$existing = fetch_one("SELECT id FROM absensi WHERE user_id = $userId AND tanggal = '$date'");
if ($existing) {
    flash('message', 'Anda sudah melakukan absensi masuk hari ini.');
    redirect(base_url('pegawai/dashboard.php'));
}
query("INSERT INTO absensi (user_id, tanggal, jam_masuk, lokasi_masuk, latitude_masuk, longitude_masuk, foto_masuk, status) VALUES ($userId, '$date', CURTIME(), '$lokasi', '$lat', '$lng', '$foto', 'Hadir')");
flash('message', 'Absensi masuk berhasil dicatat.');
redirect(base_url('pegawai/dashboard.php'));
""",
    'pegawai/absensi/simpan_pulang.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';
$userId = intval($_SESSION['user']['id']);
$foto = $_POST['foto_pulang'] ?? '';
$lokasi = escape($_POST['lokasi_pulang'] ?? '');
$lat = escape($_POST['latitude_pulang'] ?? '');
$lng = escape($_POST['longitude_pulang'] ?? '');
$date = date('Y-m-d');
$record = fetch_one("SELECT id, jam_pulang FROM absensi WHERE user_id = $userId AND tanggal = '$date'");
if (!$record || $record['jam_pulang']) {
    flash('message', 'Absensi pulang tidak tersedia atau sudah dicatat.');
    redirect(base_url('pegawai/dashboard.php'));
}
if ($foto === '' || $lokasi === '') {
    flash('message', 'Silakan ambil foto dan lokasi terlebih dahulu.');
    redirect(base_url('pegawai/absensi/absen_pulang.php'));
}
query("UPDATE absensi SET jam_pulang = CURTIME(), lokasi_pulang = '$lokasi', latitude_pulang = '$lat', longitude_pulang = '$lng', foto_pulang = '$foto', updated_at = NOW() WHERE id = " . intval($record['id']));
flash('message', 'Absensi pulang berhasil dicatat.');
redirect(base_url('pegawai/dashboard.php'));
""",
    'pegawai/profil/index.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../template_header.php';
$pegawai = $_SESSION['user'];
?>
<div class=\"row justify-content-center\">
    <div class=\"col-lg-7\">
        <div class=\"card shadow-sm\">
            <div class=\"card-body\">
                <h4>Profil Pegawai</h4>
                <form action=\"<?= base_url('pegawai/profil/update.php') ?>\" method=\"post\">
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Nama Lengkap</label>
                        <input type=\"text\" name=\"fullname\" class=\"form-control\" value=\"<?= htmlspecialchars($pegawai['fullname']) ?>\" required>
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Email</label>
                        <input type=\"email\" name=\"email\" class=\"form-control\" value=\"<?= htmlspecialchars($pegawai['email']) ?>\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Telepon</label>
                        <input type=\"text\" name=\"phone\" class=\"form-control\" value=\"<?= htmlspecialchars($pegawai['phone']) ?>\">
                    </div>
                    <div class=\"mb-3\">
                        <label class=\"form-label\">Password Baru</label>
                        <input type=\"password\" name=\"password\" class=\"form-control\" placeholder=\"Kosongkan jika tidak ingin mengubah password\">
                    </div>
                    <button type=\"submit\" class=\"btn btn-primary\">Perbarui Profil</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'pegawai/profil/update.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';
$id = intval($_SESSION['user']['id']);
$fullname = escape($_POST['fullname'] ?? '');
$email = escape($_POST['email'] ?? '');
$phone = escape($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
if ($fullname === '') {
    flash('message', 'Nama wajib diisi.');
    redirect(base_url('pegawai/profil/index.php'));
}
$query = "UPDATE users SET fullname = '$fullname', email = '$email', phone = '$phone'";
if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $query .= ", password = '$hash'";
}
$query .= " WHERE id = $id";
query($query);
$user = fetch_one("SELECT * FROM users WHERE id = $id");
$_SESSION['user'] = $user;
flash('message', 'Profil berhasil diperbarui.');
redirect(base_url('pegawai/profil/index.php'));
""",
    'pegawai/riwayat/index.php': """<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../template_header.php';
$userId = intval($_SESSION['user']['id']);
$records = fetch_all("SELECT * FROM absensi WHERE user_id = $userId ORDER BY tanggal DESC, jam_masuk DESC");
?>
<div class=\"card shadow-sm\">
    <div class=\"card-body\">
        <h4>Riwayat Absensi</h4>
        <div class=\"table-responsive\">
            <table class=\"table table-bordered table-hover mb-0\">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                        <th>Verified</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $index => $row) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['jam_masuk'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jam_pulang'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= $row['verified'] ? 'Ya' : 'Belum' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
""",
    'assets/js/gps.js': """function getCurrentLocation(callback) {
    if (!navigator.geolocation) {
        callback(null, 'Geolocation tidak didukung browser.');
        return;
    }
    navigator.geolocation.getCurrentPosition(function(position) {
        callback(position.coords, null);
    }, function(error) {
        callback(null, error.message);
    }, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    });
}
""",
    'assets/js/script.js': """function initWebcam(targetSelector, previewSelector, fieldName) {
    if (typeof Webcam === 'undefined') {
        return;
    }
    Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });
    Webcam.attach(targetSelector);
    var button = document.querySelector('[data-capture="' + targetSelector + '"]');
    if (!button) return;
    button.addEventListener('click', function() {
        Webcam.snap(function(data_uri) {
            document.querySelector(previewSelector).innerHTML = '<img src="' + data_uri + '" class="img-fluid rounded border">';
            var input = document.querySelector('input[name="' + fieldName + '"]');
            if (input) {
                input.value = data_uri;
            }
        });
    });
}
""",
    'assets/css/style.css': """body {
    background: #f4f7fb;
}
.card {
    border-radius: 12px;
}
#camera {
    min-height: 240px;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
}
#camera video {
    width: 100%;
    max-width: 400px;
}
#photoPreview img {
    max-width: 100%;
}
""",
    'database/absensi_propscode.sql': """CREATE DATABASE IF NOT EXISTS absensi_propscode CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE absensi_propscode;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    role ENUM('admin', 'pegawai') NOT NULL DEFAULT 'pegawai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME DEFAULT NULL,
    jam_pulang TIME DEFAULT NULL,
    lokasi_masuk VARCHAR(255) DEFAULT NULL,
    latitude_masuk DECIMAL(10,7) DEFAULT NULL,
    longitude_masuk DECIMAL(10,7) DEFAULT NULL,
    foto_masuk TEXT DEFAULT NULL,
    lokasi_pulang VARCHAR(255) DEFAULT NULL,
    latitude_pulang DECIMAL(10,7) DEFAULT NULL,
    longitude_pulang DECIMAL(10,7) DEFAULT NULL,
    foto_pulang TEXT DEFAULT NULL,
    status ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') DEFAULT 'Hadir',
    verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, fullname, email, phone, role) VALUES
('admin', '$2y$10$WYHB8vAgcS59NtvOveTpUen71TEDniA/WzQ3LbQmhs4TLOYy9Makq', 'Administrator', 'admin@propscode.com', '081234567890', 'admin'),
('pegawai1', '$2y$10$1c5q9jWXZvPifk.69bi/Y.OZcYaGg5GxUY82/tuiOIGLN8.wAYmYK', 'Budi Santoso', 'budi@propscode.com', '081234567891', 'pegawai');
""",
    'admin/absensi/foto.php': """<?php
// Placeholder file for admin absensi photo handling.
// This file can be extended if photo workflows are added.
?>
""",
    'admin/absensi/lokasi.php': """<?php
// Placeholder file for admin absensi location handling.
// This file can be extended if location workflows are added.
?>
""",
    'auth/cek_admin.php': """<?php
require_once __DIR__ . '/session.php';
auth_admin();
?>
""",
    'auth/cek_pegawai.php': """<?php
require_once __DIR__ . '/session.php';
auth_pegawai();
?>
""",
    'pegawai/absensi/cek_gps.php': """<?php
// Placeholder file for GPS check or helper functionality.
?>
""",
    'assets/js/webcam.js': """// Placeholder webcam helper file.
// WebcamJS is loaded from CDN in the template.
""",
}

root = os.getcwd()
for relative_path, content in files.items():
    path = os.path.join(root, relative_path)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
print('Written', len(files), 'files to', root)
