// Communication Hub JavaScript
// SMS messaging functionality for deaf/mute users

const BASE_URL = document.querySelector('meta[name="base-url"]')?.content || '';

// Global state
let selectedMessage = '';
let selectedType = '';

document.addEventListener('DOMContentLoaded', function() {
    
    initializeQuickMessages();
    initializeCustomMessage();
    initializeContactSelection();
    initializeModal();
    loadRecentMessages();
    
});

// ================================
// QUICK MESSAGE BUTTONS
// ================================
function initializeQuickMessages() {
    const quickMsgBtns = document.querySelectorAll('.quick-msg-btn');
    
    quickMsgBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from all buttons
            quickMsgBtns.forEach(b => b.classList.remove('active'));
            
            // Add active to clicked button
            this.classList.add('active');
            
            // Get message data
            selectedMessage = this.dataset.message;
            selectedType = this.dataset.type;
            
            // Update preview
            updatePreview(selectedMessage);
            
            // Show preview card
            document.getElementById('previewCard').style.display = 'block';
            
            // Show confirmation modal
            showSendConfirmation();
        });
    });
}

// ================================
// CUSTOM MESSAGE
// ================================
function initializeCustomMessage() {
    const textarea = document.getElementById('customMessage');
    const charCount = document.getElementById('charCount');
    const sendBtn = document.getElementById('sendCustomBtn');
    
    if (textarea) {
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            // Change color if near limit
            if (count > 450) {
                charCount.parentElement.style.color = '#f44336';
            } else if (count > 400) {
                charCount.parentElement.style.color = '#ff9800';
            } else {
                charCount.parentElement.style.color = '#888';
            }
        });
    }
    
    if (sendBtn) {
        sendBtn.addEventListener('click', function() {
            const message = textarea.value.trim();
            
            if (!message) {
                showNotification('Please enter a message', 'error');
                textarea.focus();
                return;
            }
            
            // Clear active from quick buttons
            document.querySelectorAll('.quick-msg-btn').forEach(b => b.classList.remove('active'));
            
            selectedMessage = message;
            selectedType = 'custom';
            
            updatePreview(message);
            document.getElementById('previewCard').style.display = 'block';
            
            showSendConfirmation();
        });
    }
}

// ================================
// CONTACT SELECTION
// ================================
function initializeContactSelection() {
    const selectAllBtn = document.getElementById('selectAllContacts');
    const checkboxes = document.querySelectorAll('input[name="selectedContacts"]');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            
            this.textContent = allChecked ? 'Select All' : 'Deselect All';
            updateRecipientCount();
        });
    }
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateRecipientCount);
    });
    
    // Initial count
    updateRecipientCount();
}

function updateRecipientCount() {
    const checked = document.querySelectorAll('input[name="selectedContacts"]:checked');
    const countEl = document.getElementById('recipientCount');
    const confirmCountEl = document.getElementById('confirmRecipients');
    
    if (countEl) countEl.textContent = checked.length;
    if (confirmCountEl) confirmCountEl.textContent = checked.length;
}

function getSelectedContacts() {
    const checked = document.querySelectorAll('input[name="selectedContacts"]:checked');
    const contacts = [];
    
    checked.forEach(cb => {
        contacts.push({
            phone: cb.value,
            name: cb.dataset.name
        });
    });
    
    return contacts;
}

// ================================
// PREVIEW
// ================================
function updatePreview(message) {
    const previewText = document.getElementById('previewText');
    if (previewText) {
        previewText.textContent = message;
    }
    updateRecipientCount();
}

// ================================
// CONFIRMATION MODAL
// ================================
function initializeModal() {
    const modal = document.getElementById('sendConfirmModal');
    const cancelBtn = document.getElementById('cancelSend');
    const confirmBtn = document.getElementById('confirmSend');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }
    
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            sendMessage();
        });
    }
    
    // Close on backdrop click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    }
}

function showSendConfirmation() {
    const contacts = getSelectedContacts();
    
    if (contacts.length === 0) {
        showNotification('Please select at least one contact', 'error');
        return;
    }
    
    const modal = document.getElementById('sendConfirmModal');
    const confirmText = document.getElementById('confirmText');
    
    // Update confirmation text
    const contactNames = contacts.slice(0, 3).map(c => c.name).join(', ');
    const moreText = contacts.length > 3 ? ` and ${contacts.length - 3} more` : '';
    confirmText.textContent = `Send to: ${contactNames}${moreText}`;
    
    updateRecipientCount();
    
    modal.classList.add('active');
}

// ================================
// SEND MESSAGE (SMS INTENT)
// ================================
function sendMessage() {
    const contacts = getSelectedContacts();
    
    if (contacts.length === 0) {
        showNotification('No contacts selected', 'error');
        return;
    }
    
    if (!selectedMessage) {
        showNotification('No message selected', 'error');
        return;
    }
    
    // Build message with sender info
    const userData = document.getElementById('userData');
    const senderName = userData?.dataset.name || 'Silent Signal User';
    
    let fullMessage = selectedMessage;
    fullMessage += `\n\n---\nFrom: ${senderName}`;
    fullMessage += `\n⚠️ This person is DEAF/MUTE - Please respond via TEXT only.`;
    
    // Get phone numbers
    const phoneNumbers = contacts.map(c => c.phone.replace(/\s/g, '')).join(',');
    
    // Open SMS app
    openSMSIntent(phoneNumbers, fullMessage);
    
    // Save to recent messages
    saveRecentMessage(selectedMessage, selectedType, contacts);
    
    // Show success
    showNotification('Opening SMS app...', 'success');
    
    // Vibrate feedback
    if ('vibrate' in navigator) {
        navigator.vibrate([100, 50, 100]);
    }
    
    // Clear custom message if used
    if (selectedType === 'custom') {
        document.getElementById('customMessage').value = '';
        document.getElementById('charCount').textContent = '0';
    }
    
    // Reset selection
    selectedMessage = '';
    selectedType = '';
    document.querySelectorAll('.quick-msg-btn').forEach(b => b.classList.remove('active'));
}

