<?php <?php $pageStyles = [BASE_URL . 'assets/css/support-pages.css']; ?>
require_once VIEW_PATH . 'includes/home-header.php'; ?>


<!-- Page Hero -->
<section class="support-hero fsl-hero">
    <div class="support-hero-bg fsl-hero-bg"></div>
    <div class="container">
        <div class="support-hero-content">
            <div class="support-hero-badge"><i class="ri-hand-heart-line"></i> FSL Resources</div>
            <h1>Filipino Sign Language Resources</h1>
            <p>Downloadable guides, reference cards, and learning materials to help you communicate effectively during any emergency.</p>
        </div>
    </div>
</section>

<!-- What is FSL -->
<section class="fsl-intro">
    <div class="container">
        <div class="fsl-intro-grid">
            <div class="fsl-intro-text">
                <span class="section-eyebrow">About FSL</span>
                <h2>What is Filipino Sign Language?</h2>
                <p>Filipino Sign Language (FSL) is the official sign language of the Philippines, recognized by Republic Act No. 11106, the Filipino Sign Language Act. It is the natural language of the Filipino Deaf community.</p>
                <p>Learning even a few basic FSL signs can help you communicate with Deaf community members, barangay responders, and emergency personnel during a crisis — making you safer and more able to assist others.</p>
                <div class="fsl-intro-badges">
                    <span><i class="ri-check-line"></i> Recognized by RA 11106</span>
                    <span><i class="ri-check-line"></i> Official language for PWD services</span>
                    <span><i class="ri-check-line"></i> Used by emergency responders</span>
                </div>
            </div>
            <div class="fsl-intro-visual">
                <div class="fsl-sign-showcase">
                    <div class="sign-card-demo">
                        <div class="sign-hand"><i class="ri-hand-heart-line"></i></div>
                        <span>Help</span>
                    </div>
                    <div class="sign-card-demo accent">
                        <div class="sign-hand"><i class="ri-alarm-warning-line"></i></div>
                        <span>Emergency</span>
                    </div>
                    <div class="sign-card-demo">
                        <div class="sign-hand"><i class="ri-hospital-line"></i></div>
                        <span>Medical</span>
                    </div>
                    <div class="sign-card-demo">
                        <div class="sign-hand"><i class="ri-map-pin-line"></i></div>
                        <span>Location</span>
                    </div>
                    <div class="sign-card-demo accent">
                        <div class="sign-hand"><i class="ri-shield-check-line"></i></div>
                        <span>Safe</span>
                    </div>
                    <div class="sign-card-demo">
                        <div class="sign-hand"><i class="ri-phone-line"></i></div>
                        <span>Call</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Downloadable PDFs -->
