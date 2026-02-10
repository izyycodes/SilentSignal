// Admin Users Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeFilters();
    initializePagination();
});

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterTable(searchTerm);
        });
    }
}

// Filter functionality
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const roleFilter = document.getElementById('roleFilter');
    const disabilityFilter = document.getElementById('disabilityFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }
    
    if (disabilityFilter) {
        disabilityFilter.addEventListener('change', applyFilters);
    }
}

// Apply filters to table
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
    const disabilityFilter = document.getElementById('disabilityFilter').value.toLowerCase();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const status = row.querySelector('.status-badge').textContent.toLowerCase();
        const role = row.querySelector('.role-badge').textContent.toLowerCase();
        const disability = row.querySelector('.disability-badge').textContent.toLowerCase();
        const userName = row.querySelector('.user-name').textContent.toLowerCase();
        const userEmail = row.querySelector('.user-email').textContent.toLowerCase();
        
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        const matchesRole = !roleFilter || role.includes(roleFilter);
        const matchesDisability = !disabilityFilter || disability.includes(disabilityFilter);
        const matchesSearch = !searchTerm || 
            userName.includes(searchTerm) || 
            userEmail.includes(searchTerm);
        
        if (matchesStatus && matchesRole && matchesDisability && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updatePaginationInfo();
}

// Filter table by search term
function filterTable(searchTerm) {
    applyFilters();
}

// Initialize pagination
function initializePagination() {
    const pageNumbers = document.querySelectorAll('.page-number');
    const prevBtn = document.querySelector('.page-btn:first-child');
    const nextBtn = document.querySelector('.page-btn:last-child');
    
    pageNumbers.forEach(btn => {
        btn.addEventListener('click', function() {
            pageNumbers.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            updatePaginationButtons();
        });
    });
    
    if (prevBtn) {
        prevBtn.addEventListener('click', goToPreviousPage);
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', goToNextPage);
    }
}

// Update pagination buttons state
function updatePaginationButtons() {
    const activePageNum = parseInt(document.querySelector('.page-number.active').textContent);
    const prevBtn = document.querySelector('.page-btn:first-child');
    const nextBtn = document.querySelector('.page-btn:last-child');
    
    if (activePageNum === 1) {
        prevBtn.disabled = true;
    } else {
        prevBtn.disabled = false;
    }
    
    // Assuming max page is 97 (from the mock data)
    if (activePageNum === 97) {
        nextBtn.disabled = true;
    } else {
        nextBtn.disabled = false;
    }
}

// Go to previous page
function goToPreviousPage() {
    const activeBtn = document.querySelector('.page-number.active');
    const prevBtn = activeBtn.previousElementSibling;
    
    if (prevBtn && prevBtn.classList.contains('page-number')) {
        activeBtn.classList.remove('active');
        prevBtn.classList.add('active');
        updatePaginationButtons();
    }
}

// Go to next page
function goToNextPage() {
    const activeBtn = document.querySelector('.page-number.active');
    const nextBtn = activeBtn.nextElementSibling;
    
    if (nextBtn && nextBtn.classList.contains('page-number')) {
        activeBtn.classList.remove('active');
        nextBtn.classList.add('active');
        updatePaginationButtons();
    }
}

// Update pagination info
function updatePaginationInfo() {
    const visibleRows = document.querySelectorAll('.data-table tbody tr:not([style*="display: none"])').length;
    const paginationInfo = document.querySelector('.pagination-info');
    
    if (paginationInfo) {
        paginationInfo.textContent = `Showing 1-${Math.min(10, visibleRows)} of ${visibleRows} accounts`;
    }
}

// View user details
function viewUser(userId) {
    console.log('Viewing user:', userId);
    // TODO: Implement view user modal or redirect
    alert('View user details for ID: ' + userId);
}

// Edit user
function editUser(userId) {
    console.log('Editing user:', userId);
    // TODO: Implement edit user modal or redirect
    alert('Edit user with ID: ' + userId);
}

// Delete user
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        console.log('Deleting user:', userId);
        // TODO: Implement delete user API call
        alert('User deleted: ' + userId);
    }
}