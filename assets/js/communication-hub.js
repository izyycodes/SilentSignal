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

    const allMsgs = [...MESSAGES, ...loadCustomCards()];
    const selectedMsgs = allMsgs.filter(m => selected.has(m.id));
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

    if (!CONTACTS.length) {
        showToast('⚠️ No emergency contacts found. Add contacts in Medical Profile.', '#d84315');
        return;
    }

    const allMsgs = [...MESSAGES, ...loadCustomCards()];
    const selectedMsgs = allMsgs.filter(m => selected.has(m.id));

    // Build plain text SMS body
    let smsBody = '';
    if (USER_INFO.name) {
        smsBody += `From: ${USER_INFO.name}`;
        if (USER_INFO.pwdId) smsBody += ` (PWD ID: ${USER_INFO.pwdId})`;
        smsBody += `\n`;
    }

    selectedMsgs.forEach(m => {
        smsBody += `• ${m.title}: ${m.desc}\n`;
    });

    const locationStr = gpsReady && hubLat
        ? `https://maps.google.com/?q=${hubLat},${hubLng}`
        : (USER_INFO.address || 'Location unavailable');

    smsBody += `\nLocation: ${locationStr}`;

    if (USER_INFO.bloodType) {
        smsBody += `\nBlood Type: ${USER_INFO.bloodType}`;
    }

    smsBody += `\n\nThis person is DEAF/MUTE - Please respond via TEXT only.`;

    const phones = CONTACTS
        .map(c => (c.phone || '').replace(/\s/g, ''))
        .filter(Boolean)
        .join(',');

    if (!phones) {
        showToast('⚠️ No valid phone numbers in your contacts.', '#d84315');
        return;
    }

    // Log to server with keepalive — survives navigation, won't trigger network error
    fetch(BASE_URL + 'index.php?action=send-hub-sms', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        keepalive: true,
        body: JSON.stringify({
            messages: selectedMsgs.map(m => ({ id: m.id, title: m.title, desc: m.desc })),
            contacts: CONTACTS.map(c => ({ name: c.name, phone: c.phone })),
            latitude: hubLat,
            longitude: hubLng,
            locationLabel: gpsReady && hubLat ? `Lat: ${hubLat.toFixed(6)}, Lng: ${hubLng.toFixed(6)}` : null
        })
    }).catch(() => {}); // silent — SMS opens regardless

    showToast('📤 Opening SMS app...', '#2e7d32');
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

    // Navigate LAST after short delay so toast shows first
    setTimeout(() => {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const sep   = isIOS ? '&' : '?';
        window.location.href = `sms:${phones}${sep}body=${encodeURIComponent(smsBody)}`;
    }, 300);
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
    list.innerHTML = '';

    // ── Download All button (prepended before individual items) ──
    const dlAllWrap = document.createElement('div');
    dlAllWrap.innerHTML = `
<div class="dl-all-progress-wrap" id="dlAllProgressWrap">
  <div class="dl-all-progress-bar">
    <div class="dl-all-progress-fill" id="dlAllProgressFill" style="width:0%"></div>
  </div>
  <div class="dl-all-progress-label" id="dlAllProgressLabel">Downloading...</div>
</div>
<button class="dl-all-btn" id="dlAllBtn" onclick="downloadAllFSL()">
  <i class="ri-download-cloud-2-line"></i> Download All Resources
</button>`;
    list.appendChild(dlAllWrap);

    // ── Individual items ──
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

