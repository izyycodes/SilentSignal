<?php
// views/communication-hub.php
// Communication Hub - Quick icon-based messaging for deaf/mute users

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<meta name="base-url" content="<?php echo BASE_URL; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/communication-hub.css">

<!-- Hidden User Data for JavaScript -->
<div id="userData" 
    data-name="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>"
    data-phone="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>"
    style="display: none;">
</div>

<!-- Hidden Emergency Contacts Data -->
<div id="emergencyContactsData" 
    data-contacts='<?php echo json_encode($emergencyContacts ?? []); ?>'
    style="display: none;">
</div>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon" style="background: linear-gradient(135deg, #d81b60 0%, #ad1457 100%);">
            <i class="ri-message-2-fill"></i>
        </div>
        <div class="page-header-content">
            <h1>Communication Hub</h1>
            <p>Quick icon-based messaging for emergencies</p>
        </div>
    </div>

    <!-- Quick Message Categories -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon pink"><i class="ri-chat-3-line"></i></div>
            <h2>Quick Messages</h2>
        </div>
        <p class="card-description">Tap an icon to quickly send a pre-written message to your emergency contacts.</p>
        
        <div class="quick-messages-grid">
            <!-- Emergency Messages -->
            <div class="message-category">
                <h3 class="category-title emergency"><i class="ri-alarm-warning-line"></i> Emergency</h3>
                <div class="message-buttons">
                    <button class="quick-msg-btn emergency" data-message="🚨 EMERGENCY! I need immediate help. Please come to my location now!" data-type="emergency">
                        <i class="ri-alarm-warning-fill"></i>
                        <span>Need Help Now</span>
                    </button>
                    <button class="quick-msg-btn emergency" data-message="🏥 I need medical assistance. Please send help or call emergency services." data-type="medical">
                        <i class="ri-hospital-fill"></i>
                        <span>Medical Help</span>
                    </button>
                    <button class="quick-msg-btn emergency" data-message="🚔 I feel unsafe. Please check on me or call the police." data-type="safety">
                        <i class="ri-shield-fill"></i>
                        <span>Feel Unsafe</span>
                    </button>
                    <button class="quick-msg-btn emergency" data-message="🔥 There's a fire emergency! Please call fire department and evacuate the area." data-type="fire">
                        <i class="ri-fire-fill"></i>
                        <span>Fire Emergency</span>
                    </button>
                </div>
            </div>

            <!-- Status Updates -->
            <div class="message-category">
                <h3 class="category-title status"><i class="ri-user-heart-line"></i> Status Updates</h3>
                <div class="message-buttons">
                    <button class="quick-msg-btn safe" data-message="✅ I'm safe and okay. No need to worry." data-type="safe">
                        <i class="ri-check-double-fill"></i>
                        <span>I'm Safe</span>
                    </button>
                    <button class="quick-msg-btn info" data-message="🏠 I've arrived home safely." data-type="home">
                        <i class="ri-home-heart-fill"></i>
                        <span>Home Safe</span>
                    </button>
                    <button class="quick-msg-btn info" data-message="📍 I'm on my way. Will update when I arrive." data-type="onway">
                        <i class="ri-run-fill"></i>
                        <span>On My Way</span>
                    </button>
                    <button class="quick-msg-btn warning" data-message="⏰ I'm running late. Will be there soon." data-type="late">
                        <i class="ri-time-fill"></i>
                        <span>Running Late</span>
                    </button>
                </div>
            </div>

            <!-- Daily Needs -->
            <div class="message-category">
                <h3 class="category-title needs"><i class="ri-hand-heart-line"></i> Daily Needs</h3>
                <div class="message-buttons">
                    <button class="quick-msg-btn needs" data-message="💊 I need my medication. Can you please bring it to me?" data-type="medicine">
                        <i class="ri-capsule-fill"></i>
                        <span>Need Medicine</span>
                    </button>
                    <button class="quick-msg-btn needs" data-message="🍽️ I'm hungry. Can you please bring me food?" data-type="food">
                        <i class="ri-restaurant-fill"></i>
                        <span>Need Food</span>
                    </button>
                    <button class="quick-msg-btn needs" data-message="💧 I need water. Please bring some when you can." data-type="water">
                        <i class="ri-drop-fill"></i>
                        <span>Need Water</span>
                    </button>
                    <button class="quick-msg-btn needs" data-message="🚗 I need a ride. Can you please pick me up?" data-type="ride">
                        <i class="ri-car-fill"></i>
                        <span>Need Ride</span>
                    </button>
                </div>
            </div>

            <!-- Requests -->
            <div class="message-category">
                <h3 class="category-title requests"><i class="ri-question-line"></i> Requests</h3>
                <div class="message-buttons">
                    <button class="quick-msg-btn request" data-message="📞 Please call me back when you can. It's important." data-type="callback">
                        <i class="ri-phone-fill"></i>
                        <span>Call Me Back</span>
                    </button>
                    <button class="quick-msg-btn request" data-message="👋 Can you come visit me? I'd like some company." data-type="visit">
                        <i class="ri-user-follow-fill"></i>
                        <span>Please Visit</span>
                    </button>
                    <button class="quick-msg-btn request" data-message="🛒 I need groceries. Can you help me shop?" data-type="groceries">
                        <i class="ri-shopping-cart-fill"></i>
                        <span>Need Groceries</span>
                    </button>
                    <button class="quick-msg-btn request" data-message="❓ I need help with something. Can we talk?" data-type="help">
                        <i class="ri-question-fill"></i>
                        <span>Need Assistance</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Message -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon blue"><i class="ri-edit-2-line"></i></div>
            <h2>Custom Message</h2>
        </div>
        
        <div class="custom-message-section">
            <textarea id="customMessage" class="custom-textarea" placeholder="Type your custom message here..." rows="4"></textarea>
            <div class="custom-message-actions">
                <div class="char-count"><span id="charCount">0</span>/500</div>
                <button class="btn btn-send-custom" id="sendCustomBtn">
                    <i class="ri-send-plane-fill"></i> Send Message
                </button>
            </div>
        </div>
    </div>

    <!-- Select Recipients -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon green"><i class="ri-contacts-line"></i></div>
            <h2>Send To</h2>
            <button class="btn btn-select-all" id="selectAllContacts">Select All</button>
        </div>
        
        <?php if (empty($emergencyContacts)): ?>
            <div class="empty-state">
                <i class="ri-user-add-line"></i>
                <p>No emergency contacts added yet.</p>
                <a href="<?php echo BASE_URL; ?>index.php?action=medical-profile" class="btn btn-primary">
                    Add Contacts
                </a>
            </div>
        <?php else: ?>
            <div class="contacts-select-list" id="contactsList">
                <?php foreach ($emergencyContacts as $index => $contact): ?>
                    <label class="contact-select-item">
                        <input type="checkbox" name="selectedContacts" value="<?php echo htmlspecialchars($contact['phone']); ?>" data-name="<?php echo htmlspecialchars($contact['name']); ?>" checked>
                        <div class="contact-avatar" style="background: <?php echo $contact['color'] ?? '#4caf50'; ?>;">
                            <?php echo $contact['initials'] ?? strtoupper(substr($contact['name'], 0, 1)); ?>
                        </div>
                        <div class="contact-details">
                            <span class="contact-name"><?php echo htmlspecialchars($contact['name']); ?></span>
                            <span class="contact-phone"><?php echo htmlspecialchars($contact['phone']); ?></span>
                        </div>
                        <div class="checkbox-indicator">
                            <i class="ri-check-line"></i>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Message Preview -->
    <div class="card" id="previewCard" style="display: none;">
        <div class="card-header">
            <div class="card-icon orange"><i class="ri-eye-line"></i></div>
            <h2>Message Preview</h2>
        </div>
        
        <div class="message-preview">
            <div class="preview-bubble">
                <p id="previewText">Select a message to preview</p>
            </div>
            <div class="preview-info">
                <span><i class="ri-user-line"></i> From: <?php echo htmlspecialchars($userData['name'] ?? 'You'); ?></span>
                <span><i class="ri-group-line"></i> To: <span id="recipientCount">0</span> contacts</span>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon purple"><i class="ri-history-line"></i></div>
            <h2>Recent Messages</h2>
        </div>
        
        <div class="recent-messages" id="recentMessages">
            <div class="empty-recent">
                <i class="ri-chat-off-line"></i>
                <p>No recent messages</p>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal" id="sendConfirmModal">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="ri-send-plane-fill"></i>
        </div>
        <h2>Send Message?</h2>
        <p id="confirmText">This will open your SMS app with the message ready to send.</p>
        <div class="confirm-details">
            <div class="confirm-item">
                <i class="ri-group-line"></i>
                <span>Recipients: <strong id="confirmRecipients">0</strong></span>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="cancelSend">Cancel</button>
            <button class="btn btn-primary" id="confirmSend">
                <i class="ri-send-plane-fill"></i> Send Now
            </button>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/communication-hub.js"></script>
</body>
</html>