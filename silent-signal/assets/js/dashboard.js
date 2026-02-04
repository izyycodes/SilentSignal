// Dashboard Shared JavaScript
// Handles header dropdown and mobile navigation

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // MOBILE NAVIGATION
    // ================================
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileNav = document.getElementById('mobileNav');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');
    const mobileNavClose = document.getElementById('mobileNavClose');
    
    function openMobileNav() {
        if (mobileNav) mobileNav.classList.add('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileNav() {
        if (mobileNav) mobileNav.classList.remove('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileNav);
    if (mobileNavClose) mobileNavClose.addEventListener('click', closeMobileNav);
    if (mobileNavOverlay) mobileNavOverlay.addEventListener('click', closeMobileNav);
    
    // Close mobile nav on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileNav();
            closeUserDropdown();
        }
    });
    
    // ================================
    // USER DROPDOWN
    // ================================
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    function toggleUserDropdown() {
        if (userDropdown) userDropdown.classList.toggle('active');
    }
    
    function closeUserDropdown() {
        if (userDropdown) {
            userDropdown.classList.remove('active');
        }
    }
    
    if (userDropdownBtn) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleUserDropdown();
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (userDropdown && !userDropdown.contains(e.target)) {
            closeUserDropdown();
        }
    });
    
    // Close dropdown when clicking a link inside
    if (userDropdownMenu) {
        userDropdownMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeUserDropdown);
        });
    }
    
    // ================================
    // WINDOW RESIZE HANDLER
    // ================================
    window.addEventListener('resize', function() {
        // Close mobile nav if window is resized to desktop
        if (window.innerWidth > 1024) {
            closeMobileNav();
        }
    });
    
});