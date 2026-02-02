
    <!-- Hero Section -->
    <section id="home" class="hero">
        <img src="assets/images/hero-background.jpg" alt="Emergency Communication Background" class="hero-bg-image">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <?php echo $heroTitle; ?>
                </h1>
                <p class="hero-description">
                    <?php echo $heroDescription; ?>
                </p>
                <a href="index.php?action=auth" class="btn-hero">Get Started</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Key Features</h2>
                <p class="section-subtitle">Designed for Accessibility</p>
            </div>
            
            <div class="features-grid">
                <?php
                foreach($features as $row) {
                    echo '<div class="feature-card">';
                    echo '<div class="feature-icon">' . $row['icon'] . '</div>';
                    echo '<h3 class="feature-title">' . $row['title'] . '</h3>';
                    echo '<p class="feature-description">' . $row['desc'] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            
            <div class="steps-grid">
                <?php
                foreach($howItWorks as $row) {
                    echo '<div class="step-card">';
                    echo '<div class="step-number">' . $row['number'] . '</div>';
                    echo '<div class="step-title">' . $row['title'] . '</div>';
                    echo '<div class="step-description">' . $row['desc'] . '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            
            <div class="contact-content">
                <div class="contact-form-wrapper">
                    <form action="index.php?action=submit-contact" method="POST" class="contact-form">
                        <div class="form-group">
                            <label for="name">Name (optional)</label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-submit">Send Message</button>
                    </form>
                </div>
                
                <div class="contact-info-wrapper">
                    <div class="contact-info-header">
                        <h3>Need help or have questions?</h3>
                        <p>We are here to support you — no voice required.</p>
                    </div>
                    
                    <div class="contact-info-grid">
                        <div class="contact-info-item">
                            <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="contact-details">
                                <h4>Call Us</h4>
                                <p><?php echo CONTACT_PHONE; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="contact-details">
                                <h4>Email Us</h4>
                                <p><?php echo CONTACT_EMAIL; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon"><i class="ri-global-line"></i></div>
                            <div class="contact-details">
                                <h4>Website</h4>
                                <p><?php echo CONTACT_WEBSITE; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon address-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="contact-details">
                                <h4>Address</h4>
                                <p><?php echo CONTACT_ADDRESS; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-section">
                        <h4>Follow Us On:</h4>
                        <div class="social-icons">
                            <a href="#" class="social-icon-circle"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="social-icon-circle"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="social-icon-circle"><i class="fa-brands fa-tiktok"></i></a>
                            <a href="#" class="social-icon-circle"><i class="fa-brands fa-x-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>