// ================================
// SMS INTENT
// ================================
function openSMSIntent(phoneNumbers, message) {
    const encodedMessage = encodeURIComponent(message);
    
    // Detect platform
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    
    let smsUrl;
    if (isIOS) {
        // iOS uses & for body
        smsUrl = `sms:${phoneNumbers}&body=${encodedMessage}`;
    } else {
        // Android and others use ?body=
        smsUrl = `sms:${phoneNumbers}?body=${encodedMessage}`;
    }
    
    window.location.href = smsUrl;
}

// ================================
// RECENT MESSAGES
// ================================
function saveRecentMessage(message, type, contacts) {
    const recent = JSON.parse(localStorage.getItem('recentMessages') || '[]');
    
    recent.unshift({
        message: message,
        type: type,
        contacts: contacts.map(c => c.name).join(', '),
        timestamp: new Date().toISOString()
    });
    
    // Keep only last 10
    if (recent.length > 10) {
        recent.pop();
    }
    
    localStorage.setItem('recentMessages', JSON.stringify(recent));
    loadRecentMessages();
}

function loadRecentMessages() {
    const container = document.getElementById('recentMessages');
    if (!container) return;
    
    const recent = JSON.parse(localStorage.getItem('recentMessages') || '[]');
    
    if (recent.length === 0) {
        container.innerHTML = `
            <div class="empty-recent">
                <i class="ri-chat-off-line"></i>
                <p>No recent messages</p>
            </div>
        `;
        return;
    }
    
    const typeIcons = {
        emergency: 'ri-alarm-warning-fill',
        medical: 'ri-hospital-fill',
        safety: 'ri-shield-fill',
        fire: 'ri-fire-fill',
        safe: 'ri-check-double-fill',
        home: 'ri-home-heart-fill',
        onway: 'ri-run-fill',
        late: 'ri-time-fill',
        medicine: 'ri-capsule-fill',
        food: 'ri-restaurant-fill',
        water: 'ri-drop-fill',
        ride: 'ri-car-fill',
        callback: 'ri-phone-fill',
        visit: 'ri-user-follow-fill',
        groceries: 'ri-shopping-cart-fill',
        help: 'ri-question-fill',
        custom: 'ri-edit-2-fill'
    };
    
    const typeColors = {
        emergency: '#e53935',
        medical: '#e53935',
        safety: '#e53935',
        fire: '#e53935',
        safe: '#4caf50',
        home: '#4caf50',
        onway: '#2196f3',
        late: '#ff9800',
        medicine: '#9c27b0',
        food: '#ff9800',
        water: '#2196f3',
        ride: '#607d8b',
        callback: '#2196f3',
        visit: '#9c27b0',
        groceries: '#4caf50',
        help: '#ff9800',
        custom: '#1A4D7F'
    };
    
    container.innerHTML = recent.map(msg => {
        const icon = typeIcons[msg.type] || 'ri-message-2-fill';
        const color = typeColors[msg.type] || '#888';
        const time = formatRelativeTime(msg.timestamp);
        
        return `
            <div class="recent-message-item">
                <div class="recent-icon" style="background: ${color}20; color: ${color};">
                    <i class="${icon}"></i>
                </div>
                <div class="recent-content">
                    <p class="recent-text">${escapeHtml(msg.message.substring(0, 50))}${msg.message.length > 50 ? '...' : ''}</p>
                    <span class="recent-meta">To: ${escapeHtml(msg.contacts)} • ${time}</span>
                </div>
                <button class="btn-resend" data-message="${escapeHtml(msg.message)}" data-type="${msg.type}">
                    <i class="ri-repeat-fill"></i>
                </button>
            </div>
        `;
    }).join('');
    
    // Add resend functionality
    container.querySelectorAll('.btn-resend').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedMessage = this.dataset.message;
            selectedType = this.dataset.type;
            updatePreview(selectedMessage);
            document.getElementById('previewCard').style.display = 'block';
            showSendConfirmation();
        });
    });
}

function formatRelativeTime(timestamp) {
    const now = new Date();
    const then = new Date(timestamp);
    const diff = Math.floor((now - then) / 1000);
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
    return then.toLocaleDateString();
}

// ================================
// HELPERS
// ================================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    // Remove existing
    document.querySelectorAll('.notification').forEach(n => n.remove());
    
    const colors = {
        success: '#4caf50',
        error: '#f44336',
        info: '#2196f3',
        warning: '#ff9800'
    };
    
    const icons = {
        success: 'ri-check-line',
        error: 'ri-error-warning-line',
        info: 'ri-information-line',
        warning: 'ri-alert-line'
    };
    
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `
        <i class="${icons[type]}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 15px 20px;
        background: ${colors[type]};
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
`;
document.head.appendChild(style);