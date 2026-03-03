// ============================================================
// COMMUNICATION HUB - Full Functional JS
// ============================================================

// ── Data from PHP ──
const CATEGORIES = typeof categoriesData !== 'undefined' ? categoriesData : [];
const MESSAGES   = typeof messagesData   !== 'undefined' ? messagesData   : [];
const FSL_ITEMS  = typeof fslItemsData   !== 'undefined' ? fslItemsData   : [];
const CONTACTS   = typeof emergencyContactsData !== 'undefined' ? emergencyContactsData : [];
const USER_INFO  = typeof userInfoData   !== 'undefined' ? userInfoData   : {};

// ── State ──
let selected   = new Set();
let activeCat  = 'all';
let hubLat     = null;
let hubLng     = null;
let gpsReady   = false;

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    renderCategories();
    renderMessages();
    renderFSL();
    updateButtons();
    startGPS();
});

// ── GPS ──
function startGPS() {
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(
        pos => {
            hubLat = pos.coords.latitude;
            hubLng = pos.coords.longitude;
            gpsReady = true;
        },
        null,
        { enableHighAccuracy: true, timeout: 10000 }
    );
    navigator.geolocation.watchPosition(
        pos => { hubLat = pos.coords.latitude; hubLng = pos.coords.longitude; gpsReady = true; },
        null,
        { enableHighAccuracy: true }
    );
}

// ── Render Categories ──
function renderCategories() {
    const grid = document.getElementById('catGrid');
    grid.innerHTML = '';
    CATEGORIES.forEach(c => {
        const el = document.createElement('div');
        el.className = 'cat-pill' + (c.id === activeCat ? ' active' : '');
        el.dataset.cat = c.id;
        el.onclick = () => filterCategory(c.id);
        el.innerHTML = `<i class="${c.icon}"></i><span>${c.label}</span>`;
        grid.appendChild(el);
    });
}

// ── Filter Category ──
function filterCategory(id) {
    activeCat = id;
    document.querySelectorAll('.cat-pill').forEach(p => p.classList.toggle('active', p.dataset.cat === id));
    renderMessages();
}

// ── Render Messages ──
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
<div class="msg-title">${escHtml(m.title)}</div>
<div class="msg-desc">${escHtml(m.desc)}</div>`;
        wrap.appendChild(card);
        grid.appendChild(wrap);
    });
}

// ── Toggle Message ──
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

// ── Counter Animation ──
function bumpCounter() {
    const el = document.getElementById('selCount');
    el.textContent = selected.size;
    el.classList.remove('bump');
    void el.offsetWidth;
    el.classList.add('bump');
    setTimeout(() => el.classList.remove('bump'), 300);
}

// ── Update SMS Preview ──
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

    const selectedMsgs = MESSAGES.filter(m => selected.has(m.id));
    let html = '';

    // User info header
    if (USER_INFO.name) {
        html += `<div class="sms-from-row"><i class="ri-user-line"></i> <strong>${escHtml(USER_INFO.name)}</strong>${USER_INFO.pwdId ? ' (PWD: ' + escHtml(USER_INFO.pwdId) + ')' : ''}</div>`;
    }

    selectedMsgs.forEach(m => {
        html += `<div class="sms-msg-row">
<span class="sms-dot"></span>
<span class="sms-title">${escHtml(m.title)}:</span>
<span class="sms-desc">${escHtml(m.desc)}</span>
</div>`;
    });

    const locationStr = gpsReady && hubLat
        ? `Lat: ${hubLat.toFixed(6)}, Lng: ${hubLng.toFixed(6)}`
        : (USER_INFO.address || 'Location unavailable');

    html += `<div class="sms-gps-tag">
