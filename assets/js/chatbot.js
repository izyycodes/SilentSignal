// ============================================================
// SILENT SIGNAL — CHATBOT WIDGET JS (HYBRID VERSION)
// ============================================================

// OFFLINE KNOWLEDGE BASE
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

// OFFLINE MATCHING
function ssGetOfflineResponse(message) {
    const msg = message.toLowerCase();

    for (let item of SS_KNOWLEDGE) {
        for (let keyword of item.keywords) {
            if (msg.includes(keyword)) {
                return item.response;
            }
        }
    }
    return null;
}

// COOLDOWN (ANTI-SPAM)
let lastRequestTime = 0;


// ============================================================
// ORIGINAL CODE (MODIFIED BELOW)
// ============================================================

const SS_SYSTEM_PROMPT = `You are Signara, the friendly assistant for Silent Signal...`;

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


// MAIN FUNCTION (UPDATED)
function ssSubmitMessage(text) {
    ssAppendMessage('user', text);
    ssChatHistory.push({ role: 'user', content: text });

    // OFFLINE FIRST
    const offlineReply = ssGetOfflineResponse(text);

    if (offlineReply) {
        ssAppendMessage('assistant', offlineReply);
        return;
    }

    // API FALLBACK
    ssShowTyping();
    ssChatLoading = true;
    document.getElementById('ssChatSendBtn').disabled = true;

    const baseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '/';

    fetch(baseUrl + 'index.php?action=chatbot-proxy', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            system: SS_SYSTEM_PROMPT,
            messages: ssChatHistory.slice(-6) // limit history
        })
    })
    .then(r => r.json())
    .then(data => {
        ssRemoveTyping();
        const reply = data?.content?.[0]?.text || 
            'I’m not sure about that yet. Try asking about SOS or disaster tips.';
        ssChatHistory.push({ role: 'assistant', content: reply });
        ssAppendMessage('assistant', reply);
    })
    .catch(() => {
        ssRemoveTyping();
        ssAppendMessage('assistant', 
            'Offline mode active. Try asking about SOS, flood, earthquake, or profile setup.', 
            true
        );
    })
    .finally(() => {
        ssChatLoading = false;
        document.getElementById('ssChatSendBtn').disabled = false;
        document.getElementById('ssChatInput').focus();
    });
}


// ============================================================
// REMAINING FUNCTIONS (UNCHANGED)
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

    if (inList) out += '</ul';
    return out.replace(/<br>$/, '');
}