<?php
require_once __DIR__ . '/../../auth/session.php';
auth_pegawai();
require_once __DIR__ . '/../../config/config.php';

$userId = intval($_SESSION['user']['id']);
$date = date('Y-m-d');
$existing = fetch_one("SELECT id FROM absensi WHERE user_id = $userId AND tanggal = '$date'");
if ($existing) {
    flash('message', 'anda telah absen hari ini');
    redirect(base_url('pegawai/dashboard.php'));
}

// Check if clock-in window has opened yet
$currentTime = date('H:i:s');
if ($currentTime < START_TIME_MASUK) {
    $startTimeFormatted = date('H:i', strtotime(START_TIME_MASUK));
    flash('message', "Absensi masuk belum dibuka. Silakan melakukan absensi setelah pukul {$startTimeFormatted} WIB.");
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
                    <div class="bg-primary-light text-primary rounded-pill p-2 me-3 d-inline-flex"><i class="bi bi-box-arrow-in-right fs-4"></i></div>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">Absensi Masuk</h4>
                        <p class="text-muted small mb-0">Silakan ambil foto selfie dan konfirmasi lokasi Anda untuk melakukan absensi masuk.</p>
                    </div>
                </div>
                
                <form action="<?= base_url('pegawai/absensi/simpan_masuk.php') ?>" method="post">
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
                    
                    <input type="hidden" name="foto_masuk" id="foto_masuk">
                    <input type="hidden" name="latitude_masuk" id="latitude_masuk">
                    <input type="hidden" name="longitude_masuk" id="longitude_masuk">

                    <!-- Status & Keterangan Kehadiran -->
                    <?php 
                    $currentTime = date('H:i:s');
                    $isLate = ($currentTime > LIMIT_TIME_MASUK);
                    ?>

                    <?php if ($isLate) : ?>
                        <div class="alert alert-danger border-0 bg-danger-light text-danger d-flex align-items-center mb-4" role="alert" style="border-radius: 12px;">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                            <div>Batas waktu absen masuk telah berakhir (<?= htmlspecialchars(date('H:i', strtotime(LIMIT_TIME_MASUK))) ?> WIB). Anda terlambat dan hanya dapat mengajukan Izin atau Sakit dengan keterangan.</div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-check-circle me-1 text-primary"></i> Status Kehadiran</label>
                        <select name="status" id="status" class="form-select" required style="border-radius: 10px;">
                            <?php if (!$isLate) : ?>
                                <option value="Hadir">Hadir (Tepat Waktu)</option>
                            <?php endif; ?>
                            <option value="Izin" <?= $isLate ? 'selected' : '' ?>>Izin (Tidak Hadir)</option>
                            <option value="Sakit">Sakit (Tidak Hadir)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="keterangan-section" style="<?= !$isLate ? 'display: none;' : '' ?>">
                        <label class="form-label fw-semibold"><i class="bi bi-chat-text me-1 text-primary"></i> Keterangan Alasan Tidak Hadir</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan mengapa Anda tidak hadir atau izin hari ini..." style="border-radius: 10px;" <?= $isLate ? 'required' : '' ?>></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Lokasi Koordinat GPS</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="lokasi_masuk" name="lokasi_masuk" placeholder="Klik dapatkan lokasi untuk mengisi koordinat" readonly required>
                            <button type="button" class="btn btn-outline-secondary px-3" id="get-location"><i class="bi bi-pin-map-fill me-1"></i> Dapatkan Lokasi</button>
                        </div>
                        <div id="map" class="rounded-16 border bg-light shadow-sm" style="height: 250px; display: none;"></div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('pegawai/dashboard.php') ?>" class="btn btn-outline-secondary flex-grow-1 py-2.5"><i class="bi bi-arrow-left"></i> Batal</a>
                        <button type="submit" class="btn btn-success flex-grow-2 py-2.5 px-4"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Kirim Absensi Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
window.addEventListener('load', function() {
    var captureBtn = document.getElementById('capture-photo');
    captureBtn.disabled = true;
    var webcamReady = false;
    var maxWait = 5000; // ms
    var interval = 100; // ms
    var waited = 0;
    function initWebcam() {
        if (typeof Webcam !== 'undefined' && typeof Webcam.attach === 'function') {
            Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });
            try {
                Webcam.attach('#camera');
                webcamReady = true;
                captureBtn.disabled = false;
                Webcam.on('error', function(err) {
                    console.error('Webcam error:', err);
                    alert('Terjadi kesalahan pada webcam: ' + (err && err.name ? err.name : JSON.stringify(err)));
                });
            } catch (e) {
                console.error('Attach error:', e);
            }
            return;
        }
        if (waited >= maxWait) {
            console.warn('Webcam library not loaded after wait');
            alert('Webcam tidak tersedia. Pastikan browser mengizinkan akses kamera dan koneksi ke library berhasil.');
            return;
        }
        waited += interval;
        setTimeout(initWebcam, interval);
    }
    initWebcam();

    captureBtn.addEventListener('click', function() {
        if (!webcamReady || typeof Webcam === 'undefined' || typeof Webcam.snap !== 'function') {
            alert('Webcam belum siap. Tunggu beberapa saat lalu coba lagi.');
            return;
        }
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
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            
            document.getElementById('latitude_masuk').value = lat;
            document.getElementById('longitude_masuk').value = lng;
            document.getElementById('lokasi_masuk').value = lat + ', ' + lng;
            
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

    // Dynamic visibility and validation for Keterangan
    const statusSelect = document.getElementById('status');
    const keteranganSection = document.getElementById('keterangan-section');
    const keteranganInput = document.getElementById('keterangan');
    
    if (statusSelect && keteranganSection && keteranganInput) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'Hadir') {
                keteranganSection.style.display = 'none';
                keteranganInput.removeAttribute('required');
            } else {
                keteranganSection.style.display = '';
                keteranganInput.setAttribute('required', 'required');
            }
        });
    }
});
</script>
<?php include __DIR__ . '/../../template_footer.php'; ?>
