// Admin Messages Management JavaScript

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
            applyFilters();
        });
    }
}

// Filter functionality
function initializeFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
}

// Apply filters to table
function applyFilters() {
    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const category = row.querySelector('.category-badge').textContent.toLowerCase();
        const userName = row.querySelector('.user-name').textContent.toLowerCase();
        const subject = row.querySelector('.subject').textContent.toLowerCase();
        
        const matchesCategory = !categoryFilter || category.includes(categoryFilter);
        const matchesSearch = !searchTerm || 
            userName.includes(searchTerm) || 
            subject.includes(searchTerm);
        
        if (matchesCategory && matchesSearch) {
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
    
    // Assuming max page is 16
    if (activePageNum === 16) {
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
        paginationInfo.textContent = `Showing 1-${Math.min(10, visibleRows)} of ${visibleRows} messages`;
    }
}

// View message details
function viewMessage(messageId) {
    console.log('Viewing message:', messageId);
    
    // Show modal
    const modal = document.getElementById('messageModal');
    const modalBody = document.getElementById('messageModalBody');
    
    // TODO: Fetch message details from API
    // For now, show placeholder
    modalBody.innerHTML = `
        <div class="message-detail">
            <div class="detail-section">
                <h4>Message Information</h4>
                <p><strong>Message ID:</strong> ${messageId}</p>
                <p><strong>Subject:</strong> Sample Subject</p>
                <p><strong>Category:</strong> Technical Support</p>
                <p><strong>Priority:</strong> High</p>
                <p><strong>Date:</strong> Jan 15, 2024</p>
            </div>
            <div class="detail-section">
                <h4>Message Content</h4>
                <p>Full message content will be displayed here...</p>
            </div>
            <div class="detail-section">
                <h4>Reply</h4>
                <textarea class="reply-textarea" placeholder="Type your reply here..." rows="5"></textarea>
                <div class="modal-actions">
                    <button class="btn-primary" onclick="sendReply(${messageId})">
                        <i class="ri-send-plane-line"></i> Send Reply
                    </button>
                    <button class="btn-secondary" onclick="markAsResolved(${messageId})">
                        <i class="ri-check-line"></i> Mark as Resolved
                    </button>
                </div>
            </div>
        </div>
    `;
    
    modal.classList.add('active');
}

// Close message modal
function closeMessageModal() {
    const modal = document.getElementById('messageModal');
    modal.classList.remove('active');
}

// Send reply
function sendReply(messageId) {
    const replyText = document.querySelector('.reply-textarea').value;
    
    if (!replyText.trim()) {
        alert('Please enter a reply message.');
        return;
    }
    
    console.log('Sending reply to message:', messageId);
    console.log('Reply:', replyText);
    
    // TODO: Implement API call to send reply
    alert('Reply sent successfully!');
    closeMessageModal();
}

// Mark as resolved
function markAsResolved(messageId) {
    if (confirm('Mark this message as resolved?')) {
        console.log('Marking message as resolved:', messageId);
        // TODO: Implement API call
        alert('Message marked as resolved!');
        closeMessageModal();
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('messageModal');
    if (event.target === modal) {
        closeMessageModal();
    }
}

// Escape key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeMessageModal();
    }
});