<section class="fsl-downloads">
    <div class="container">
        <div class="section-header-inline">
            <div>
                <span class="section-eyebrow">Downloads</span>
                <h2>Emergency FSL Reference Guides</h2>
                <p>Free downloadable PDF cards you can print, save, or share. Designed for quick reference during emergencies.</p>
            </div>
        </div>

        <div class="downloads-grid">
            <div class="download-card">
                <div class="download-card-top" style="background: linear-gradient(135deg, #c62828, #ef5350);">
                    <i class="ri-alarm-warning-line"></i>
                    <div class="download-pdf-badge">PDF</div>
                </div>
                <div class="download-card-body">
                    <h3>Emergency Preparedness Guide</h3>
                    <p>Essential FSL signs for before, during, and after an emergency. Includes signs for evacuation, shelter, danger, and help.</p>
                    <div class="download-meta">
                        <span><i class="ri-file-pdf-line"></i> PDF</span>
                        <span><i class="ri-pages-line"></i> ~4 pages</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-emergency-preparedness.pdf" download class="download-btn">
                        <i class="ri-download-2-line"></i> Download Free
                    </a>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-emergency-preparedness.pdf" target="_blank" class="preview-btn">
                        <i class="ri-eye-line"></i> Preview
                    </a>
                </div>
            </div>

            <div class="download-card">
                <div class="download-card-top" style="background: linear-gradient(135deg, #1976d2, #42a5f5);">
                    <i class="ri-message-3-line"></i>
                    <div class="download-pdf-badge">PDF</div>
                </div>
                <div class="download-card-body">
                    <h3>Disaster Communication Cards</h3>
                    <p>Visual communication cards for common disaster situations. Printable cards to show responders when verbal communication is not possible.</p>
                    <div class="download-meta">
                        <span><i class="ri-file-pdf-line"></i> PDF</span>
                        <span><i class="ri-pages-line"></i> ~4 pages</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-disaster-communication.pdf" download class="download-btn">
                        <i class="ri-download-2-line"></i> Download Free
                    </a>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-disaster-communication.pdf" target="_blank" class="preview-btn">
                        <i class="ri-eye-line"></i> Preview
                    </a>
                </div>
            </div>

            <div class="download-card">
                <div class="download-card-top" style="background: linear-gradient(135deg, #2e7d32, #66bb6a);">
                    <i class="ri-route-line"></i>
                    <div class="download-pdf-badge">PDF</div>
                </div>
                <div class="download-card-body">
                    <h3>Evacuation Instructions</h3>
                    <p>Step-by-step evacuation guidance in FSL and visual format. Includes signs for exit, gather, move, and wait for instructions.</p>
                    <div class="download-meta">
                        <span><i class="ri-file-pdf-line"></i> PDF</span>
                        <span><i class="ri-pages-line"></i> ~4 pages</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-evacuation-instructions.pdf" download class="download-btn">
                        <i class="ri-download-2-line"></i> Download Free
                    </a>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-evacuation-instructions.pdf" target="_blank" class="preview-btn">
                        <i class="ri-eye-line"></i> Preview
                    </a>
                </div>
            </div>

            <div class="download-card">
                <div class="download-card-top" style="background: linear-gradient(135deg, #e65100, #ffa726);">
                    <i class="ri-first-aid-kit-line"></i>
                    <div class="download-pdf-badge">PDF</div>
                </div>
                <div class="download-card-body">
                    <h3>First Aid Communication</h3>
                    <p>FSL signs specifically for first aid and medical emergencies. Helps you communicate symptoms, pain location, and medical needs to responders.</p>
                    <div class="download-meta">
                        <span><i class="ri-file-pdf-line"></i> PDF</span>
                        <span><i class="ri-pages-line"></i> ~4 pages</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-first-aid.pdf" download class="download-btn">
                        <i class="ri-download-2-line"></i> Download Free
                    </a>
                    <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-first-aid.pdf" target="_blank" class="preview-btn">
                        <i class="ri-eye-line"></i> Preview
                    </a>
                </div>
            </div>
        </div>

        <div class="download-all-bar">
            <div class="download-all-info">
                <i class="ri-folder-zip-line"></i>
                <div>
                    <strong>Download All 4 Guides</strong>
                    <span>Print them all and keep copies in your emergency kit and home.</span>
                </div>
            </div>
            <div class="download-all-links">
                <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-emergency-preparedness.pdf" download class="download-all-btn">
                    Emergency Prep <i class="ri-download-line"></i>
                </a>
                <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-disaster-communication.pdf" download class="download-all-btn">
                    Disaster Comms <i class="ri-download-line"></i>
                </a>
                <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-evacuation-instructions.pdf" download class="download-all-btn">
                    Evacuation <i class="ri-download-line"></i>
                </a>
                <a href="<?php echo BASE_URL; ?>assets/fsl/fsl-first-aid.pdf" download class="download-all-btn">
                    First Aid <i class="ri-download-line"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Quick Reference Signs -->