<i class="ri-map-pin-line"></i>
GPS: ${escHtml(locationStr)}
</div>`;

    if (USER_INFO.bloodType) {
        html += `<div class="sms-medical-tag"><i class="ri-heart-pulse-line"></i> Blood Type: ${escHtml(USER_INFO.bloodType)}</div>`;
    }

    content.innerHTML = html;
}

// ── Enable/Disable Buttons ──
function updateButtons() {
    const has = selected.size > 0;
    document.getElementById('btnSend').disabled = !has;
    document.getElementById('btnClear').disabled = !has;
}

// ── Send SMS ──
function sendSMS() {
    if (selected.size === 0) return;

    const btn = document.getElementById('btnSend');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Sending...';

    const selectedMsgs = MESSAGES.filter(m => selected.has(m.id)).map(m => ({ id: m.id, title: m.title, desc: m.desc }));
    const contactsToNotify = CONTACTS.map(c => ({ name: c.name, phone: c.phone }));

    fetch(BASE_URL + 'index.php?action=send-hub-sms', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            messages: selectedMsgs,
            contacts: contactsToNotify,
            latitude: hubLat,
            longitude: hubLng,
            locationLabel: gpsReady && hubLat ? `Lat: ${hubLat.toFixed(6)}, Lng: ${hubLng.toFixed(6)}` : null
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.innerHTML = '<i class="ri-send-plane-fill"></i> Send SMS';
        if (data.success) {
            showToast('📤 SMS sent to your emergency contacts!', '#2e7d32');
            setTimeout(() => {
                selected.clear();
                bumpCounter();
                updateSmsPreview();
                updateButtons();
                renderMessages();
            }, 400);
        } else {
            btn.disabled = false;
            showToast('⚠️ ' + (data.message || 'Could not send. Try again.'), '#d84315');
        }
    })
    .catch(() => {
        btn.innerHTML = '<i class="ri-send-plane-fill"></i> Send SMS';
        btn.disabled = false;
        showToast('⚠️ Network error. Try again.', '#d84315');
    });
}

// ── Clear All ──
function clearAll() {
    selected.clear();
    bumpCounter();
    updateSmsPreview();
    updateButtons();
    renderMessages();
    showToast('🗑️ All selections cleared', '#7b1fa2');
}

// ── Camera ──
function camAction(type, btn) {
    btn.style.transform = 'scale(.92)';
    setTimeout(() => btn.style.transform = '', 160);
    if (navigator.vibrate) navigator.vibrate(40);

    const inputId = type === 'photo' ? 'hubCameraPhoto' : 'hubCameraVideo';
    const input = document.getElementById(inputId);
    if (input) {
        input.click();
    } else {
        showToast(
            type === 'photo' ? '📸 Photo captured & GPS tagged!' : '🎥 Recording started…',
            type === 'photo' ? '#388e3c' : '#7b1fa2'
        );
        logHubMedia(type);
    }
}

function handleHubCapture(input, type) {
    if (input.files && input.files[0]) {
        showToast(
            type === 'photo' ? '📸 Photo captured & GPS tagged!' : '🎥 Video captured!',
            type === 'photo' ? '#388e3c' : '#7b1fa2'
        );
        logHubMedia(type);
        input.value = '';
    }
}

function logHubMedia(type) {
    fetch(BASE_URL + 'index.php?action=log-hub-media', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type, latitude: hubLat, longitude: hubLng })
    }).catch(() => {});
}

// ── FSL Downloads ──
function renderFSL() {
    const list = document.getElementById('fslList');
    FSL_ITEMS.forEach((item, idx) => {
        const wrap = document.createElement('div');
        wrap.className = 'fsl-item';
        wrap.innerHTML = `
<div class="fsl-item-header">
  <i class="fsl-doc-icon ri-file-text-line"></i>
  <div class="fsl-item-text">
    <div class="fsl-title">${escHtml(item.title)}</div>
    <div class="fsl-desc">${escHtml(item.desc)}</div>
  </div>
</div>
<button class="dl-btn" id="dlBtn${idx}" onclick="startDownload(${idx})">
  <i class="ri-download-line"></i> Download PDF
</button>
<div class="dl-progress-wrap" id="dlProg${idx}">
  <div class="dl-progress-bar"><div class="dl-progress-fill" id="dlFill${idx}"></div></div>
  <div class="dl-progress-label" id="dlLabel${idx}">Downloading…</div>
</div>`;
        list.appendChild(wrap);
    });
}

function startDownload(idx) {
    const btn   = document.getElementById('dlBtn'   + idx);
    const wrap  = document.getElementById('dlProg'  + idx);
    const fill  = document.getElementById('dlFill'  + idx);
    const label = document.getElementById('dlLabel' + idx);

    fill.style.width    = '0%';
    label.textContent   = 'Downloading…';
    label.className     = 'dl-progress-label';
    wrap.classList.add('visible');
    btn.disabled        = true;

    // Simulated progress (actual download link would be served from server)
    let pct = 0;
    const iv = setInterval(() => {
        pct += Math.floor(Math.random() * 10) + 5;
        if (pct >= 100) {
            pct = 100;
            clearInterval(iv);
            fill.style.width    = '100%';
            label.textContent   = '✓ Download complete!';
            label.className     = 'dl-progress-label done';
            btn.innerHTML       = '<i class="ri-check-line"></i> Downloaded';
            btn.disabled        = false;
            showToast('📄 ' + FSL_ITEMS[idx].title + ' downloaded!', '#2e7d32');

            // Offer actual file download if it exists
            if (FSL_ITEMS[idx].file) {
                const a = document.createElement('a');
                a.href     = BASE_URL + 'assets/fsl/' + FSL_ITEMS[idx].file;
                a.download = FSL_ITEMS[idx].file;
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }

            setTimeout(() => {
                btn.innerHTML     = '<i class="ri-download-line"></i> Download PDF';
                wrap.classList.remove('visible');
                fill.style.width  = '0%';
                label.textContent = 'Downloading…';
                label.className   = 'dl-progress-label';
            }, 3000);
            return;
        }
        fill.style.width  = pct + '%';
        label.textContent = 'Downloading… ' + pct + '%';
    }, 130);
}

// ── Toast ──
let toastTimer;
function showToast(msg, bg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = bg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}

// ── HTML Escape ──
function escHtml(str) {
    if (str == null) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
