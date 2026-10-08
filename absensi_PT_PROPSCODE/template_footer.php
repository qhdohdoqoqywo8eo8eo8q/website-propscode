<?php $is_admin_footer = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'; ?>

<?php if ($is_admin_footer) : ?>
    </div><!-- /.container-fluid -->
</div><!-- /.admin-main-content -->
<?php else : ?>
    </div><!-- /.container.main-content -->
<?php endif; ?>

<footer class="bg-white border-top py-3 mt-4">
    <div class="container text-center text-muted">&copy; <?= date('Y') ?> PT PROPSCODE Studio Teknologi</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="<?= asset_url('js/gps.js') ?>"></script>
<script src="<?= asset_url('js/script.js') ?>"></script>

<?php if ($is_admin_footer) : ?>
<script>
(function() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const closeBtn = document.getElementById('sidebarCloseBtn');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
})();
</script>
<?php endif; ?>

</body>
</html>