<section class="fsl-quick-ref">
    <div class="container">
        <div class="section-header-inline">
            <div>
                <span class="section-eyebrow">Quick Reference</span>
                <h2>Essential Emergency Signs</h2>
                <p>The most important FSL signs to know. Show this screen to responders or bystanders if needed.</p>
            </div>
        </div>

        <div class="signs-grid">
            <?php
            $signs = [
                ['icon' => 'ri-alarm-warning-line',   'color' => '#c62828', 'word' => 'HELP',       'desc' => 'Extend both hands forward, palms up, and lift upward.'],
                ['icon' => 'ri-heart-pulse-line',      'color' => '#e65100', 'word' => 'EMERGENCY',  'desc' => 'Form an "E" handshape and shake rapidly side to side.'],
                ['icon' => 'ri-map-pin-line',          'color' => '#1976d2', 'word' => 'WHERE',      'desc' => 'Index finger points outward and moves side to side.'],
                ['icon' => 'ri-hospital-line',         'color' => '#2e7d32', 'word' => 'HOSPITAL',   'desc' => 'Form an "H" on the upper arm and draw a cross shape.'],
                ['icon' => 'ri-shield-check-line',     'color' => '#37474f', 'word' => 'SAFE',       'desc' => 'Cross arms over chest, then open outward in a sweeping motion.'],
                ['icon' => 'ri-fire-line',             'color' => '#b71c1c', 'word' => 'FIRE',       'desc' => 'Wiggle all fingers upward in a flickering flame motion.'],
                ['icon' => 'ri-water-flash-line',      'color' => '#0277bd', 'word' => 'WATER',      'desc' => 'Form a "W" handshape and tap the chin twice.'],
                ['icon' => 'ri-run-line',              'color' => '#5d4037', 'word' => 'EVACUATE',   'desc' => 'Point forward firmly, then sweep hand in direction of exit.'],
                ['icon' => 'ri-phone-line',            'color' => '#6a1b9a', 'word' => 'CALL 911',   'desc' => 'Mimic holding a phone, then point to emergency number fingers.'],
                ['icon' => 'ri-first-aid-kit-line',    'color' => '#ef5350', 'word' => 'MEDICINE',   'desc' => 'Rub middle finger on palm of opposite hand in circles.'],
                ['icon' => 'ri-eye-off-line',          'color' => '#546e7a', 'word' => 'I AM DEAF',  'desc' => 'Point to ear then cross index fingers in front of chest.'],
                ['icon' => 'ri-checkbox-circle-line',  'color' => '#2e7d32', 'word' => 'I AM OK',    'desc' => 'Form an "OK" shape with thumb and index finger, raise up.'],
            ];
            foreach ($signs as $sign): ?>
            <div class="sign-ref-card">
                <div class="sign-ref-icon" style="background: <?php echo $sign['color']; ?>1a; color: <?php echo $sign['color']; ?>;">
                    <i class="<?php echo $sign['icon']; ?>"></i>
                </div>
                <div class="sign-ref-word"><?php echo $sign['word']; ?></div>
                <div class="sign-ref-desc"><?php echo $sign['desc']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="signs-disclaimer">
            <i class="ri-information-line"></i>
            <p>These are simplified descriptions to help you learn the concept. For accurate FSL handshapes and movements, refer to the official downloadable PDF guides or consult a certified FSL interpreter.</p>
        </div>
    </div>
</section>

<!-- External Resources -->
<section class="fsl-external">
    <div class="container">
        <div class="section-header-inline">
            <div>
                <span class="section-eyebrow">Learn More</span>
                <h2>Official FSL Organizations</h2>
                <p>These organizations offer certified FSL training and additional learning resources.</p>
            </div>
        </div>
        <div class="external-grid">
            <div class="external-card">
                <div class="external-icon"><i class="ri-government-line"></i></div>
                <div>
                    <h4>NCDA — National Council on Disability Affairs</h4>
                    <p>The official government body overseeing programs and services for persons with disability in the Philippines.</p>
                    <a href="https://www.ncda.gov.ph" target="_blank" rel="noopener" class="external-link">Visit ncda.gov.ph <i class="ri-external-link-line"></i></a>
                </div>
            </div>
            <div class="external-card">
                <div class="external-icon"><i class="ri-school-line"></i></div>
                <div>
                    <h4>Philippine School for the Deaf (PSD)</h4>
                    <p>The premier government school for deaf learners, offering FSL instruction and resources for families and educators.</p>
                    <a href="#" class="external-link">Learn about PSD <i class="ri-external-link-line"></i></a>
                </div>
            </div>
            <div class="external-card">
                <div class="external-icon"><i class="ri-book-open-line"></i></div>
                <div>
                    <h4>DepEd FSL Curriculum</h4>
                    <p>The Department of Education's FSL curriculum materials are available for schools serving deaf learners nationwide.</p>
                    <a href="https://www.deped.gov.ph" target="_blank" rel="noopener" class="external-link">Visit deped.gov.ph <i class="ri-external-link-line"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="safety-cta">
    <div class="container">
        <div class="safety-cta-content">
            <i class="ri-hand-heart-line"></i>
            <h2>Ready to use these resources?</h2>
            <p>Download the guides, create your Silent Signal account, and be prepared for any emergency.</p>
            <a href="<?php echo BASE_URL; ?>index.php?action=auth&mode=signup" class="cta-primary-btn">Get Started Free</a>
            <a href="<?php echo BASE_URL; ?>index.php?action=safety-guide" class="cta-secondary-btn">Read Safety Guide</a>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>