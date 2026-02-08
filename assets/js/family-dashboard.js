// Emergency Actions
function handleEmergencyCall() {
if (confirm('Call emergency services (911)?')) {
window.location.href = 'tel:911';
}
}

function viewLocation(lat, lng) {
window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
}

function sendMessage(pwdId) {
alert('Opening communication hub...');
window.location.href = '<?php echo BASE_URL; ?>index.php?action=communication-hub&pwd_id=' + pwdId;
}

function viewProfile(pwdId) {
alert('Viewing PWD profile...');
window.location.href = '<?php echo BASE_URL; ?>index.php?action=view-pwd-profile&id=' + pwdId;
}

function callMember(phone) {
if (confirm(`Call ${phone}?`)) {
window.location.href = 'tel:' + phone;
}
}

function messageMember(memberId) {
alert('Opening messaging...');
}

// Emergency FAB
let fabOpen = false;

function triggerEmergencyActions() {
const fabMenu = document.querySelector('.fab-menu');
fabOpen = !fabOpen;

if (fabOpen) {
fabMenu.style.display = 'flex';
setTimeout(() => {
fabMenu.classList.add('active');
}, 10);
} else {
fabMenu.classList.remove('active');
setTimeout(() => {
fabMenu.style.display = 'none';
}, 300);
}
}

function callEmergencyServices() {
if (confirm('Call emergency services (911)?')) {
window.location.href = 'tel:911';
}
}

function alertAllFamily() {
if (confirm('Send emergency alert to all family members?')) {
alert('Emergency alert sent to all family members!');
}
}

function viewEmergencyContacts() {
window.location.href = '<?php echo BASE_URL; ?>index.php?action=medical-profile#emergency-contacts';
}

// Close FAB menu when clicking outside
document.addEventListener('click', function(event) {
const fab = document.querySelector('.emergency-fab');
if (fabOpen && !fab.contains(event.target)) {
triggerEmergencyActions();
}
});