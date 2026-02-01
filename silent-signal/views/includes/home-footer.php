<footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Silent Signal Logo" class="logo-icon">
                        <span class="logo-text">Silent Signal.</span>
                    </div>
                    <p class="footer-description">
                        Empowering deaf and mute users through <br>
                        accessible emergency communication.
                    </p>
                    <div class="social-icons">
						<a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a>
						<a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
						<a href="#" class="social-icon"><i class="fa-brands fa-tiktok"></i></a>
						<a href="#" class="social-icon"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#home' : BASE_URL . 'index.php?action=home#home'; ?>">Home</a></li>
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#features' : BASE_URL . 'index.php?action=home#features'; ?>">Features</a></li>
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#how-it-works' : BASE_URL . 'index.php?action=home#how-it-works'; ?>">How It Works</a></li>
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#contact' : BASE_URL . 'index.php?action=home#contact'; ?>">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-title">Information</h4>
                    <ul class="footer-links">
                        <li class="footer-info"><i class="fa-solid fa-phone"></i><a href="#"><?php echo CONTACT_PHONE; ?></a></li>
                        <li class="footer-info"><i class="fa-solid fa-envelope"></i><a href="#"><?php echo CONTACT_EMAIL; ?></a></li>
                        <li class="footer-info"><i class="fa-solid fa-location-dot"></i><a href="#"><?php echo CONTACT_ADDRESS; ?></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; Silent Signal. Copyright 2026. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo BASE_URL; ?>assets/js/home.js"></script>
</body>
</html>