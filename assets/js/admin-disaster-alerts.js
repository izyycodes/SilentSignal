// Admin Disaster Alerts Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeFilters();
    initializePagination();
    startWeatherMonitoring();
});

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            applyFilters();
        });
    }
}

// Filter functionality
function initializeFilters() {
    const severityFilter = document.getElementById('severityFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    if (severityFilter) {
        severityFilter.addEventListener('change', applyFilters);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', applyFilters);
    }
}

// Apply filters to table
function applyFilters() {
    const severityFilter = document.getElementById('severityFilter').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const severity = row.querySelector('.severity-badge').textContent.toLowerCase();
        const disasterType = row.querySelector('.disaster-badge').textContent.toLowerCase();
        const location = row.querySelector('.location-info').textContent.toLowerCase();
        
        const matchesSeverity = !severityFilter || severity.includes(severityFilter);
        const matchesType = !typeFilter || disasterType.includes(typeFilter);
        const matchesSearch = !searchTerm || 
            disasterType.includes(searchTerm) || 
            location.includes(searchTerm);
        
        if (matchesSeverity && matchesType && matchesSearch) {
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
    
    // Assuming max page is 5
    if (activePageNum === 5) {
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
        paginationInfo.textContent = `Showing 1-${Math.min(10, visibleRows)} of ${visibleRows} disaster alerts`;
    }
}

// Start weather monitoring
function startWeatherMonitoring() {
    // Check for weather updates every 5 minutes
    setInterval(() => {
        console.log('Checking for weather updates...');
        // TODO: Implement API call to weather service
        // fetchWeatherUpdates();
    }, 300000); // 5 minutes
}

// Fetch weather updates
function fetchWeatherUpdates() {
    // TODO: Implement weather API integration
    console.log('Fetching weather updates...');
}

// Show notification for severe weather
function showSevereWeatherAlert(alertData) {
    if (Notification.permission === 'granted') {
        new Notification('Severe Weather Alert', {
            body: alertData.message,
            icon: '/assets/images/weather-alert-icon.png',
            tag: 'severe-weather'
        });
    }
}

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Initialize on load
requestNotificationPermission();