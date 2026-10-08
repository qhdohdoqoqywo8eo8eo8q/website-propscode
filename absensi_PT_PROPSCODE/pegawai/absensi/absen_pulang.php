<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';

$userId = intval($_SESSION['user']['id']);
$date = date('Y-m-d');
$record = fetch_one("SELECT id, status, jam_pulang FROM absensi WHERE user_id = $userId AND tanggal = '$date'");

if (!$record) {
    flash('message', 'Anda harus melakukan Absen Masuk terlebih dahulu sebelum dapat mengakses Absen Pulang.');
    redirect(base_url('pegawai/dashboard.php'));
} elseif (in_array($record['status'], ['Izin', 'Sakit', 'Alpha'])) {
    $statusLabel = $record['status'] === 'Alpha' ? 'Tanpa Keterangan' : $record['status'];
    flash('message', 'Anda tidak perlu melakukan Absen Pulang karena status kehadiran Anda hari ini adalah ' . $statusLabel . '.');
    redirect(base_url('pegawai/dashboard.php'));
} elseif ($record['jam_pulang']) {
    flash('message', 'anda telah absen hari ini');
    redirect(base_url('pegawai/dashboard.php'));
}

require_once __DIR__ . '/../../template_header.php';
?>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-sm-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success-light text-success rounded-pill p-2 me-3 d-inline-flex"><i class="bi bi-box-arrow-left fs-4"></i></div>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">Absensi Pulang</h4>
                        <p class="text-muted small mb-0">Silakan ambil foto selfie dan konfirmasi lokasi Anda untuk melakukan absensi pulang.</p>
                    </div>
                </div>
                
                <form action="<?= base_url('pegawai/absensi/simpan_pulang.php') ?>" method="post">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6 text-center">
                            <label class="form-label d-block text-start mb-2"><i class="bi bi-camera me-1"></i> Kamera</label>
                            <div id="camera" class="mb-3"></div>
                            <button type="button" id="capture-photo" class="btn btn-outline-primary btn-sm w-100 py-2"><i class="bi bi-camera-fill me-1"></i> Ambil Foto</button>
                        </div>
                        <div class="col-md-6 text-center">
                            <label class="form-label d-block text-start mb-2"><i class="bi bi-image me-1"></i> Pratinjau Foto</label>
                            <div id="photoPreview" class="d-flex align-items-center justify-content-center border rounded-16 bg-light" style="height: 300px; overflow: hidden;">
                                <div class="text-muted small p-3">
                                    <i class="bi bi-image-fill d-block fs-1 mb-2 opacity-50"></i>
                                    Foto belum diambil
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="foto_pulang" id="foto_pulang">
                    <input type="hidden" name="latitude_pulang" id="latitude_pulang">
                    <input type="hidden" name="longitude_pulang" id="longitude_pulang">
                    
                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Lokasi Koordinat GPS</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="lokasi_pulang" name="lokasi_pulang" placeholder="Klik dapatkan lokasi untuk mengisi koordinat" readonly required>
                            <button type="button" class="btn btn-outline-secondary px-3" id="get-location"><i class="bi bi-pin-map-fill me-1"></i> Dapatkan Lokasi</button>
                        </div>
                        <div id="map" class="rounded-16 border bg-light shadow-sm" style="height: 250px; display: none;"></div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('pegawai/dashboard.php') ?>" class="btn btn-outline-secondary flex-grow-1 py-2.5"><i class="bi bi-arrow-left"></i> Batal</a>
                        <button type="submit" class="btn btn-success flex-grow-2 py-2.5 px-4"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Kirim Absensi Pulang</button>
                    </div>
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
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            
            document.getElementById('latitude_pulang').value = lat;
            document.getElementById('longitude_pulang').value = lng;
            document.getElementById('lokasi_pulang').value = lat + ', ' + lng;
            
            // Show and draw map
            var mapDiv = document.getElementById('map');
            mapDiv.style.display = 'block';
            
            if (!window.leafletMap) {
                window.leafletMap = L.map('map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(window.leafletMap);
                window.leafletMarker = L.marker([lat, lng]).addTo(window.leafletMap)
                    .bindPopup('Lokasi Anda saat ini')
                    .openPopup();
            } else {
                window.leafletMap.setView([lat, lng], 16);
                window.leafletMarker.setLatLng([lat, lng]);
            }
            
            // Invalidate size after layout flow adjustments
            setTimeout(function() {
                window.leafletMap.invalidateSize();
            }, 200);
            
        }, function(err) {
            alert('Gagal mendapatkan lokasi: ' + err.message);
        }, { enableHighAccuracy: true });
    });
});
</script>
<?php include __DIR__ . '/../../template_footer.php'; ?>
