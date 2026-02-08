// ─── DATA FROM PHP ────────────────────────────────────────
// Data is now passed from UserController.php
const CATEGORIES = typeof categoriesData !== 'undefined' ? categoriesData : [];
const MESSAGES = typeof messagesData !== 'undefined' ? messagesData : [];
const FSL_ITEMS = typeof fslItemsData !== 'undefined' ? fslItemsData : [];

// ─── STATE ────────────────────────────────────────────────
let selected = new Set();
let activeCat = 'all';

// ─── RENDER CATEGORIES ────────────────────────────────────
function renderCategories() {
const grid = document.getElementById('catGrid');
grid.innerHTML = '';
CATEGORIES.forEach(c => {
const el = document.createElement('div');
el.className = 'cat-pill' + (c.id === activeCat ? ' active' : '');
el.dataset.cat = c.id;
el.onclick = () => filterCategory(c.id, el);
el.innerHTML = `<i class="${c.icon}"></i><span>${c.label}</span>`;
grid.appendChild(el);
});
}

// ─── FILTER CATEGORY ──────────────────────────────────────
function filterCategory(id) {
activeCat = id;
// update pills
document.querySelectorAll('.cat-pill').forEach(p => p.classList.toggle('active', p.dataset.cat === id));
renderMessages();
}

// ─── RENDER MESSAGES ──────────────────────────────────────
function renderMessages() {
const grid = document.getElementById('msgGrid');
grid.innerHTML = '';
const filtered = activeCat === 'all' ? MESSAGES : MESSAGES.filter(m => m.cat === activeCat);

filtered.forEach((m, i) => {
const wrap = document.createElement('div');
wrap.className = 'msg-card-wrap';
wrap.style.animationDelay = (i * 0.035) + 's';

const card = document.createElement('div');
card.className = 'msg-card' + (selected.has(m.id) ? ' selected' : '');
card.dataset.id = m.id;
card.onclick = () => toggleMessage(m.id, card);
card.innerHTML = `
<span class="sel-badge">✓ Selected</span>
<i class="${m.icon}"></i>
<div class="msg-title">${m.title}</div>
<div class="msg-desc">${m.desc}</div>`;

wrap.appendChild(card);
grid.appendChild(wrap);
});
}

// ─── TOGGLE MESSAGE ───────────────────────────────────────
function toggleMessage(id, card) {
if (selected.has(id)) {
selected.delete(id);
card.classList.remove('selected');
} else {
selected.add(id);
card.classList.add('selected');
}
bumpCounter();
updateSmsPreview();
updateButtons();
if (navigator.vibrate) navigator.vibrate(30);
}

// ─── COUNTER BUMP ANIMATION ──────────────────────────────
function bumpCounter() {
const el = document.getElementById('selCount');
el.textContent = selected.size;
el.classList.remove('bump');
void el.offsetWidth; // force reflow
el.classList.add('bump');
setTimeout(() => el.classList.remove('bump'), 300);
}

// ─── UPDATE SMS PREVIEW ───────────────────────────────────
function updateSmsPreview() {
const box = document.getElementById('smsPreviewBox');
const placeholder = document.getElementById('smsPlaceholder');
const content = document.getElementById('smsContent');

if (selected.size === 0) {
box.classList.add('empty');
placeholder.style.display = 'block';
content.style.display = 'none';
content.innerHTML = '';
return;
}

box.classList.remove('empty');
placeholder.style.display = 'none';
content.style.display = 'block';

let html = '';
MESSAGES.filter(m => selected.has(m.id)).forEach(m => {
html += `<div class="sms-msg-row">
<span class="sms-dot"></span>
<span class="sms-title">${m.title}:</span>
<span class="sms-desc">${m.desc}</span>
</div>`;
});
html += `<div class="sms-gps-tag">
<i class="ri-map-pin-line"></i>
GPS: 123 Tindahan ng Masayang Alala, Bacolod City
</div>`;
content.innerHTML = html;
}

