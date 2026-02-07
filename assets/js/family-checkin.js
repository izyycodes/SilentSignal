// ── status button: persistent chosen ring + ripple ──
function setStatus(type, el){
el.parentElement.querySelectorAll('.status-btn').forEach(b => b.classList.remove('chosen'));
el.classList.add('chosen');
addRipple(el, type === 'safe' ? 'rgba(46,125,50,.25)' : 'rgba(198,40,40,.25)');

if (navigator.vibrate) {
navigator.vibrate(50);
}

showToast(
type === 'safe' ? '✅ Status updated – You are Safe!' : '🚨 Help request sent to your family!',
type === 'safe' ? '#2e7d32' : '#c62828'
);
}

// ── GPS pin ripple ──
function gpsTap(e, btn){
addRipple(btn, 'rgba(255,255,255,.4)');

if (navigator.vibrate) {
navigator.vibrate(30);
}

showToast('📍 Opening map…', '#1A4D7F');
}

// ── media button flash ──
function mediaTap(btn, msg){
btn.classList.add('flash');
btn.addEventListener('animationend', ()=> btn.classList.remove('flash'), {once:true});

if (navigator.vibrate) {
navigator.vibrate(40);
}

showToast(msg, '#7b1fa2');
}

// ── breadcrumb ripple ──
function breadcrumbTap(e, btn){
addRipple(btn, 'rgba(255,255,255,.35)');

if (navigator.vibrate) {
navigator.vibrate(30);
}

showToast('📍 Loading breadcrumbs…', '#1565c0');
}

// ── generic ripple helper ──
function addRipple(el, color){
const r = document.createElement('span');
r.className = 'ripple';
r.style.background = color;
const rect = el.getBoundingClientRect();
const size = Math.max(rect.width, rect.height);
r.style.width = r.style.height = size + 'px';
r.style.left = (rect.width / 2 - size / 2) + 'px';
r.style.top = (rect.height / 2 - size / 2) + 'px';
el.appendChild(r);
r.addEventListener('animationend', ()=> r.remove(), {once:true});
}

// ── toast ──
function showToast(message, bgColor){
const toast = document.getElementById('toast');
toast.textContent = message;
toast.style.background = bgColor;
toast.classList.add('show');
setTimeout(()=> toast.classList.remove('show'), 3000);
}