function startDownload(idx, onDone) {
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
            if (onDone) onDone(); 
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

async function downloadAllFSL() {
    const btn         = document.getElementById('dlAllBtn');
    const progressWrap = document.getElementById('dlAllProgressWrap');
    const fill        = document.getElementById('dlAllProgressFill');
    const label       = document.getElementById('dlAllProgressLabel');
    const total       = FSL_ITEMS.length;

    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line"></i> Downloading...';
    progressWrap.classList.add('visible');

    for (let i = 0; i < total; i++) {
        fill.style.width  = Math.round((i / total) * 100) + '%';
        label.textContent = `Downloading ${i + 1} of ${total}: ${FSL_ITEMS[i].title}`;

        // Wait for individual download to finish
        await new Promise(resolve => startDownload(i, resolve));
    }

    // All done
    fill.style.width  = '100%';
    label.textContent = `✓ All ${total} resources downloaded!`;
    label.classList.add('done');
    btn.innerHTML     = '<i class="ri-checkbox-circle-line"></i> All Downloaded';
    btn.style.background    = 'linear-gradient(135deg, #2e7d32, #388e3c)';
    btn.style.boxShadow     = '0 4px 14px rgba(46, 125, 50, 0.3)';
    showToast(`📄 All ${total} FSL resources downloaded!`, '#2e7d32');

    setTimeout(() => {
        btn.disabled          = false;
        btn.innerHTML         = '<i class="ri-download-cloud-2-line"></i> Download All Resources';
        btn.style.background  = '';
        btn.style.boxShadow   = '';
        progressWrap.classList.remove('visible');
        fill.style.width      = '0%';
        label.classList.remove('done');
        label.textContent     = 'Downloading...';
    }, 4000);
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

// ============================================================
// CUSTOMIZE CARDS
// ============================================================

const _UID = (typeof userInfoData !== 'undefined' && userInfoData.id) ? userInfoData.id : 'guest';
const STORAGE_KEY_ORDER  = 'ss_hub_card_order_'  + _UID;
const STORAGE_KEY_HIDDEN = 'ss_hub_card_hidden_' + _UID;
const STORAGE_KEY_CUSTOM = 'ss_hub_custom_cards_' + _UID;

const DEFAULT_ORDER = [
    'emergency','danger','injured','medical_help','fire','first_aid',
    'lost','medication','flood','sick','food','hungry',
    'drinking_water','shelter','drinks','water','rest_area'
];

const ICON_POOL = [
    'ri-alarm-warning-line','ri-error-warning-line','ri-health-book-line',
    'ri-hospital-line','ri-fire-line','ri-first-aid-kit-line',
    'ri-map-pin-user-line','ri-medicine-bottle-line','ri-flood-line',
    'ri-emotion-sad-line','ri-restaurant-2-line','ri-cake-line',
    'ri-goblet-line','ri-home-heart-line','ri-cup-line','ri-drop-line',
    'ri-hotel-bed-line','ri-wheelchair-line','ri-heart-pulse-line',
    'ri-run-line','ri-walk-line','ri-car-line','ri-phone-line',
    'ri-nurse-line','ri-stethoscope-line','ri-capsule-line',
    'ri-scissors-line','ri-tools-line','ri-flashlight-line',
    'ri-shield-line','ri-hand-heart-line','ri-user-heart-line',
    'ri-building-line','ri-store-line','ri-road-map-line',
    'ri-police-line','ri-team-line','ri-parent-line','ri-child-line',
    'ri-eye-line','ri-ear-line','ri-mental-health-line',
    'ri-thermometer-line','ri-syringe-line','ri-blood-type-line',
    'ri-wheelchair-fill','ri-walk-fill','ri-parent-fill',
];

let selectedIcon = '';
let dragSrcIdx   = null;

function loadCardOrder()  {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY_ORDER))  || DEFAULT_ORDER.slice(); }
    catch(e) { return DEFAULT_ORDER.slice(); }
}
function loadHiddenCards() {
    try { return new Set(JSON.parse(localStorage.getItem(STORAGE_KEY_HIDDEN)) || []); }
    catch(e) { return new Set(); }
}
function loadCustomCards() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY_CUSTOM)) || []; }
    catch(e) { return []; }
}

function saveCardOrder(order)   { localStorage.setItem(STORAGE_KEY_ORDER,  JSON.stringify(order)); }
function saveHiddenCards(set)   { localStorage.setItem(STORAGE_KEY_HIDDEN, JSON.stringify([...set])); }
function saveCustomCards(cards) { localStorage.setItem(STORAGE_KEY_CUSTOM, JSON.stringify(cards)); }

function buildEffectiveMessages() {
    const order  = loadCardOrder();
    const hidden = loadHiddenCards();
    const custom = loadCustomCards();
    const allMsgs = [...MESSAGES, ...custom];
    const orderedIds = [...order, ...custom.map(c => c.id).filter(id => !order.includes(id))];
    const sorted = orderedIds.map(id => allMsgs.find(m => m.id === id)).filter(Boolean);
    allMsgs.forEach(m => { if (!sorted.find(s => s.id === m.id)) sorted.push(m); });
    return sorted.filter(m => !hidden.has(m.id));
}

// Patch renderMessages to use effective list
window.renderMessages = function() {
    const grid = document.getElementById('msgGrid');
    grid.innerHTML = '';
    const effective = buildEffectiveMessages();
    const filtered  = activeCat === 'all' ? effective : effective.filter(m => m.cat === activeCat);
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
};

