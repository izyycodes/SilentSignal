<?php <?php $pageStyles = [BASE_URL . 'assets/css/support-pages.css']; ?>
require_once VIEW_PATH . 'includes/home-header.php'; ?>


<!-- Page Hero -->
<section class="support-hero safety-hero">
    <div class="support-hero-bg safety-hero-bg"></div>
    <div class="container">
        <div class="support-hero-content">
            <div class="support-hero-badge"><i class="ri-shield-check-line"></i> Safety Guide</div>
            <h1>Stay Safe. Stay Prepared.</h1>
            <p>Essential safety protocols and emergency procedures designed for deaf and mute individuals in the Philippines.</p>
            <div class="safety-hero-actions">
                <a href="#before" class="hero-action-btn primary"><i class="ri-shield-check-line"></i> Before a Disaster</a>
                <a href="#during" class="hero-action-btn secondary"><i class="ri-flashlight-line"></i> During an Emergency</a>
            </div>
        </div>
    </div>
</section>

<!-- Alert Banner -->
<div class="safety-alert-banner">
    <div class="container">
        <i class="ri-information-line"></i>
        <span>In an immediate life-threatening emergency, always trigger your Silent Signal SOS first, then follow the steps in this guide.</span>
        <a href="<?php echo BASE_URL; ?>index.php?action=auth" class="alert-banner-btn">Set Up SOS Now</a>
    </div>
</div>

