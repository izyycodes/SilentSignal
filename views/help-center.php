<?php require_once VIEW_PATH . 'includes/home-header.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/support-pages.css">

<!-- Page Hero -->
<section class="support-hero">
    <div class="support-hero-bg"></div>
    <div class="container">
        <div class="support-hero-content">
            <div class="support-hero-badge"><i class="ri-customer-service-2-line"></i> Help Center</div>
            <h1>How can we help you?</h1>
            <p>Find answers, guides, and support for every feature of Silent Signal.</p>
            <div class="support-search-bar">
                <i class="ri-search-line"></i>
                <input type="text" id="helpSearch" placeholder="Search for help topics, features, or questions..." autocomplete="off">
                <button class="search-clear" id="searchClear" style="display:none;"><i class="ri-close-line"></i></button>
            </div>
        </div>
    </div>
</section>

<!-- Quick Categories -->
<section class="help-categories">
    <div class="container">
        <div class="categories-grid">
            <a href="#getting-started" class="category-card" data-filter="getting-started">
                <div class="category-icon" style="background: linear-gradient(135deg,#1976d2,#42a5f5);">
                    <i class="ri-rocket-line"></i>
                </div>
                <h3>Getting Started</h3>
                <p>Setup, registration, and first steps</p>
                <span class="category-count">6 articles</span>
            </a>
            <a href="#emergency" class="category-card" data-filter="emergency">
                <div class="category-icon" style="background: linear-gradient(135deg,#c62828,#ef5350);">
                    <i class="ri-alarm-warning-line"></i>
                </div>
                <h3>Emergency Alerts</h3>
                <p>SOS, GPS, and alert settings</p>
                <span class="category-count">5 articles</span>
            </a>
            <a href="#family" class="category-card" data-filter="family">
                <div class="category-icon" style="background: linear-gradient(135deg,#2e7d32,#66bb6a);">
                    <i class="ri-team-line"></i>
                </div>
                <h3>Family Features</h3>
                <p>Check-ins, monitoring, and contacts</p>
                <span class="category-count">4 articles</span>
            </a>
            <a href="#account" class="category-card" data-filter="account">
                <div class="category-icon" style="background: linear-gradient(135deg,#6a1b9a,#ab47bc);">
                    <i class="ri-user-settings-line"></i>
                </div>
                <h3>Account & Profile</h3>
                <p>Settings, medical profile, and security</p>
                <span class="category-count">5 articles</span>
            </a>
            <a href="#communication" class="category-card" data-filter="communication">
                <div class="category-icon" style="background: linear-gradient(135deg,#e65100,#ffa726);">
                    <i class="ri-message-3-line"></i>
                </div>
                <h3>Communication Hub</h3>
                <p>Visual tools and messages</p>
                <span class="category-count">4 articles</span>
            </a>
            <a href="#troubleshooting" class="category-card" data-filter="troubleshooting">
                <div class="category-icon" style="background: linear-gradient(135deg,#37474f,#78909c);">
                    <i class="ri-tools-line"></i>
                </div>
                <h3>Troubleshooting</h3>
                <p>Fix issues and common errors</p>
                <span class="category-count">6 articles</span>
            </a>
        </div>
    </div>
</section>