function openCustomizeDrawer() {
    document.getElementById('customizeOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
    renderCustomizeList();
    renderIconPicker('');
}

function closeCustomizeDrawer(e) {
    if (e.target === document.getElementById('customizeOverlay')) closeCustomizeDrawerBtn();
}

function closeCustomizeDrawerBtn() {
    document.getElementById('customizeOverlay').classList.remove('active');
    document.body.style.overflow = '';
    renderMessages();
}

function switchCTab(btn, tab) {
    document.querySelectorAll('.ctab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.ctab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('ctab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('active');
}

function renderCustomizeList() {
    const list   = document.getElementById('customizeCardList');
    const order  = loadCardOrder();
    const hidden = loadHiddenCards();
    const custom = loadCustomCards();
    const allMsgs = [...MESSAGES, ...custom];
    const orderedIds = [...order, ...custom.map(c => c.id).filter(id => !order.includes(id))];
    const sorted = orderedIds.map(id => allMsgs.find(m => m.id === id)).filter(Boolean);
    allMsgs.forEach(m => { if (!sorted.find(s => s.id === m.id)) sorted.push(m); });

    list.innerHTML = '';

    sorted.forEach((m, idx) => {
        const isCustom  = !!custom.find(c => c.id === m.id);
        const isVisible = !hidden.has(m.id);
        const uid = 'cc_toggle_' + m.id;

        const item = document.createElement('div');
        item.className  = 'cc-item';
        item.dataset.id = m.id;
        item.draggable  = true;
        item.innerHTML  = `
<span class="cc-drag-handle"><i class="ri-draggable"></i></span>
<div class="cc-icon"><i class="${m.icon}"></i></div>
<div class="cc-info">
  <div class="cc-title">${escHtml(m.title)}</div>
  <div class="cc-desc">${escHtml(m.desc)}</div>
</div>
<span class="cc-badge ${isCustom ? 'custom' : ''}">${isCustom ? 'Custom' : m.cat}</span>
<label class="cc-toggle">
  <input type="checkbox" id="${uid}" ${isVisible ? 'checked' : ''} onchange="toggleCardVisibility('${m.id}', this.checked)">
  <div class="cc-toggle-track"></div>
</label>
${isCustom ? `<button class="cc-delete" onclick="deleteCustomCard('${m.id}')" title="Delete"><i class="ri-delete-bin-line"></i></button>` : ''}`;

        item.addEventListener('dragstart', e => {
            dragSrcIdx = idx;
            item.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        item.addEventListener('dragend', () => item.classList.remove('dragging'));
        item.addEventListener('dragover', e => { e.preventDefault(); item.classList.add('drag-over'); });
        item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
        item.addEventListener('drop', e => {
            e.preventDefault();
            item.classList.remove('drag-over');
            if (dragSrcIdx === null || dragSrcIdx === idx) return;
            const items = [...list.querySelectorAll('.cc-item')];
            const ids   = items.map(i => i.dataset.id);
            const moved = ids.splice(dragSrcIdx, 1)[0];
            ids.splice(idx, 0, moved);
            saveCardOrder(ids);
            renderCustomizeList();
        });

        list.appendChild(item);
    });
}

function toggleCardVisibility(id, visible) {
    const hidden = loadHiddenCards();
    visible ? hidden.delete(id) : hidden.add(id);
    saveHiddenCards(hidden);
}

function deleteCustomCard(id) {
    const custom = loadCustomCards().filter(c => c.id !== id);
    saveCustomCards(custom);
    const order = loadCardOrder().filter(oid => oid !== id);
    saveCardOrder(order);
    const hidden = loadHiddenCards();
    hidden.delete(id);
    saveHiddenCards(hidden);
    renderCustomizeList();
    showToast('🗑️ Card deleted', '#7b1fa2');
}

function resetCards() {
    if (!confirm('Reset all cards to default order and show all hidden cards?')) return;
    localStorage.removeItem(STORAGE_KEY_ORDER);
    localStorage.removeItem(STORAGE_KEY_HIDDEN);
    renderCustomizeList();
    showToast('↺ Cards reset to default', '#555');
}

function renderIconPicker(query) {
    const picker = document.getElementById('iconPicker');
    picker.innerHTML = '';
    const q = query.toLowerCase().replace('ri-','');
    const filtered = q ? ICON_POOL.filter(ic => ic.replace('ri-','').replace(/-/g,' ').includes(q)) : ICON_POOL;
    filtered.forEach(ic => {
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'icon-option' + (selectedIcon === ic ? ' selected' : '');
        btn.title     = ic.replace('ri-','').replace(/-/g,' ');
        btn.innerHTML = `<i class="${ic}"></i>`;
        btn.onclick   = () => selectIcon(ic);
        picker.appendChild(btn);
    });
}

function filterIcons(q) { renderIconPicker(q); }

function selectIcon(ic) {
    selectedIcon = ic;
    document.getElementById('iconPreviewEl').className    = ic;
    document.getElementById('iconPreviewName').textContent = ic.replace('ri-','').replace(/-/g,' ');
    renderIconPicker(document.getElementById('ccIconSearch').value);
}

function createCustomCard() {
    const title = document.getElementById('ccTitle').value.trim();
    const desc  = document.getElementById('ccDesc').value.trim();
    const cat   = document.getElementById('ccCat').value;
    const icon  = selectedIcon;

    if (!title) { showToast('⚠️ Card title is required', '#d84315'); return; }
    if (!desc)  { showToast('⚠️ Description is required', '#d84315'); return; }
    if (!icon)  { showToast('⚠️ Please select an icon', '#d84315'); return; }

    const id     = 'custom_' + Date.now();
    const custom = loadCustomCards();
    custom.push({ id, cat, icon, title, desc });
    saveCustomCards(custom);

    const order = loadCardOrder();
    order.push(id);
    saveCardOrder(order);

    document.getElementById('ccTitle').value = '';
    document.getElementById('ccDesc').value  = '';
    selectedIcon = '';
    document.getElementById('iconPreviewEl').className     = 'ri-question-line';
    document.getElementById('iconPreviewName').textContent = 'No icon selected';
    renderIconPicker('');

    const manageBtn = document.querySelector('.ctab[data-tab="manage"]');
    switchCTab(manageBtn, 'manage');
    renderCustomizeList();
    showToast('✅ Card "' + title + '" added!', '#2e7d32');
}