<!-- Safety Phases -->
<section class="safety-phases" id="before">
    <div class="container">

        <!-- Phase 1: Before -->
        <div class="phase-block" id="before">
            <div class="phase-label before"><i class="ri-calendar-check-line"></i> Phase 1</div>
            <h2 class="phase-title">Before a Disaster</h2>
            <p class="phase-subtitle">Preparation is your most powerful tool. Take these steps now so you are ready when it matters.</p>

            <div class="safety-cards-grid">
                <div class="safety-card">
                    <div class="safety-card-num">01</div>
                    <div class="safety-card-icon blue"><i class="ri-user-heart-line"></i></div>
                    <h3>Complete Your Medical Profile</h3>
                    <p>Fill in your blood type, medical conditions, allergies, and medications in Silent Signal. This is sent automatically with every SOS alert so responders know exactly how to help you.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?action=auth" class="safety-card-link">Set up profile <i class="ri-arrow-right-line"></i></a>
                </div>
                <div class="safety-card">
                    <div class="safety-card-num">02</div>
                    <div class="safety-card-icon green"><i class="ri-team-line"></i></div>
                    <h3>Register Emergency Contacts</h3>
                    <p>Add at least 2 trusted contacts — a family member and a neighbor or colleague. Make sure their mobile numbers are correct and that they know you have registered them in the app.</p>
                </div>
                <div class="safety-card">
                    <div class="safety-card-num">03</div>
                    <div class="safety-card-icon orange"><i class="ri-first-aid-kit-line"></i></div>
                    <h3>Prepare an Emergency Kit</h3>
                    <p>Keep a go-bag ready with: 3-day supply of water and food, medications, a written copy of your medical information, a fully charged power bank, a whistle, and a printed SOS card with your details.</p>
                </div>
                <div class="safety-card">
                    <div class="safety-card-num">04</div>
                    <div class="safety-card-icon purple"><i class="ri-map-pin-line"></i></div>
                    <h3>Know Your Evacuation Routes</h3>
                    <p>Identify the nearest evacuation centers in your barangay. Walk the routes ahead of time. Mark them on a physical map in case your phone has no signal during the disaster.</p>
                </div>
                <div class="safety-card">
                    <div class="safety-card-num">05</div>
                    <div class="safety-card-icon teal"><i class="ri-notification-3-line"></i></div>
                    <h3>Enable Disaster Alerts</h3>
                    <p>Turn on disaster monitoring in your Silent Signal settings. Allow notification permissions in your browser. This ensures you receive typhoon, flood, and earthquake alerts for your area automatically.</p>
                </div>
                <div class="safety-card">
                    <div class="safety-card-num">06</div>
                    <div class="safety-card-icon red"><i class="ri-message-2-line"></i></div>
                    <h3>Practice the Communication Hub</h3>
                    <p>Familiarize yourself with the visual cards in the Communication Hub before an emergency. Load the page once while online so it is cached and accessible without internet during a crisis.</p>
                </div>
            </div>
        </div>

        <!-- Phase 2: During -->
        <div class="phase-block" id="during">
            <div class="phase-label during"><i class="ri-flashlight-line"></i> Phase 2</div>
            <h2 class="phase-title">During an Emergency</h2>
            <p class="phase-subtitle">Stay calm. Follow these steps in order. Your Silent Signal app is your primary communication tool.</p>

            <div class="steps-timeline">
                <div class="timeline-item">
                    <div class="timeline-dot red"></div>
                    <div class="timeline-content">
                        <span class="timeline-step">Step 1</span>
                        <h3>Trigger Your SOS</h3>
                        <p>Open Silent Signal and press the red SOS button on your Dashboard. This immediately sends your location and medical information to all your registered contacts via SMS.</p>
                        <div class="timeline-tip"><i class="ri-lightbulb-line"></i> You have 10 seconds to cancel if pressed accidentally.</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot orange"></div>
                    <div class="timeline-content">
                        <span class="timeline-step">Step 2</span>
                        <h3>Move to Safety</h3>
                        <p>Follow your pre-planned evacuation route. For floods — move to higher ground. For earthquakes — drop, cover, and hold on, then evacuate once shaking stops. For fires — stay low and exit immediately.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot blue"></div>
                    <div class="timeline-content">
                        <span class="timeline-step">Step 3</span>
                        <h3>Communicate with Responders</h3>
                        <p>Open the Communication Hub on your device and show responders the visual cards. Point to "I need help," "I cannot hear," "I am injured," or other relevant cards to communicate your needs without speaking.</p>
                        <div class="timeline-tip"><i class="ri-lightbulb-line"></i> You can also show your medical profile screen directly to responders.</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot green"></div>
                    <div class="timeline-content">
                        <span class="timeline-step">Step 4</span>
                        <h3>Send a Check-In</h3>
                        <p>Once you reach safety, send a family check-in from your Dashboard. This notifies all your family contacts that you are safe and ends the active SOS alert status.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phase 3: After -->
        <div class="phase-block" id="after">
            <div class="phase-label after"><i class="ri-heart-pulse-line"></i> Phase 3</div>
            <h2 class="phase-title">After the Emergency</h2>
            <p class="phase-subtitle">Recovery takes time. These steps help you stay safe and update your support network.</p>

            <div class="after-grid">
                <div class="after-card">
                    <i class="ri-checkbox-circle-line"></i>
                    <div>
                        <h4>Mark Yourself as Safe</h4>
                        <p>Update your status in Silent Signal so family contacts stop worrying and your alert history is correctly recorded.</p>
                    </div>
                </div>
                <div class="after-card">
                    <i class="ri-hospital-line"></i>
                    <div>
                        <h4>Seek Medical Attention</h4>
                        <p>Even if you feel fine, visit an evacuation center medic. Show your medical profile on Silent Signal for faster assessment.</p>
                    </div>
                </div>
                <div class="after-card">
                    <i class="ri-home-smile-line"></i>
                    <div>
                        <h4>Wait for All-Clear</h4>
                        <p>Do not return home until local authorities issue an all-clear. Monitor disaster alerts in your Silent Signal app for updates.</p>
                    </div>
                </div>
                <div class="after-card">
                    <i class="ri-refresh-line"></i>
                    <div>
                        <h4>Restock Your Emergency Kit</h4>
                        <p>Replace used supplies, recharge devices, and review your evacuation plan. Update your Silent Signal contacts if anything has changed.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- PWD-Specific Tips -->
        <div class="phase-block">
            <div class="phase-label tips"><i class="ri-star-line"></i> Special Considerations</div>
            <h2 class="phase-title">Tips for Deaf & Mute Individuals</h2>
            <p class="phase-subtitle">These specific strategies can make a critical difference in an emergency situation.</p>

            <div class="tips-grid">
                <div class="tip-item">
                    <div class="tip-icon"><i class="ri-id-card-line"></i></div>
                    <h4>Carry a Physical ID Card</h4>
                    <p>Keep a printed card in your wallet that reads: "I am deaf/mute. In an emergency, please contact: [name & number]. My medical info is on my Silent Signal app."</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><i class="ri-flashlight-line"></i></div>
                    <h4>Use a Flashlight to Signal</h4>
                    <p>If you cannot use your phone, flash a light in SOS pattern (3 short, 3 long, 3 short) to attract attention in low-light or nighttime emergencies.</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><i class="ri-pulse-line"></i></div>
                    <h4>Enable Strong Vibration Alerts</h4>
                    <p>Set your device to maximum vibration for notifications so you feel Silent Signal alerts even when you cannot hear an alarm or announcement.</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><i class="ri-group-line"></i></div>
                    <h4>Inform Your Neighbors</h4>
                    <p>Let at least one trusted neighbor know you are deaf or mute so they can physically check on you and assist you in evacuating if needed.</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><i class="ri-eye-line"></i></div>
                    <h4>Rely on Visual Cues</h4>
                    <p>Position yourself near windows or open areas where you can see emergency vehicle lights, evacuation signs, and other visual indicators during a crisis.</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><i class="ri-battery-charge-line"></i></div>
                    <h4>Keep Devices Charged</h4>
                    <p>Your phone is your most critical communication tool. Keep it above 50% charge and carry a power bank with at least one full charge at all times.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- CTA -->
<section class="safety-cta">
    <div class="container">
        <div class="safety-cta-content">
            <i class="ri-shield-check-line"></i>
            <h2>Are you prepared?</h2>
            <p>Set up your Silent Signal profile now so your medical information and contacts are ready before the next emergency.</p>
            <a href="<?php echo BASE_URL; ?>index.php?action=auth&mode=signup" class="cta-primary-btn">Create Your Profile</a>
            <a href="<?php echo BASE_URL; ?>index.php?action=fsl-resources" class="cta-secondary-btn">View FSL Resources</a>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>

<script>
// Smooth scroll offset for fixed header
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
        }
    });
});
</script>