<!-- FAQ Articles -->
<section class="help-articles">
    <div class="container">
        <div class="articles-layout">

            <!-- Sidebar -->
            <aside class="articles-sidebar">
                <h4>Jump to Section</h4>
                <nav class="sidebar-nav">
                    <a href="#getting-started" class="sidebar-link active"><i class="ri-rocket-line"></i> Getting Started</a>
                    <a href="#emergency" class="sidebar-link"><i class="ri-alarm-warning-line"></i> Emergency Alerts</a>
                    <a href="#family" class="sidebar-link"><i class="ri-team-line"></i> Family Features</a>
                    <a href="#account" class="sidebar-link"><i class="ri-user-settings-line"></i> Account & Profile</a>
                    <a href="#communication" class="sidebar-link"><i class="ri-message-3-line"></i> Communication Hub</a>
                    <a href="#troubleshooting" class="sidebar-link"><i class="ri-tools-line"></i> Troubleshooting</a>
                </nav>
                <div class="sidebar-contact-box">
                    <i class="ri-customer-service-2-line"></i>
                    <h5>Still need help?</h5>
                    <p>Our support team is ready to assist you.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?action=home#contact" class="sidebar-contact-btn">Contact Us</a>
                </div>
            </aside>

            <!-- Articles Main -->
            <main class="articles-main" id="articlesMain">

                <!-- Getting Started -->
                <div class="article-group" id="getting-started" data-category="getting-started">
                    <div class="article-group-header">
                        <div class="group-icon" style="background:linear-gradient(135deg,#1976d2,#42a5f5);"><i class="ri-rocket-line"></i></div>
                        <h2>Getting Started</h2>
                    </div>
                    <?php
                    $gettingStarted = [
                        ['q' => 'How do I create a Silent Signal account?', 'a' => 'Visit the Sign Up page and fill in your name, email, and password. You will then be asked to select your role — PWD User, Family Member, or Administrator. After completing registration, you can log in and start setting up your profile.'],
                        ['q' => 'What is the difference between a PWD account and a Family account?', 'a' => 'A PWD account is for deaf and mute individuals who need emergency communication tools — including SOS alerts, medical profiles, and the communication hub. A Family account allows a linked family member or guardian to monitor check-ins, receive alerts, and view the PWD user\'s status in real time.'],
                        ['q' => 'How do I set up my profile after registering?', 'a' => 'After logging in, go to your Dashboard and click on "Medical Profile" in the navigation menu. Fill in your medical conditions, emergency contacts, blood type, and other important details. This information is shared with emergency responders when you send an SOS.'],
                        ['q' => 'Can I use Silent Signal without an internet connection?', 'a' => 'Some features, like the Communication Hub and pre-saved visual cards, are accessible offline. However, sending SOS alerts, receiving disaster notifications, and family check-ins require an active internet or mobile data connection.'],
                        ['q' => 'Is Silent Signal free to use?', 'a' => 'Yes. Silent Signal is a free platform designed specifically to serve the PWD community. All core features — emergency alerts, family monitoring, and the communication hub — are available at no cost.'],
                        ['q' => 'How do I link my account to a family member?', 'a' => 'Go to your Dashboard and navigate to "Family Check-In." From there, you can add a family member\'s phone number. They will receive an invitation to create or link a Family account that monitors your status.'],
                    ];
                    foreach ($gettingStarted as $i => $item): ?>
                    <div class="faq-item" data-category="getting-started">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo $item['q']; ?></span>
                            <i class="ri-add-line faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo $item['a']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Emergency Alerts -->
                <div class="article-group" id="emergency" data-category="emergency">
                    <div class="article-group-header">
                        <div class="group-icon" style="background:linear-gradient(135deg,#c62828,#ef5350);"><i class="ri-alarm-warning-line"></i></div>
                        <h2>Emergency Alerts</h2>
                    </div>
                    <?php
                    $emergency = [
                        ['q' => 'How do I send an SOS alert?', 'a' => 'From your Dashboard, tap the large red "Send SOS" button. The system will automatically attach your current GPS location, medical profile, and emergency contacts, then send an SMS notification to all your registered family contacts and nearby responders.'],
                        ['q' => 'What information is sent when I trigger an SOS?', 'a' => 'The SOS message includes your full name, current GPS coordinates (with a map link), blood type, known medical conditions, allergies, current medications, and the phone numbers of your emergency contacts.'],
                        ['q' => 'Can I cancel an accidental SOS?', 'a' => 'Yes. After pressing SOS, you have a 10-second countdown to cancel before the alert is sent. If the alert has already been sent, you can mark it as "False Alarm" from your Dashboard, which sends a follow-up notification to all contacts.'],
                        ['q' => 'How do disaster monitoring alerts work?', 'a' => 'Silent Signal monitors real-time disaster data from PAGASA and NDRRMC. When a disaster is detected within your area (based on your registered location), you will receive an in-app notification and, if enabled, an SMS alert with safety instructions.'],
                        ['q' => 'How do I update my emergency contact numbers?', 'a' => 'Go to Medical Profile from the navigation menu and scroll to the "Emergency Contacts" section. You can add, edit, or remove contacts at any time. Changes take effect immediately for future SOS alerts.'],
                    ];
                    foreach ($emergency as $item): ?>
                    <div class="faq-item" data-category="emergency">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo $item['q']; ?></span>
                            <i class="ri-add-line faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo $item['a']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Family Features -->
                <div class="article-group" id="family" data-category="family">
                    <div class="article-group-header">
                        <div class="group-icon" style="background:linear-gradient(135deg,#2e7d32,#66bb6a);"><i class="ri-team-line"></i></div>
                        <h2>Family Features</h2>
                    </div>
                    <?php
                    $family = [
                        ['q' => 'How does the Family Check-In system work?', 'a' => 'The PWD user can send a "I\'m Safe" check-in with one tap. Family members linked to that account receive an instant notification. If a scheduled check-in is missed, family members are automatically alerted so they can take action.'],
                        ['q' => 'Can a family member see the PWD user\'s location?', 'a' => 'Location is only shared during an active SOS alert or when the PWD user manually sends a location check-in. Silent Signal does not continuously track or expose location data to protect user privacy.'],
                        ['q' => 'How many family members can be linked to one account?', 'a' => 'Up to 5 family members or emergency contacts can be linked to a single PWD account. Each contact can be assigned a role such as Primary Guardian, Secondary Contact, or Medical Caregiver.'],
                        ['q' => 'What notifications does a family member receive?', 'a' => 'Family members receive SMS and in-app notifications for: SOS alerts, check-in completions, missed check-ins, disaster alerts affecting the PWD user\'s area, and status updates marked as "False Alarm."'],
                    ];
                    foreach ($family as $item): ?>
                    <div class="faq-item" data-category="family">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo $item['q']; ?></span>
                            <i class="ri-add-line faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo $item['a']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Account & Profile -->
                <div class="article-group" id="account" data-category="account">
                    <div class="article-group-header">
                        <div class="group-icon" style="background:linear-gradient(135deg,#6a1b9a,#ab47bc);"><i class="ri-user-settings-line"></i></div>
                        <h2>Account & Profile</h2>
                    </div>
                    <?php
                    $account = [
                        ['q' => 'How do I change my password?', 'a' => 'Log in and go to your profile dropdown (top right corner). Select "Settings," then choose "Change Password." You will need to enter your current password before setting a new one.'],
                        ['q' => 'How do I update my medical profile information?', 'a' => 'Navigate to "Medical Profile" from the dashboard sidebar. All fields — including blood type, conditions, allergies, medications, and doctor contacts — can be updated and saved at any time.'],
                        ['q' => 'Is my medical information kept private?', 'a' => 'Yes. Your medical profile is encrypted and is only shared in two situations: when you trigger an SOS alert (shared with your emergency contacts and responders) and when an authorized admin reviews verified emergency records.'],
                        ['q' => 'How do I delete my account?', 'a' => 'To request account deletion, contact us via the Contact page. Our team will process the request within 3 business days and permanently remove your data from our servers in compliance with data privacy regulations.'],
                        ['q' => 'Can I use Silent Signal on multiple devices?', 'a' => 'Yes. You can log in to your account from any device with a browser. Your data, medical profile, and contacts are synced across all sessions automatically.'],
                    ];
                    foreach ($account as $item): ?>
                    <div class="faq-item" data-category="account">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo $item['q']; ?></span>
                            <i class="ri-add-line faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo $item['a']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Communication Hub -->
                <div class="article-group" id="communication" data-category="communication">
                    <div class="article-group-header">
                        <div class="group-icon" style="background:linear-gradient(135deg,#e65100,#ffa726);"><i class="ri-message-3-line"></i></div>
                        <h2>Communication Hub</h2>
                    </div>
                    <?php
                    $comms = [
                        ['q' => 'What is the Communication Hub?', 'a' => 'The Communication Hub is a visual communication tool designed for emergencies. It contains pre-built icon cards for common needs (e.g., "I need water," "I am injured," "I need medicine") that can be shown to responders or bystanders without speaking.'],
                        ['q' => 'Can I customize the visual communication cards?', 'a' => 'Yes. From the Communication Hub page, tap "Customize Cards" to add, remove, or reorder cards. You can also create custom cards with your own text and icons for situations specific to your needs.'],
                        ['q' => 'Are the communication tools available in Filipino Sign Language?', 'a' => 'FSL reference cards and guides are available in the FSL Resources section. The Communication Hub\'s visual icon system is designed to be universally understood without requiring knowledge of a specific sign language.'],
                        ['q' => 'Does the Communication Hub work offline?', 'a' => 'Once the page has been loaded, the visual cards are available offline through your browser\'s cache. We recommend loading the Communication Hub at least once while online so it is accessible in low-connectivity emergencies.'],
                    ];
                    foreach ($comms as $item): ?>
                    <div class="faq-item" data-category="communication">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo $item['q']; ?></span>
                            <i class="ri-add-line faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo $item['a']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Troubleshooting -->
                <div class="article-group" id="troubleshooting" data-category="troubleshooting">
                    <div class="article-group-header">
                        <div class="group-icon" style="background:linear-gradient(135deg,#37474f,#78909c);"><i class="ri-tools-line"></i></div>
                        <h2>Troubleshooting</h2>
                    </div>
                    <?php
                    $trouble = [
                        ['q' => 'My SOS alert did not send. What should I do?', 'a' => 'Check your internet or mobile data connection first. If the connection is fine, ensure location permissions are enabled for your browser (required for GPS coordinates). Try refreshing the page and resending. If the issue persists, contact our support team.'],
                        ['q' => 'I am not receiving notifications from the app.', 'a' => 'Make sure browser notifications are enabled for the Silent Signal site in your device settings. Also check that your contacts have their phone numbers correctly saved in your profile. SMS delivery depends on your mobile carrier.'],
                        ['q' => 'The page is not loading correctly.', 'a' => 'Try clearing your browser cache and cookies, then reload the page. If using a mobile browser, ensure it is up to date. Silent Signal works best on Chrome, Firefox, Safari, and Edge.'],
                        ['q' => 'I forgot my password and cannot log in.', 'a' => 'On the Login page, click "Forgot Password?" and enter your registered email address. You will receive a password reset link within a few minutes. Check your spam folder if you do not see it in your inbox.'],
                        ['q' => 'My location is not being detected accurately.', 'a' => 'GPS accuracy depends on your device and environment. For best results, use a device with GPS hardware and allow location access in your browser. Indoors or in areas with weak signal, accuracy may be reduced.'],
                        ['q' => 'The family check-in link is not working for my contact.', 'a' => 'Ensure the family member has created a Silent Signal account using the same phone number you linked. Invitation links expire after 48 hours — you can resend the invitation from the Family Check-In settings page.'],
                    ];
                    foreach ($trouble as $item): ?>
                    <div class="faq-item" data-category="troubleshooting">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo $item['q']; ?></span>
                            <i class="ri-add-line faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo $item['a']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- No results message -->
                <div class="no-results" id="noResults" style="display:none;">
                    <i class="ri-search-line"></i>
                    <h3>No results found</h3>
                    <p>Try different keywords or <a href="<?php echo BASE_URL; ?>index.php?action=home#contact">contact us</a> directly.</p>
                </div>

            </main>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>

