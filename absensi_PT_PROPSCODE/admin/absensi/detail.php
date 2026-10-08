<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$id = intval($_GET['id'] ?? 0);
$record = fetch_one("SELECT a.*, u.fullname FROM absensi a JOIN users u ON a.user_id = u.id WHERE a.id = $id");
if (!$record) {
    redirect(base_url('admin/absensi/index.php'));
}
?>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-sm-5">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Detail Absensi</h4>
                        <p class="text-muted small mb-0">Informasi lengkap presensi pegawai untuk tanggal terpilih.</p>
                    </div>
                    <a href="<?= base_url('admin/absensi/index.php') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Info column -->
                    <div class="col-md-6">
                        <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-info-circle me-1"></i> Data Kehadiran</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Nama Pegawai</span>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($record['fullname']) ?></span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Tanggal</span>
                                <span class="fw-semibold text-dark"><i class="bi bi-calendar3 me-1 text-muted"></i> <?= htmlspecialchars($record['tanggal']) ?></span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Jam Masuk</span>
                                <span class="fw-semibold text-dark"><i class="bi bi-clock text-primary me-1"></i> <?= htmlspecialchars($record['jam_masuk'] ?? '-') ?></span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Jam Pulang</span>
                                <span class="fw-semibold text-dark"><i class="bi bi-clock-history text-muted me-1"></i> <?= htmlspecialchars($record['jam_pulang'] ?? '-') ?></span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status Kehadiran</span>
                                <?php 
                                $statusClass = 'badge-hadir';
                                if ($record['status'] === 'Izin') $statusClass = 'badge-izin';
                                if ($record['status'] === 'Sakit') $statusClass = 'badge-sakit';
                                if ($record['status'] === 'Alpha') $statusClass = 'badge-alpha';
                                ?>
                                <span class="badge-pill-modern <?= $statusClass ?>"><?= htmlspecialchars($record['status']) ?></span>
                            </li>
                            <?php if ($record['status'] !== 'Hadir' && !empty($record['keterangan'])) : ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Keterangan Alasan</span>
                                    <span class="text-dark fw-bold" style="max-width: 250px; text-align: right;"><?= htmlspecialchars($record['keterangan']) ?></span>
                                </li>
                            <?php endif; ?>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status Verifikasi</span>
                                <?php if ($record['verified']) : ?>
                                    <span class="badge-pill-modern badge-verified-yes"><i class="bi bi-check-circle-fill"></i> Sudah Terverifikasi</span>
                                <?php else : ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge-pill-modern badge-verified-no"><i class="bi bi-exclamation-circle-fill"></i> Menunggu Verifikasi</span>
                                        <a href="<?= base_url('admin/absensi/verifikasi.php?id=' . $record['id']) ?>" class="btn btn-success btn-sm py-1 px-2.5 small"><i class="bi bi-patch-check"></i> Verifikasi Sekarang</a>
                                    </div>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <!-- GPS Location column -->
                    <div class="col-md-6">
                        <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-geo-alt me-1"></i> Lokasi GPS & Koordinat</h5>
                        <div class="bg-light p-3 rounded-16 mb-3">
                            <div class="small fw-bold text-muted mb-1">Koordinat Masuk:</div>
                            <div class="small text-dark mb-3">
                                <i class="bi bi-pin-map-fill text-danger me-1"></i> <?= htmlspecialchars($record['lokasi_masuk'] ?? 'Tidak ada data lokasi masuk.') ?>
                                <?php if ($record['latitude_masuk'] && $record['longitude_masuk']) : ?>
                                    <br><a href="https://www.google.com/maps/search/?api=1&query=<?= $record['latitude_masuk'] ?>,<?= $record['longitude_masuk'] ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0.5 px-2 mt-2"><i class="bi bi-map"></i> Buka Google Maps</a>
                                <?php endif; ?>
                            </div>

                            <div class="small fw-bold text-muted mb-1">Koordinat Pulang:</div>
                            <div class="small text-dark">
                                <i class="bi bi-pin-map text-muted me-1"></i> <?= htmlspecialchars($record['lokasi_pulang'] ?? 'Tidak ada data lokasi pulang.') ?>
                                <?php if ($record['latitude_pulang'] && $record['longitude_pulang']) : ?>
                                    <br><a href="https://www.google.com/maps/search/?api=1&query=<?= $record['latitude_pulang'] ?>,<?= $record['longitude_pulang'] ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0.5 px-2 mt-2"><i class="bi bi-map"></i> Buka Google Maps</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (($record['latitude_masuk'] && $record['longitude_masuk']) || ($record['latitude_pulang'] && $record['longitude_pulang'])) : ?>
                            <div id="map-detail" class="rounded-16 border bg-light shadow-sm mb-3" style="height: 250px;"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Captured Photos Row -->
                <div class="row g-4 pt-3 border-top">
                    <div class="col-sm-6 text-center">
                        <h6 class="fw-bold text-dark mb-2">Foto Saat Masuk</h6>
                        <div class="border rounded-16 p-2 bg-light d-inline-block w-100" style="max-width: 320px;">
                            <?php if ($record['foto_masuk']) : ?>
                                <img src="<?= htmlspecialchars($record['foto_masuk']) ?>" class="img-fluid rounded-12" alt="Foto Masuk" style="object-fit: cover; width: 100%; max-height: 240px;">
                            <?php else : ?>
                                <div class="text-muted small py-5"><i class="bi bi-camera-video-off d-block fs-2 mb-2 opacity-50"></i>Tidak ada foto masuk</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-sm-6 text-center">
                        <h6 class="fw-bold text-dark mb-2">Foto Saat Pulang</h6>
                        <div class="border rounded-16 p-2 bg-light d-inline-block w-100" style="max-width: 320px;">
                            <?php if ($record['foto_pulang']) : ?>
                                <img src="<?= htmlspecialchars($record['foto_pulang']) ?>" class="img-fluid rounded-12" alt="Foto Pulang" style="object-fit: cover; width: 100%; max-height: 240px;">
                            <?php else : ?>
                                <div class="text-muted small py-5"><i class="bi bi-camera-video-off d-block fs-2 mb-2 opacity-50"></i>Tidak ada foto pulang</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (($record['latitude_masuk'] && $record['longitude_masuk']) || ($record['latitude_pulang'] && $record['longitude_pulang'])) : ?>
<script>
window.addEventListener('load', function() {
    var map = L.map('map-detail');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var markers = [];

    <?php if ($record['latitude_masuk'] && $record['longitude_masuk']) : ?>
        var markerMasuk = L.marker([<?= $record['latitude_masuk'] ?>, <?= $record['longitude_masuk'] ?>], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map).bindPopup('<b>Absen Masuk</b><br>Jam: <?= htmlspecialchars($record['jam_masuk'] ?? '') ?>');
        markers.push(markerMasuk);
    <?php endif; ?>

    <?php if ($record['latitude_pulang'] && $record['longitude_pulang']) : ?>
        var markerPulang = L.marker([<?= $record['latitude_pulang'] ?>, <?= $record['longitude_pulang'] ?>], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map).bindPopup('<b>Absen Pulang</b><br>Jam: <?= htmlspecialchars($record['jam_pulang'] ?? '') ?>');
        markers.push(markerPulang);
    <?php endif; ?>

    if (markers.length > 0) {
        var group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.2));
        if (markers.length === 1) {
            map.setZoom(15);
        }
    }
});
</script>
<?php endif; ?>
<?php include __DIR__ . '/../../template_footer.php'; ?>
