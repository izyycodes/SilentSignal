// Admin Users Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeFilters();
});

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
}

// Filter functionality
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const roleFilter = document.getElementById('roleFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }
}

// Apply filters to table
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const statusBadge = row.querySelector('.status-badge');
        const roleBadge = row.querySelector('.role-badge');
        const userName = row.querySelector('.user-name');
        const userEmail = row.querySelector('.user-email');
        
        const status = statusBadge ? statusBadge.textContent.toLowerCase() : '';
        const role = roleBadge ? roleBadge.textContent.toLowerCase() : '';
        const name = userName ? userName.textContent.toLowerCase() : '';
        const email = userEmail ? userEmail.textContent.toLowerCase() : '';
        
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        const matchesRole = !roleFilter || role.includes(roleFilter);
        const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
        
        if (matchesStatus && matchesRole && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// View user details
function viewUser(userId) {
    console.log('Viewing user:', userId);
    // TODO: Implement view user modal or redirect to user detail page
    alert('View user details for ID: ' + userId + '\n\nThis feature will show the user\'s full profile, medical information, and activity history.');
}

// Verify user account
function verifyUser(userId, currentPage) {
    if (confirm('✅ Verify this user account?\n\nThey will gain full access to the Silent Signal system.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=admin-verify-user';

        const userIdInput = document.createElement('input');
        userIdInput.type = 'hidden';
        userIdInput.name = 'user_id';
        userIdInput.value = userId;

        const pageInput = document.createElement('input');
        pageInput.type = 'hidden';
        pageInput.name = 'page';
        pageInput.value = currentPage;

        form.appendChild(userIdInput);
        form.appendChild(pageInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Toggle user active status (activate/deactivate)
function toggleActive(userId, currentPage) {
    if (confirm('⚠️ Change this user\'s account status?\n\nThis will activate or deactivate their access to the system.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=admin-toggle-active';

        const userIdInput = document.createElement('input');
        userIdInput.type = 'hidden';
        userIdInput.name = 'user_id';
        userIdInput.value = userId;

        const pageInput = document.createElement('input');
        pageInput.type = 'hidden';
        pageInput.name = 'page';
        pageInput.value = currentPage;

        form.appendChild(userIdInput);
        form.appendChild(pageInput);
        document.body.appendChild(form);
        form.submit();
    }
}