<script>
// FAQ accordion
document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
        const item = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(o => {
            o.classList.remove('open');
            o.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
            item.classList.add('open');
            btn.setAttribute('aria-expanded', 'true');
        }
    });
});

// Search
const searchInput = document.getElementById('helpSearch');
const clearBtn    = document.getElementById('searchClear');

searchInput.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    clearBtn.style.display = q ? 'flex' : 'none';

    const items  = document.querySelectorAll('.faq-item');
    const groups = document.querySelectorAll('.article-group');
    let anyVisible = false;

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        const match = !q || text.includes(q);
        item.style.display = match ? '' : 'none';
        if (match) anyVisible = true;
    });

    groups.forEach(group => {
        const visible = [...group.querySelectorAll('.faq-item')].some(i => i.style.display !== 'none');
        group.style.display = visible ? '' : 'none';
    });

    document.getElementById('noResults').style.display = anyVisible ? 'none' : '';
});

clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    clearBtn.style.display = 'none';
    document.querySelectorAll('.faq-item, .article-group').forEach(el => el.style.display = '');
    document.getElementById('noResults').style.display = 'none';
});

// Sidebar active link on scroll
const sections = document.querySelectorAll('.article-group');
const sidebarLinks = document.querySelectorAll('.sidebar-link');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(sec => {
        if (window.scrollY >= sec.offsetTop - 120) current = sec.id;
    });
    sidebarLinks.forEach(l => {
        l.classList.toggle('active', l.getAttribute('href') === '#' + current);
    });
});
</script>