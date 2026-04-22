// ============================================================
// SILENT SIGNAL — CHATBOT WIDGET JS (HYBRID VERSION)
// ============================================================

// OFFLINE KNOWLEDGE BASE
// These are ONLY used when the user has no internet connection
const SS_KNOWLEDGE = [
    {
        keywords: ['sos', 'emergency', 'help', 'alert'],
        response: `To send an SOS:
• Tap the red SOS button on the dashboard
• Your location and medical profile will be sent to your emergency contacts
• Make sure GPS is enabled`
    },
    {
        keywords: ['medical', 'profile', 'setup'],
        response: `To set up your Medical Profile:
• Go to Profile page
• Enter blood type, allergies, medications
• Add emergency contacts`
    },
    {
        keywords: ['flood'],
        response: `Flood Safety:
• Move to higher ground
• Avoid floodwater
• Prepare your go bag`
    },
    {
        keywords: ['earthquake'],
        response: `Earthquake Safety:
• Drop, Cover, Hold
• Stay away from windows`
    },
    {
        keywords: ['family', 'check'],
        response: `Family Check-In:
• Tap "I'm Safe"
• Your family will be notified instantly`
    }
];

// OFFLINE MATCHING — only called when navigator.onLine is false
function ssGetOfflineResponse(message) {
    const msg = message.toLowerCase();
    for (let item of SS_KNOWLEDGE) {
        for (let keyword of item.keywords) {
            if (msg.includes(keyword)) return item.response;
        }
    }
    return `I'm currently offline. Try asking about SOS, flood, earthquake, or profile setup when you're back online.`;
}

// COOLDOWN (ANTI-SPAM)
let lastRequestTime = 0;

// ============================================================
// SYSTEM PROMPT FOR SIGNARA
// ============================================================
const SS_SYSTEM_PROMPT = `You are Signara, the friendly assistant for Silent Signal — a disaster preparedness and emergency communication app designed specifically for deaf and mute PWD (Persons with Disability) users in the Philippines.


You help users with:
1. HOW TO USE SILENT SIGNAL
   - Sending SOS alerts (tap the red SOS button on the Dashboard/Emergency Alert page; it sends GPS location + medical profile via SMS to all emergency contacts)
   - Setting up the Medical Profile (blood type, conditions, allergies, medications, emergency contacts)
   - Using the Communication Hub (visual icon cards to communicate needs without speaking; customizable cards)
   - Family Check-In system (tap "I'm Safe" to notify family; family members get live status updates)
   - Disaster Monitoring (real-time PAGASA/NDRRMC alerts based on your registered location)
   - Emergency Alert page (allow GPS location, send alert with location to contacts)
   - Forgot password / account recovery


2. PWD EMERGENCY TIPS
   - Always keep Medical Profile updated — it is sent automatically with every SOS
   - Pre-load the Communication Hub page while online so it works offline
   - Keep phone charged; enable vibration alerts for silent environments
   - Share your PWD ID and blood type with emergency responders using the Communication Hub
   - Download FSL (Filipino Sign Language) resources from the Communication Hub for offline use


3. APP TROUBLESHOOTING
   - SOS not sending: check internet/data connection and browser location permissions
   - Not receiving notifications: enable browser notifications in device settings
   - Location not detected: use a device with GPS hardware; allow location in browser settings
   - Page not loading: clear browser cache; use Chrome, Firefox, Safari, or Edge
   - Forgot password: use the Forgot Password link on the Login page
   - Family member not linked: ensure they registered using the same phone number you linked


4. GENERAL DISASTER PREPAREDNESS (Philippines context)
   - Typhoon: move to higher ground, avoid flood-prone areas, follow PAGASA advisories
   - Earthquake: Drop-Cover-Hold On; move away from windows and heavy furniture
   - Flood: never walk through floodwater; evacuate early before roads are impassable
   - Fire: crawl low under smoke; never use elevators; meet at designated assembly point
   - Prepare a Go Bag: water, food, medicines, documents, flashlight, whistle, phone charger
   

Tone: Warm, calm, clear, and concise. Use short sentences. Bullet points when listing steps.
Language: Respond in the same language the user writes in (English or Filipino/Tagalog).
Limits: You are NOT a substitute for emergency services. If someone describes an active emergency, always remind users to call 911 or local emergency numbers for life-threatening situations immediately in addition to using the app. Keep responses under 120 words unless the user asks for more detail. Never provide medical diagnoses.`;

let ssChatHistory = [];
let ssChatOpen    = false;
let ssChatLoading = false;

function ssChatToggle() {
    ssChatOpen = !ssChatOpen;
    const win   = document.getElementById('ssChatWindow');
    const icon  = document.querySelector('.ss-chat-fab-icon');
    const close = document.querySelector('.ss-chat-fab-close');
    win.style.display   = ssChatOpen ? 'flex' : 'none';
    icon.style.display  = ssChatOpen ? 'none'  : 'block';
    close.style.display = ssChatOpen ? 'block' : 'none';
    if (ssChatOpen) setTimeout(() => document.getElementById('ssChatInput').focus(), 200);
}

