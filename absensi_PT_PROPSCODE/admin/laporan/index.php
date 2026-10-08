<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4>Menu Laporan</h4>
                <p>Pilih jenis laporan yang ingin ditampilkan atau dicetak.</p>
                <div class="list-group">
                    <a href="<?= base_url('admin/laporan/harian.php') ?>" class="list-group-item list-group-item-action">Laporan Harian</a>
                    <a href="<?= base_url('admin/laporan/bulanan.php') ?>" class="list-group-item list-group-item-action">Laporan Bulanan</a>
                    <a href="<?= base_url('admin/laporan/tahunan.php') ?>" class="list-group-item list-group-item-action">Laporan Tahunan</a>
                    <a href="<?= base_url('admin/laporan/cetak_pdf.php') ?>" class="list-group-item list-group-item-action">Cetak Semua Absensi</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>