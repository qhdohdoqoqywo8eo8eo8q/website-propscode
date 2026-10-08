<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$month = $_GET['month'] ?? date('Y-m');
$records = fetch_all("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE DATE_FORMAT(a.tanggal, '%Y-%m') = '" . escape($month) . "' ORDER BY a.tanggal DESC");
?>
<div class="row mb-3">
    <div class="col-md-8">
        <h4>Laporan Bulanan</h4>
        <p class="text-muted">Tampilkan absensi berdasarkan bulan.</p>
    </div>
    <div class="col-md-4">
        <form method="get" class="d-flex gap-2">
            <input type="month" name="month" class="form-control" value="<?= htmlspecialchars($month) ?>">
            <button class="btn btn-primary">Tampilkan</button>
        </form>
    </div>
</div>
<div class="d-flex justify-content-end gap-2 mb-3">
    <button type="button" onclick="window.print()" class="btn btn-outline-secondary">Cetak</button>
    <a href="<?= base_url('admin/laporan/export_excel.php?type=bulanan&month=' . urlencode($month)) ?>" class="btn btn-success">Ekspor Excel</a>
</div>
<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover mb-0">
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
