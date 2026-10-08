<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lat = floatval($_POST['office_latitude'] ?? -6.2088);
    $lng = floatval($_POST['office_longitude'] ?? 106.8456);
    $radius = intval($_POST['office_radius_meters'] ?? 100);
    $time = trim($_POST['start_time_masuk'] ?? '07:00:00');
    $limit_time = trim($_POST['limit_time_masuk'] ?? '08:00:00');
    
    if ($lat && $lng && $radius && $time && $limit_time) {
        save_settings([
            'office_latitude' => $lat,
            'office_longitude' => $lng,
            'office_radius_meters' => $radius,
            'start_time_masuk' => $time,
            'limit_time_masuk' => $limit_time
        ]);
        flash('message', 'Pengaturan absensi berhasil diperbarui!');
        redirect(base_url('admin/pengaturan/index.php'));
    } else {
        $error = 'Semua field harus diisi dengan benar.';
    }
}

require_once __DIR__ . '/../../template_header.php';
$settings = get_settings();
?>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars(flash('message')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 p-sm-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-primary-light text-primary rounded-pill p-2 me-3 d-inline-flex">
                        <i class="bi bi-gear-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">Pengaturan Sistem & Geofencing</h4>
                        <p class="text-muted small mb-0">Atur batasan waktu masuk, lokasi koordinat kantor, dan radius geofence absensi.</p>
                    </div>
                </div>

                <form action="" method="post">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-sliders me-1"></i> Konfigurasi Parameter</h5>
                            
                            <div class="mb-3">
                                <label for="start_time_masuk" class="form-label fw-semibold">Jam Buka Absen Masuk</label>
                                <input type="time" step="1" class="form-control" id="start_time_masuk" name="start_time_masuk" value="<?= htmlspecialchars($settings['start_time_masuk']) ?>" required>
                                <div class="form-text">Waktu server terkecil pegawai boleh melakukan absen masuk.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="limit_time_masuk" class="form-label fw-semibold">Jam Batas Akhir Absen Masuk (Terlambat)</label>
                                <input type="time" step="1" class="form-control" id="limit_time_masuk" name="limit_time_masuk" value="<?= htmlspecialchars($settings['limit_time_masuk'] ?? '08:00:00') ?>" required>
                                <div class="form-text">Batas waktu untuk dianggap tepat waktu. Setelah jam ini karyawan dianggap terlambat dan harus memasukkan alasan tidak hadir.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="office_radius_meters" class="form-label fw-semibold">Radius Absen (Meter)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="office_radius_meters" name="office_radius_meters" value="<?= htmlspecialchars($settings['office_radius_meters']) ?>" min="10" required>
                                    <span class="input-group-text">Meter</span>
                                </div>
                                <div class="form-text">Jarak maksimal pegawai dari kantor agar bisa melakukan absensi.</div>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="office_latitude" class="form-label fw-semibold">Latitude Kantor</label>
                                    <input type="text" class="form-control" id="office_latitude" name="office_latitude" value="<?= htmlspecialchars($settings['office_latitude']) ?>" required readonly>
                                </div>
                                <div class="col-6">
                                    <label for="office_longitude" class="form-label fw-semibold">Longitude Kantor</label>
                                    <input type="text" class="form-control" id="office_longitude" name="office_longitude" value="<?= htmlspecialchars($settings['office_longitude']) ?>" required readonly>
                                </div>
                                <div class="form-text">Gunakan peta di sebelah kanan untuk memperbarui koordinat secara interaktif.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 mt-2 fw-semibold"><i class="bi bi-save2-fill me-1"></i> Simpan Pengaturan</button>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-map-fill me-1"></i> Peta Lokasi Kantor</h5>
                            <div id="map-config" class="rounded-16 border mb-2 shadow-sm" style="height: 300px;"></div>
                            <p class="small text-muted text-center"><i class="bi bi-info-circle me-1"></i> Klik pada peta atau seret pin untuk memindahkan posisi koordinat kantor.</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function() {
    var latInput = document.getElementById('office_latitude');
    var lngInput = document.getElementById('office_longitude');
    var radiusInput = document.getElementById('office_radius_meters');

    var initLat = parseFloat(latInput.value);
    var initLng = parseFloat(lngInput.value);
    var initRadius = parseInt(radiusInput.value);

    // Initialize Map
    var map = L.map('map-config').setView([initLat, initLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Marker
    var officeMarker = L.marker([initLat, initLng], {
        draggable: true
    }).addTo(map);

    // Radius Circle
    var radiusCircle = L.circle([initLat, initLng], {
        color: 'blue',
        fillColor: '#30f',
        fillOpacity: 0.15,
        radius: initRadius
    }).addTo(map);

    function updateInputs(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
    }

    // Drag marker event
    officeMarker.on('dragend', function(e) {
        var position = officeMarker.getLatLng();
        updateInputs(position.lat, position.lng);
        radiusCircle.setLatLng(position);
    });

    // Map click event
    map.on('click', function(e) {
        officeMarker.setLatLng(e.latlng);
        radiusCircle.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
    });

    // Radius change event
    radiusInput.addEventListener('input', function() {
        var r = parseInt(this.value);
        if (r && r >= 10) {
            radiusCircle.setRadius(r);
        }
    });
});
</script>
<?php include __DIR__ . '/../../template_footer.php'; ?>
