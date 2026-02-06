<?php
// views/includes/dashboard-footer.php
// Shared footer for all dashboard/logged-in pages

// Footer links array
$footerLinks = [
    ['label' => 'Home', 'action' => 'dashboard'],
    ['label' => 'Emergency Alert', 'action' => 'emergency-alert'],
    ['label' => 'Disaster Monitor', 'action' => 'disaster-monitor'],
    ['label' => 'Family Check-in', 'action' => 'family-checkin'],
    ['label' => 'Communication Hub', 'action' => 'communication-hub'],
];

$footerSupport = [
    ['label' => 'Help Center', 'href' => '#'],
    ['label' => 'Safety Guide', 'href' => '#'],
    ['label' => 'FSL Resources', 'href' => '#'],
    ['label' => 'Contact Us', 'action' => 'home', 'anchor' => '#contact'],
];

$footerSocial = [
    ['icon' => 'fa-brands fa-facebook-f', 'href' => '#'],
    ['icon' => 'fa-brands fa-instagram', 'href' => '#'],
    ['icon' => 'fa-brands fa-tiktok', 'href' => '#'],
    ['icon' => 'fa-brands fa-x-twitter', 'href' => '#'],
];
?>

</main>
<!-- End Main Content Wrapper -->

<!-- Dashboard Footer -->
<footer class="dashboard-footer">
    <div class="footer-container">
        <div class="footer-content">
            <!-- Brand Section -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Silent Signal Logo">
                    <span>Silent Signal.</span>
                </div>
                <p class="footer-tagline">Empowering deaf and mute users through accessible emergency communication.</p>
                <div class="footer-social">
                    <?php foreach ($footerSocial as $social): ?>
                        <a href="<?php echo $social['href']; ?>" class="social-icon">
                            <i class="<?php echo $social['icon']; ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <?php foreach ($footerLinks as $link): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>index.php?action=<?php echo $link['action']; ?>">
                                <?php echo $link['label']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Support -->
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <?php foreach ($footerSupport as $link): ?>
                        <li>
                            <a href="<?php echo isset($link['action']) ? BASE_URL . 'index.php?action=' . $link['action'] . ($link['anchor'] ?? '') : $link['href']; ?>">
                                <?php echo $link['label']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="footer-section">
                <h4>Information</h4>
                <ul class="contact-list">
                    <li><i class="fa-solid fa-phone"></i> <?php echo CONTACT_PHONE; ?></li>
                    <li><i class="fa-solid fa-envelope"></i> <?php echo CONTACT_EMAIL; ?></li>
                    <li><i class="fa-solid fa-location-dot"></i> <?php echo CONTACT_ADDRESS; ?></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Silent Signal. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- Dashboard Shared Scripts -->
<script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>