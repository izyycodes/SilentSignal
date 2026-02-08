<?php
// views/includes/admin-footer.php
?>
    </div>
</main>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
// Sidebar toggle functionality
const sidebar = document.getElementById('adminSidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const mobileMenuBtn = document.getElementById('mobileMenuBtn');

// Desktop toggle
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
}

// Mobile toggle
if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', () => {
        sidebar.classList.add('mobile-open');
        sidebarOverlay.classList.add('active');
    });
}

// Close sidebar on overlay click
if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        sidebarOverlay.classList.remove('active');
    });
}

// Restore sidebar state
const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
if (sidebarCollapsed === 'true') {
    sidebar.classList.add('collapsed');
}
</script>