// Admin Emergency Alerts Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeFilters();
    initializePagination();
    startAutoRefresh();
});

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            applyFilters();
        });
    }
}

// Filter functionality
function initializeFilters() {
    const priorityFilter = document.getElementById('priorityFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    if (priorityFilter) {
        priorityFilter.addEventListener('change', applyFilters);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
}

// Apply filters to table
function applyFilters() {
    const priorityFilter = document.getElementById('priorityFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const priority = row.querySelector('.priority-badge').textContent.toLowerCase();
        const status = row.querySelector('.status-badge').textContent.toLowerCase();
        const userName = row.querySelector('.user-name').textContent.toLowerCase();
        const location = row.querySelector('.location-info').textContent.toLowerCase();
        
        const matchesPriority = !priorityFilter || priority.includes(priorityFilter);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        const matchesSearch = !searchTerm || 
            userName.includes(searchTerm) || 
            location.includes(searchTerm);
        
        if (matchesPriority && matchesStatus && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updatePaginationInfo();
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
    
    // Assuming max page is 7
    if (activePageNum === 7) {
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
        paginationInfo.textContent = `Showing 1-${Math.min(10, visibleRows)} of ${visibleRows} emergency alerts`;
    }
}

// View alert details
function viewAlert(alertId) {
    console.log('Viewing alert:', alertId);
    // TODO: Implement view alert modal with details
    alert('View alert details for ID: ' + alertId);
}

// View location on map
function viewLocation(alertId) {
    console.log('Viewing location for alert:', alertId);
    // TODO: Implement map view modal
    alert('View location on map for alert ID: ' + alertId);
}

// Resolve alert
function resolveAlert(alertId) {
    if (confirm('Mark this alert as resolved?')) {
        console.log('Resolving alert:', alertId);
        // TODO: Implement resolve alert API call
        alert('Alert resolved: ' + alertId);
        // Update UI
        updateAlertStatus(alertId, 'resolved');
    }
}

// Update alert status in UI
function updateAlertStatus(alertId, newStatus) {
    const rows = document.querySelectorAll('.data-table tbody tr');
    rows.forEach(row => {
        const alertIdCell = row.querySelector('.alert-id');
        if (alertIdCell && alertIdCell.textContent.includes(alertId)) {
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = `status-badge ${newStatus}`;
                statusBadge.textContent = newStatus.toUpperCase();
            }
        }
    });
}

// Auto-refresh alerts every 30 seconds
function startAutoRefresh() {
    setInterval(() => {
        console.log('Auto-refreshing alerts...');
        // TODO: Implement AJAX call to fetch new alerts
        // refreshAlerts();
    }, 30000);
}

// Play alert sound for critical alerts
function playAlertSound() {
    // TODO: Implement alert sound
    const audio = new Audio('/assets/sounds/alert.mp3');
    audio.play().catch(e => console.log('Audio play failed:', e));
}

// Check for new critical alerts
function checkCriticalAlerts() {
    const criticalAlerts = document.querySelectorAll('.priority-badge.critical');
    if (criticalAlerts.length > 0) {
        // Show notification or play sound
        console.log('Critical alerts detected:', criticalAlerts.length);
    }
}

// Initialize on load
checkCriticalAlerts();