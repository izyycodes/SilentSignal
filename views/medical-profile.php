<?php
// views/medical-profile.php
// Medical Profile & Pre-Registration - Data is passed from UserController

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/medical-profile.css">

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header medical-header">
        <div class="page-header-icon" style="background: linear-gradient(135deg, #26a69a 0%, #00897b 100%);">
            <i class="ri-heart-pulse-fill"></i>
        </div>
        <div class="page-header-content">
            <h1>Medical Profile & Pre-Registration</h1>
            <p>Manage your medical information for emergencies</p>
        </div>
        <button class="btn btn-save" id="saveChangesBtn">
            <i class="ri-edit-line"></i> Edit Profile
        </button>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <?php foreach ($tabs as $index => $tab): ?>
            <button class="tab-btn <?php echo $index === 0 ? 'active' : ''; ?>" data-tab="<?php echo $tab['id']; ?>">
                <i class="<?php echo $tab['icon']; ?>"></i>
                <span><?php echo $tab['label']; ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        
        <!-- Tab 1: Medical Profile -->
        <div class="tab-pane active" id="medical-profile">
            
            <!-- Personal Information -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon blue"><i class="ri-user-line"></i></div>
                    <h2>Personal Information</h2>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstName" class="form-control" value="<?php echo $personalInfo['firstName']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastName" class="form-control" value="<?php echo $personalInfo['lastName']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dateOfBirth" class="form-control" value="<?php echo $personalInfo['dateOfBirth']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="Male" <?php echo $personalInfo['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $personalInfo['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $personalInfo['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>PWD ID Number</label>
                        <input type="text" name="pwdId" class="form-control" value="<?php echo $personalInfo['pwdId']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo $personalInfo['phone']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $personalInfo['email']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Street Address</label>
                        <input type="text" name="streetAddress" class="form-control" value="<?php echo $personalInfo['streetAddress']; ?>">
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo $personalInfo['city']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Province</label>
                        <input type="text" name="province" class="form-control" value="<?php echo $personalInfo['province']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Zip Code</label>
                        <input type="text" name="zipCode" class="form-control" value="<?php echo $personalInfo['zipCode']; ?>">
                    </div>
                </div>
            </div>

            <!-- Disability Status -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon purple"><i class="ri-wheelchair-line"></i></div>
                    <h2>Disability Status</h2>
                </div>
                
                <div class="disability-status">
                    <div class="disability-info" style="flex:1;">
                        <span class="disability-label">Primary Disability</span>
                        <!-- Display mode -->
                        <span class="disability-value" id="disabilityDisplay">
                            <?php echo htmlspecialchars($disabilityStatus['primary']); ?>
                        </span>
                        <!-- Edit mode (hidden by default) -->
                        <div id="disabilityEditMode" style="display:none; margin-top:6px;">
                            <select id="disabilitySelect" class="form-control" style="margin-bottom:6px;">
                                <option value="" disabled selected>-- Select Disability --</option>
                                <option value="Deaf/Mute" <?php echo $disabilityStatus['primary'] === 'Deaf/Mute' ? 'selected' : ''; ?>>Deaf/Mute</option>
                                <option value="Blind" <?php echo $disabilityStatus['primary'] === 'Blind' ? 'selected' : ''; ?>>Blind</option>
                                <option value="Mobility Impaired" <?php echo $disabilityStatus['primary'] === 'Mobility Impaired' ? 'selected' : ''; ?>>Mobility Impaired</option>
                                <option value="Intellectually Disabled" <?php echo $disabilityStatus['primary'] === 'Intellectually Disabled' ? 'selected' : ''; ?>>Intellectually Disabled</option>
                                <option value="Psychosocial Disability" <?php echo $disabilityStatus['primary'] === 'Psychosocial Disability' ? 'selected' : ''; ?>>Psychosocial Disability</option>
                                <option value="Chronic Illness" <?php echo $disabilityStatus['primary'] === 'Chronic Illness' ? 'selected' : ''; ?>>Chronic Illness</option>
                                <option value="Other" <?php echo (!empty($disabilityStatus['primary']) && !in_array($disabilityStatus['primary'], ['Deaf/Mute','Blind','Mobility Impaired','Intellectually Disabled','Psychosocial Disability','Chronic Illness','Not specified'])) ? 'selected' : ''; ?>>Other (specify below)</option>
                            </select>
                            <input type="text" id="disabilityCustomInput" name="disabilityType" class="form-control" 
                                   placeholder="Enter your disability type..."
                                   value="<?php echo htmlspecialchars($disabilityStatus['primary'] !== 'Not specified' ? $disabilityStatus['primary'] : ''); ?>"
                                   style="display:none;">
                        </div>
                        <!-- Hidden input to carry value when not in edit mode -->
                        <input type="hidden" id="disabilityTypeHidden" name="disabilityType" 
                               value="<?php echo htmlspecialchars($disabilityStatus['primary']); ?>">
                    </div>
                    <?php
                        // Badge logic: show "Pending" if not verified, "Verified" if is_verified is true in users table
                        // We'll use the users is_verified flag (passed via disabilityStatus)
                        $isVerified = $disabilityStatus['is_verified'] ?? false;
                    ?>
                    <span class="<?php echo $isVerified ? 'verified-badge' : 'pending-badge'; ?>">
                        <?php if ($isVerified): ?>
                            <i class="ri-checkbox-circle-fill"></i> VERIFIED
                        <?php else: ?>
                            <i class="ri-time-line"></i> PENDING
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <!-- Allergies -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon red"><i class="ri-alert-line"></i></div>
                    <h2>Allergies</h2>
                </div>
                
                <div class="tags-container" id="allergiesContainer">
                    <?php foreach ($allergies as $allergy): ?>
                        <div class="tag tag-red">
                            <span><?php echo $allergy; ?></span>
                            <button class="tag-remove"><i class="ri-close-line"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn-add" id="addAllergyBtn">
                    <i class="ri-add-line"></i> Add Allergy
                </button>
            </div>

            <!-- Current Medications -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon green"><i class="ri-capsule-line"></i></div>
                    <h2>Current Medications</h2>
                </div>
                
                <div class="medications-list" id="medicationsContainer">
                    <?php foreach ($medications as $med): ?>
                        <div class="medication-item">
                            <i class="ri-medicine-bottle-line"></i>
                            <span><?php echo $med; ?></span>
                            <button class="item-remove"><i class="ri-close-line"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn-add" id="addMedicationBtn">
                    <i class="ri-add-line"></i> Add Medication
                </button>
            </div>

            <!-- Medical Conditions & Blood Type -->
            <div class="card-row">
                <!-- Medical Conditions -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon yellow"><i class="ri-stethoscope-line"></i></div>
                        <h2>Medical Conditions</h2>
                    </div>
                    
                    <div class="tags-container" id="conditionsContainer">
                        <?php foreach ($medicalConditions as $condition): ?>
                            <div class="tag tag-yellow">
                                <span><?php echo $condition; ?></span>
                                <button class="tag-remove"><i class="ri-close-line"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" id="addConditionBtn">
                        <i class="ri-add-line"></i>
                    </button>
                </div>

                <!-- Blood Type -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon red"><i class="ri-heart-3-line"></i></div>
                        <h2>Blood Type</h2>
                    </div>
                    
                    <div class="blood-type-display">
                        <div class="blood-type-badge">
                            <span class="blood-type-value"><?php echo $bloodType; ?></span>
                            <span class="blood-type-label">Blood Type</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Emergency Contacts -->
        <div class="tab-pane" id="emergency-contacts">
            
            <!-- Emergency Contacts List -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon green"><i class="ri-contacts-line"></i></div>
                    <h2>Emergency Contacts</h2>
                    <button class="btn btn-add-contact" id="addContactBtn">
                        <i class="ri-add-line"></i> Add Contact
                    </button>
                </div>
                
                <div class="contacts-list">
                    <?php foreach ($emergencyContacts as $contact): ?>
                        <div class="contact-card">
                            <div class="contact-avatar" style="background: <?php echo $contact['color']; ?>;">
                                <?php echo $contact['initials']; ?>
                            </div>
                            <div class="contact-info">
                                <h4><?php echo $contact['name']; ?></h4>
                                <span class="contact-relation"><?php echo $contact['relation']; ?></span>
                                <span class="contact-phone"><?php echo $contact['phone']; ?></span>
                            </div>
                            <div class="contact-actions">
                                <button class="action-btn call"><i class="ri-phone-fill"></i></button>
                                <button class="action-btn delete"><i class="ri-close-line"></i></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Emergency SMS Alert Configuration -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon orange"><i class="ri-alarm-warning-line"></i></div>
                    <h2>Emergency SMS Alert Configuration</h2>
                </div>
                
                <div class="sms-config">
                    <label class="config-label">Standard Alert Message:</label>
                    <div class="sms-preview-box">
                        <div class="sms-preview-content">
                            <p class="sms-header">🚨 <strong>EMERGENCY ALERT</strong> 🚨</p>
                            <p class="sms-warning">⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠</p>
                            <br>
                            <p>Name: <?php echo $smsConfig['name']; ?></p>
                            <p>PWD ID: <?php echo $smsConfig['pwdId']; ?></p>
                            <p>Phone: <?php echo $smsConfig['phone']; ?></p>
                            <p>Address: <?php echo $smsConfig['address']; ?></p>
                            <p>Status: <?php echo $smsConfig['status']; ?></p>
                            <p>Location: GPS coordinates will be included</p>
                            <p>Time: [Timestamp]</p>
                            <br>
                            <p><strong>Medical Info:</strong></p>
                            <p>• Blood Type: <?php echo $smsConfig['bloodType']; ?></p>
                            <p>• Allergies: <?php echo $smsConfig['allergies']; ?></p>
                            <p>• Medications: <?php echo $smsConfig['medications']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Medication Reminders -->
        <!-- ENHANCED MEDICATION REMINDERS SECTION -->
<!-- Replace lines 263-307 in views/medical-profile.php with this code -->

        <!-- Tab 3: Medication Reminders -->
        <div class="tab-pane" id="medication-reminders">
            
            <!-- Visual Medication Reminders -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon blue"><i class="ri-eye-line"></i></div>
                    <h2>Visual Medication Reminders</h2>
                    <button class="btn btn-add-reminder" id="addReminderBtn">
                        <i class="ri-add-line"></i> Add Reminder
                    </button>
                </div>
                
                <div class="reminders-list">
                    <?php foreach ($medicationReminders as $reminder): ?>
                        <div class="reminder-card">
                            <div class="reminder-icon" style="background: <?php echo $reminder['color']; ?>;">
                                <i class="ri-capsule-fill"></i>
                            </div>
                            <div class="reminder-info">
                                <!-- Medication Name -->
                                <h4 class="reminder-name-display"><?php echo $reminder['name']; ?></h4>
                                <input type="text" class="reminder-name-edit form-control" 
                                       value="<?php echo $reminder['name']; ?>" 
                                       
                                       placeholder="Enter medication name">
                                
                                <!-- Frequency -->
                                <span class="reminder-frequency-display reminder-frequency"><?php echo $reminder['frequency']; ?></span>
                                <select class="reminder-frequency-edit form-control" style="display: none; margin-bottom: 8px; width: auto;">
                                    <option value="Once daily" <?php echo $reminder['frequency'] === 'Once daily' ? 'selected' : ''; ?>>Once daily</option>
                                    <option value="Twice daily" <?php echo $reminder['frequency'] === 'Twice daily' ? 'selected' : ''; ?>>Twice daily</option>
                                    <option value="Three times daily" <?php echo $reminder['frequency'] === 'Three times daily' ? 'selected' : ''; ?>>Three times daily</option>
                                    <option value="Every 4 hours" <?php echo $reminder['frequency'] === 'Every 4 hours' ? 'selected' : ''; ?>>Every 4 hours</option>
                                    <option value="Every 6 hours" <?php echo $reminder['frequency'] === 'Every 6 hours' ? 'selected' : ''; ?>>Every 6 hours</option>
                                    <option value="Every 8 hours" <?php echo $reminder['frequency'] === 'Every 8 hours' ? 'selected' : ''; ?>>Every 8 hours</option>
                                    <option value="As needed" <?php echo $reminder['frequency'] === 'As needed' ? 'selected' : ''; ?>>As needed</option>
                                </select>
                                
                                <span class="reminder-time"><i class="ri-time-line"></i> <?php echo $reminder['time']; ?></span>
                            </div>
                            <div class="reminder-actions">
                                <button class="btn btn-set-time">Set Time</button>
                                <button class="btn btn-delete-reminder">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Reminder Features -->
            <div class="card features-card">
                <div class="card-header">
                    <div class="card-icon yellow"><i class="ri-lightbulb-line"></i></div>
                    <h2>Reminder Features</h2>
                </div>
                
                <div class="features-list">
                    <?php foreach ($reminderFeatures as $feature): ?>
                        <div class="feature-item">
                            <i class="ri-checkbox-circle-fill"></i>
                            <span><?php echo $feature; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>assets/js/medical-profile.js"></script>
</body>
</html>