// ─── ENABLE / DISABLE BUTTONS ─────────────────────────────
function updateButtons() {
const has = selected.size > 0;
document.getElementById('btnSend').disabled = !has;
document.getElementById('btnClear').disabled = !has;
}

// ─── SEND SMS ─────────────────────────────────────────────
function sendSMS() {
showToast('📤 SMS sent to your emergency contacts!', '#2e7d32');
// small delay so toast feels responsive
setTimeout(() => {
selected.clear();
bumpCounter();
updateSmsPreview();
updateButtons();
renderMessages(); // re-render to remove selected styles
}, 400);
}

// ─── CLEAR ALL ────────────────────────────────────────────
function clearAll() {
selected.clear();
bumpCounter();
updateSmsPreview();
updateButtons();
renderMessages();
showToast('🗑️ All selections cleared', '#7b1fa2');
}

// ─── CAMERA ───────────────────────────────────────────────
function camAction(type, btn) {
btn.style.transform = 'scale(.92)';
setTimeout(() => btn.style.transform = '', 160);
if (navigator.vibrate) navigator.vibrate(40);
showToast(
type === 'photo' ? '📸 Photo captured & GPS tagged!' : '🎥 Recording started…',
type === 'photo' ? '#388e3c' : '#7b1fa2'
);
}

// ─── FSL DOWNLOADS ────────────────────────────────────────
function renderFSL() {
const list = document.getElementById('fslList');
FSL_ITEMS.forEach((item, idx) => {
const wrap = document.createElement('div');
wrap.className = 'fsl-item';
wrap.innerHTML = `
<div class="fsl-item-header">
<i class="fsl-doc-icon ri-file-text-line"></i>
<div class="fsl-item-text">
<div class="fsl-title">${item.title}</div>
<div class="fsl-desc">${item.desc}</div>
</div>
</div>
<button class="dl-btn" id="dlBtn${idx}" onclick="startDownload(${idx})">
<i class="ri-download-line"></i> Download PDF
</button>
<div class="dl-progress-wrap" id="dlProg${idx}">
<div class="dl-progress-bar">
<div class="dl-progress-fill" id="dlFill${idx}"></div>
</div>
<div class="dl-progress-label" id="dlLabel${idx}">Downloading…</div>
</div>`;
list.appendChild(wrap);
});
}

function startDownload(idx) {
const btn = document.getElementById('dlBtn' + idx);
const wrap = document.getElementById('dlProg' + idx);
const fill = document.getElementById('dlFill' + idx);
const label = document.getElementById('dlLabel' + idx);

// reset
fill.style.width = '0%';
label.textContent = 'Downloading…';
label.className = 'dl-progress-label';
wrap.classList.add('visible');
btn.disabled = true;

let pct = 0;
const iv = setInterval(() => {
pct += Math.floor(Math.random() * 10) + 5;
if (pct >= 100) {
pct = 100;
clearInterval(iv);
fill.style.width = '100%';
label.textContent = '✓ Download complete!';
label.className = 'dl-progress-label done';
btn.innerHTML = '<i class="ri-check-line"></i> Downloaded';
btn.disabled = false;
showToast('📄 ' + FSL_ITEMS[idx].title + ' downloaded!', '#2e7d32');

// reset after 3 s
setTimeout(() => {
btn.innerHTML = '<i class="ri-download-line"></i> Download PDF';
wrap.classList.remove('visible');
fill.style.width = '0%';
label.textContent = 'Downloading…';
label.className = 'dl-progress-label';
}, 3000);
return;
}
fill.style.width = pct + '%';
label.textContent = 'Downloading… ' + pct + '%';
}, 130);
}

// ─── TOAST ────────────────────────────────────────────────
let toastTimer;
function showToast(msg, bg) {
const t = document.getElementById('toast');
t.textContent = msg;
t.style.background = bg;
t.classList.add('show');
clearTimeout(toastTimer);
toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}

// ─── INIT ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
renderCategories();
renderMessages();
renderFSL();
updateButtons();
});