// Admin Messages Management JavaScript

document.addEventListener('DOMContentLoaded', function () {
    initializeSearch();
    initializeFilters();
    updatePaginationInfo();
});

// ─────────────────────────────────────────────
// SEARCH
// ─────────────────────────────────────────────
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
}

// ─────────────────────────────────────────────
// FILTERS
// ─────────────────────────────────────────────
function initializeFilters() {
    ['categoryFilter', 'priorityFilter', 'statusFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', applyFilters);
    });
}

function applyFilters() {
    const category = document.getElementById('categoryFilter').value.toLowerCase();
    const priority = document.getElementById('priorityFilter').value.toLowerCase();
    const status   = document.getElementById('statusFilter').value.toLowerCase();
    const search   = document.getElementById('searchInput').value.toLowerCase();

    const rows = document.querySelectorAll('.data-table tbody tr');

    rows.forEach(row => {
        // Read values directly from data attributes — reliable and consistent
        const rowCategory = (row.dataset.category || '').toLowerCase();
        const rowPriority = (row.dataset.priority || '').toLowerCase();
        const rowStatus   = (row.dataset.status   || '').toLowerCase();
        const rowName     = (row.querySelector('.user-name')?.textContent || '').toLowerCase();
        const rowSubject  = (row.querySelector('.subject')?.textContent   || '').toLowerCase();

        const matchesCategory = !category || rowCategory.includes(category);
        const matchesPriority = !priority || rowPriority === priority;
        const matchesStatus   = !status   || rowStatus   === status;
        const matchesSearch   = !search   || rowName.includes(search) || rowSubject.includes(search);

        row.style.display = (matchesCategory && matchesPriority && matchesStatus && matchesSearch)
            ? ''
            : 'none';
    });

    updatePaginationInfo();
}

function updatePaginationInfo() {
    const visible = document.querySelectorAll('.data-table tbody tr:not([style*="display: none"])').length;
    const total   = document.querySelectorAll('.data-table tbody tr').length;
    const info    = document.querySelector('.pagination-info');
    if (info) {
        const end = visible > 0 ? visible : 0;
        info.innerHTML = `Showing <strong>1-${end}</strong> of <strong>${total}</strong> messages`;
    }
}

// ─────────────────────────────────────────────
// MODAL — current message id held here
// ─────────────────────────────────────────────
let currentMessageId = null;

function viewMessage(row) {
    currentMessageId = row.dataset.id;

    const category = row.dataset.category || '';
    const priority = row.dataset.priority || '';
    const status   = row.dataset.status   || '';

    // ── Header ──
    document.getElementById('modalMessageId').textContent = row.dataset.messageId || '';

    // Category badge
    const catBadge  = document.getElementById('modalCategory');
    const catIcon   = getCategoryIcon(category);
    catBadge.className = `category-badge ${category}`;
    catBadge.innerHTML = `<i class="${catIcon}"></i> ${category}`;

    // Priority badge
    const priBadge  = document.getElementById('modalPriority');
    priBadge.className = `priority-badge ${priority}`;
    priBadge.textContent = priority.toUpperCase();

    // Status badge
    const staBadge  = document.getElementById('modalStatus');
    staBadge.className = `status-badge ${status}`;
    staBadge.textContent = formatStatus(status);

    // ── Sender info ──
    document.getElementById('modalName').textContent    = row.dataset.name    || '—';
    document.getElementById('modalEmail').textContent   = row.dataset.email   || '—';
    document.getElementById('modalDate').textContent    = row.dataset.date    || '—';
    document.getElementById('modalSubject').textContent = row.dataset.subject || '—';

    // ── Message body ──
    document.getElementById('modalMessage').textContent = row.dataset.message || '';

    // ── Previous reply (show only if it exists) ──
    const replySection  = document.getElementById('previousReplySection');
    const replyContent  = row.dataset.reply || '';
    const replyDate     = row.dataset.dateReplied || '';

    if (replyContent) {
        document.getElementById('modalReply').textContent      = replyContent;
        document.getElementById('modalDateReplied').textContent = replyDate ? `Sent on ${replyDate}` : '';
        replySection.style.display = '';
    } else {
        replySection.style.display = 'none';
    }

    // ── Clear reply textarea ──
    document.getElementById('replyTextarea').value = '';

    // ── Open ──
    document.getElementById('messageModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.remove('active');
    document.body.style.overflow = '';
    currentMessageId = null;
}

// ─────────────────────────────────────────────
// SEND REPLY — wire to backend
// ─────────────────────────────────────────────
function sendReply() {
    const reply = document.getElementById('replyTextarea').value.trim();

    if (!reply) {
        alert('Please enter a reply message.');
        return;
    }

    if (!confirm('Send this reply via email to the user?')) {
        return;
    }

    // Get current page from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 1;

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=admin-send-reply';

    const messageIdInput = document.createElement('input');
    messageIdInput.type = 'hidden';
    messageIdInput.name = 'message_id';
    messageIdInput.value = currentMessageId;

    const replyInput = document.createElement('input');
    replyInput.type = 'hidden';
    replyInput.name = 'reply_text';
    replyInput.value = reply;

    const pageInput = document.createElement('input');
    pageInput.type = 'hidden';
    pageInput.name = 'page';
    pageInput.value = currentPage;

    form.appendChild(messageIdInput);
    form.appendChild(replyInput);
    form.appendChild(pageInput);
    document.body.appendChild(form);
    form.submit();
}

// ─────────────────────────────────────────────
// MARK AS RESOLVED — wire to backend
// ─────────────────────────────────────────────
function markAsResolved() {
    if (!confirm('Mark this message as resolved?')) return;

    // Get current page from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 1;

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=admin-resolve-message';

    const messageIdInput = document.createElement('input');
    messageIdInput.type = 'hidden';
    messageIdInput.name = 'message_id';
    messageIdInput.value = currentMessageId;

    const pageInput = document.createElement('input');
    pageInput.type = 'hidden';
    pageInput.name = 'page';
    pageInput.value = currentPage;

    form.appendChild(messageIdInput);
    form.appendChild(pageInput);
    document.body.appendChild(form);
    form.submit();
}

// ─────────────────────────────────────────────
// UPDATE STATUS — from dropdown in table
// ─────────────────────────────────────────────
function updateStatus(messageId, newStatus, currentPage) {
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=admin-update-message-status';

    const messageIdInput = document.createElement('input');
    messageIdInput.type = 'hidden';
    messageIdInput.name = 'message_id';
    messageIdInput.value = messageId;

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = newStatus;

    const pageInput = document.createElement('input');
    pageInput.type = 'hidden';
    pageInput.name = 'page';
    pageInput.value = currentPage;

    form.appendChild(messageIdInput);
    form.appendChild(statusInput);
    form.appendChild(pageInput);
    document.body.appendChild(form);
    form.submit();
}

// ─────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────
function getCategoryIcon(category) {
    const icons = {
        'Emergency': 'ri-alarm-warning-line',
        'Technical': 'ri-tools-line',
        'Feedback':  'ri-chat-smile-2-line',
        'Support':   'ri-customer-service-2-line',
        'General':   'ri-question-line',
    };
    return icons[category] || 'ri-message-3-line';
}

function formatStatus(status) {
    return status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
}

// ─────────────────────────────────────────────
// CLOSE MODAL — click outside or Escape
// ─────────────────────────────────────────────
document.addEventListener('click', function (e) {
    const modal = document.getElementById('messageModal');
    if (e.target === modal) closeMessageModal();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeMessageModal();
});