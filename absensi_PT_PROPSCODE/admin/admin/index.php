<?php
require_once __DIR__ . '/../../auth/session.php';
auth_admin();
require_once __DIR__ . '/../../template_header.php';
$admins = fetch_all("SELECT * FROM users WHERE role = 'admin' ORDER BY created_at DESC");
$total_admins = count($admins);
?>

<div class="row g-4 mb-4">
    <!-- Header Title & Description -->
    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock-fill text-warning me-2"></i>Data Admin</h4>
            <p class="text-muted small mb-0">Kelola dan pantau seluruh akun administrator sistem.</p>
        </div>
        <a href="<?= base_url('admin/admin/tambah.php') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm py-2 px-3">
            <i class="bi bi-person-plus-fill fs-5"></i>
            <span>Tambah Admin Baru</span>
        </a>
    </div>

    <!-- Quick Stats Widgets -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-white position-relative overflow-hidden" style="border-radius: 16px;">
            <div class="card-body p-4 position-relative" style="z-index: 2;">
                <h6 class="text-white-50 fw-semibold mb-1">Total Admin Terdaftar</h6>
                <h2 class="fw-extrabold mb-0"><?= $total_admins ?> <span class="fs-6 fw-normal opacity-75">Administrator</span></h2>
            </div>
            <div class="position-absolute opacity-10" style="bottom: -20px; right: -10px; font-size: 8rem; line-height: 1; pointer-events: none;">
                <i class="bi bi-shield-lock"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="w-100">
                    <h6 class="fw-bold text-dark mb-2">Cari Data Admin</h6>
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="adminSearchInput" class="form-control py-2.5 border-0 bg-light" placeholder="Masukkan nama, username, email, atau telepon..." style="border-radius: 12px; font-size: 0.95rem;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin List Card -->
<div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white border-bottom py-3 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">Daftar Akun Administrator</h6>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 font-monospace small"><span id="filteredCount"><?= $total_admins ?></span> / <?= $total_admins ?> Terlihat</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 60px;">#</th>
                    <th>Administrator</th>
                    <th>Username</th>
                    <th>Kontak</th>
                    <th>Tanggal Registrasi</th>
                    <th class="pe-4 text-end" style="width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="adminTableBody">
                <?php if (empty($admins)) : ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-slash d-block fs-1 mb-2 opacity-50"></i>
                            Belum ada data administrator terdaftar.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($admins as $index => $admin) : 
                        // Initial Avatar Generation
                        $words = explode(" ", $admin['fullname']);
                        $initials = "";
                        if (count($words) >= 2) {
                            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($admin['fullname'], 0, 2));
                        }
                    ?>
                        <tr class="admin-row" data-search="<?= htmlspecialchars(strtolower($admin['fullname'] . ' ' . $admin['username'] . ' ' . $admin['email'] . ' ' . $admin['phone'])) ?>">
                            <td class="ps-4 text-muted font-monospace"><?= $index + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle avatar-admin me-3">
                                        <?= htmlspecialchars($initials) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($admin['fullname']) ?></div>
                                        <div class="text-muted small" style="font-size: 0.8rem;"><?= htmlspecialchars($admin['email'] ?: 'Tidak ada email') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="border-radius: 8px;">
                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($admin['username']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <?php if ($admin['phone']) : ?>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $admin['phone']) ?>" target="_blank" class="text-decoration-none text-muted d-block mb-0.5">
                                            <i class="bi bi-whatsapp text-success me-1"></i> <?= htmlspecialchars($admin['phone']) ?>
                                        </a>
                                    <?php else : ?>
                                        <span class="text-white-50 small">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($admin['created_at'])) ?>
                                    <div class="text-white-50" style="font-size: 0.75rem;"><?= date('H:i', strtotime($admin['created_at'])) ?> WIB</div>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="<?= base_url('admin/admin/edit.php?id=' . $admin['id']) ?>" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1.5 py-1.5 px-3" style="border-radius: 8px;">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="<?= base_url('admin/admin/hapus.php?id=' . $admin['id']) ?>" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5 py-1.5 px-3" style="border-radius: 8px;" onclick="return confirm('Apakah Anda yakin ingin menghapus akun administrator ini?')">
                                        <i class="bi bi-trash"></i>
                                        <span>Hapus</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('adminSearchInput');
    const tableBody = document.getElementById('adminTableBody');
    const rows = tableBody.getElementsByClassName('admin-row');
    const filteredCount = document.getElementById('filteredCount');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const searchData = row.getAttribute('data-search');
            
            if (searchData.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
        
        filteredCount.textContent = visibleCount;
    });
});
</script>

<?php include __DIR__ . '/../../template_footer.php'; ?>