function openChatbot() {
    if (!ssChatOpen) ssChatToggle();
}

function ssChatClear() {
    ssChatHistory = [];
    document.getElementById('ssChatMessages').innerHTML = `
    <div class="ss-chat-welcome">
        <div class="ss-chat-welcome-icon"><i class="ri-sparkling-2-fill"></i></div>
        <div class="ss-chat-welcome-text">
            <strong>Hi! I'm Signara</strong>
            <p>I can help you with Silent Signal features, emergency tips, and disaster preparedness. What do you need?</p>
        </div>
    </div>`;
}

function ssAskChip(btn) {
    ssSubmitMessage(btn.textContent.trim());
}

function ssChatKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); ssChatSend(); }
}

function ssAutoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}

function ssChatSend() {
    const input = document.getElementById('ssChatInput');
    const text  = input.value.trim();
    if (!text || ssChatLoading) return;

    // ⏱️ COOLDOWN CHECK
    const now = Date.now();
    if (now - lastRequestTime < 1500) {
        ssAppendMessage('assistant', 'Please wait a moment before sending another message.');
        return;
    }
    lastRequestTime = now;

    input.value = '';
    input.style.height = 'auto';
    ssSubmitMessage(text);
}

// MAIN FUNCTION
function ssSubmitMessage(text) {
    ssAppendMessage('user', text);
    ssChatHistory.push({ role: 'user', content: text });

    // ── OFFLINE MODE — use local knowledge base only ───────────
    if (!navigator.onLine) {
        const offlineReply = ssGetOfflineResponse(text);
        ssChatHistory.push({ role: 'assistant', content: offlineReply });
        ssAppendMessage('assistant', offlineReply, true);
        return;
    }

    // ── ONLINE MODE — always send to Llama AI via Groq ─────────
    ssShowTyping();
    ssChatLoading = true;
    document.getElementById('ssChatSendBtn').disabled = true;

    const baseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '/';

    fetch(baseUrl + 'index.php?action=chatbot-proxy', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            system: SS_SYSTEM_PROMPT,
            messages: ssChatHistory.slice(-6)
        })
    })
    .then(r => r.json())
    .then(data => {
        ssRemoveTyping();
        const reply = data?.content?.[0]?.text ||
            "I'm not sure about that yet. Try asking about SOS or disaster tips.";
        ssChatHistory.push({ role: 'assistant', content: reply });
        ssAppendMessage('assistant', reply);
    })
    .catch(() => {
        // fetch failed mid-request — fall back to offline knowledge base
        ssRemoveTyping();
        const fallback = ssGetOfflineResponse(text);
        ssChatHistory.push({ role: 'assistant', content: fallback });
        ssAppendMessage('assistant', fallback, true);
    })
    .finally(() => {
        ssChatLoading = false;
        document.getElementById('ssChatSendBtn').disabled = false;
        document.getElementById('ssChatInput').focus();
    });
}

// ============================================================
// UI HELPERS (UNCHANGED)
// ============================================================

function ssAppendMessage(role, text, isError) {
    const msgs = document.getElementById('ssChatMessages');
    const div  = document.createElement('div');
    div.className = 'ss-msg ' + role + (isError ? ' ss-msg-error' : '');

    const avatar = document.createElement('div');
    avatar.className = 'ss-msg-avatar';
    avatar.innerHTML = role === 'user'
        ? '<i class="ri-user-3-line"></i>'
        : '<i class="ri-sparkling-2-fill"></i>';

    const bubble = document.createElement('div');
    bubble.className = 'ss-msg-bubble';
    bubble.innerHTML = ssFormatText(text);

    div.appendChild(avatar);
    div.appendChild(bubble);
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function ssShowTyping() {
    const msgs = document.getElementById('ssChatMessages');
    const div  = document.createElement('div');
    div.className = 'ss-msg assistant ss-typing';
    div.id = 'ssTypingIndicator';
    div.innerHTML = `
        <div class="ss-msg-avatar"><i class="ri-sparkling-2-fill"></i></div>
        <div class="ss-msg-bubble">
            <div class="ss-typing-dot"></div>
            <div class="ss-typing-dot"></div>
            <div class="ss-typing-dot"></div>
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function ssRemoveTyping() {
    const el = document.getElementById('ssTypingIndicator');
    if (el) el.remove();
}

function ssFormatText(text) {
    let html = text
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')
        .replace(/\*(.*?)\*/g,'<em>$1</em>');

    const lines = html.split('\n');
    let inList = false;
    let out = '';

    lines.forEach(line => {
        const isBullet = /^(•|-|\d+\.) /.test(line.trim());
        const content  = line.replace(/^(•|-|\d+\.) /, '').trim();

        if (isBullet) {
            if (!inList) { out += '<ul>'; inList = true; }
            out += '<li>' + content + '</li>';
        } else {
            if (inList) { out += '</ul>'; inList = false; }
            out += line + (line ? '<br>' : '');
        }
    });

    if (inList) out += '</ul>';
    return out.replace(/<br>$/, '');
}