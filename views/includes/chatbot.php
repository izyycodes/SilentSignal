<?php
// views/includes/chatbot.php
// AI Chatbot — floating widget injected into both home and dashboard footers
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/chatbot.css">

<!-- SILENT Signara CHATBOT WIDGET -->
<div id="ssChatBtn" class="ss-chat-fab" onclick="ssChatToggle()" title="Ask AI Assistant">
    <i class="ri-sparkling-2-fill ss-chat-fab-icon"></i>
    <i class="ri-close-line ss-chat-fab-close" style="display:none;"></i>
    <span class="ss-chat-fab-pulse"></span>
</div>

<div id="ssChatWindow" class="ss-chat-window" style="display:none;">
    <div class="ss-chat-header">
        <div class="ss-chat-header-info">
            <div class="ss-chat-avatar">
                <i class="ri-sparkling-2-fill"></i>
            </div>
            <div>
                <div class="ss-chat-title">Signara</div>
                <div class="ss-chat-subtitle">Silent Signal Assistant</div>
            </div>
        </div>
        <div class="ss-chat-header-actions">
            <button class="ss-chat-clear-btn" onclick="ssChatClear()" title="Clear chat">
                <i class="ri-delete-bin-line"></i>
            </button>
            <button class="ss-chat-close-btn" onclick="ssChatToggle()" title="Close">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>

    <div class="ss-chat-messages" id="ssChatMessages">
        <div class="ss-chat-welcome">
            <div class="ss-chat-welcome-icon"><i class="ri-sparkling-2-fill"></i></div>
            <div class="ss-chat-welcome-text">
                <strong>Hi! I'm Signara</strong>
                <p>I can help you with Silent Signal features, emergency tips, and disaster preparedness. What do you need?</p>
            </div>
        </div>
        <div class="ss-chat-suggestions">
            <button class="ss-chat-chip" onclick="ssAskChip(this)">How do I send an SOS?</button>
            <button class="ss-chat-chip" onclick="ssAskChip(this)">Set up my medical profile</button>
            <button class="ss-chat-chip" onclick="ssAskChip(this)">What to do in a flood?</button>
            <button class="ss-chat-chip" onclick="ssAskChip(this)">Link a family member</button>
        </div>
    </div>

    <div class="ss-chat-input-row">
        <textarea
            id="ssChatInput"
            class="ss-chat-input"
            placeholder="Ask anything about Silent Signal…"
            rows="1"
            onkeydown="ssChatKeydown(event)"
            oninput="ssAutoResize(this)"
        ></textarea>
        <button class="ss-chat-send-btn" id="ssChatSendBtn" onclick="ssChatSend()">
            <i class="ri-send-plane-fill"></i>
        </button>
    </div>
    <div class="ss-chat-footer-note">Powered by Llama AI · For emergencies call 911</div>
</div>

<script>
    // Make BASE_URL available to chatbot.js
    const BASE_URL = <?php echo json_encode(BASE_URL); ?>;
</script>
<script src="<?php echo BASE_URL; ?>assets/js/chatbot.js"></script>