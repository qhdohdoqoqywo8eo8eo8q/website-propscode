<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="mb-4">
            <a href="<?= base_url('admin/pegawai/index.php') ?>" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1.5 small fw-semibold">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pegawai
            </a>
        </div>
        
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
            <div class="card-header bg-success text-white p-4 border-0 position-relative">
                <div class="position-relative" style="z-index: 2;">
                    <h5 class="fw-bold mb-1"><i class="bi bi-person-plus-fill me-2 text-white"></i>Tambah Pegawai</h5>
                    <p class="text-white-50 small mb-0">Buat akun pegawai baru untuk sistem absensi PT PROPSCODE.</p>
                </div>
                <div class="position-absolute" style="top: -20px; right: -20px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(0,0,0,0) 70%); filter: blur(15px); pointer-events: none; z-index: 1;"></div>
            </div>
            <div class="card-body p-4 p-sm-5">
                <form action="<?= base_url('admin/pegawai/simpan.php') ?>" method="post">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="fullname" class="form-control border-start-0 ps-0" placeholder="Masukkan nama lengkap pegawai..." required style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-at"></i></span>
                                <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Username login..." required style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control border-start-0 ps-0" placeholder="Nomor telepon/WA..." style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="Alamat email aktif..." style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Masukkan password yang aman..." required style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-2">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="<?= base_url('admin/pegawai/index.php') ?>" class="btn btn-light px-4 py-2" style="border-radius: 10px;">Batal</a>
                                <button type="submit" class="btn btn-success fw-bold px-4 py-2 text-white" style="border-radius: 10px;">Simpan Pegawai</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../template_footer.php'; ?>
