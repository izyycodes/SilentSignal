// Medical Profile & Pre-Registration JavaScript

document.addEventListener('DOMContentLoaded', function () {

    // ================================
    // STATE MANAGEMENT
    // ================================
    let isEditing = false;
    let hasUnsavedChanges = false;
    let originalFormData = null;

    // ================================
    // INITIALIZE
    // ================================
    function init() {
        // Store original form data
        originalFormData = collectFormData();

        // Make all inputs readonly initially
        setFormReadonly(true);
    }

    // ================================
    // TAB NAVIGATION
    // ================================
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (hasUnsavedChanges) {
                if (!confirm('You have unsaved changes. Do you want to discard them?')) {
                    e.preventDefault();
                    return;
                }
                // Reset changes
                resetForm();
            }

            const tabId = this.getAttribute('data-tab');

            // Remove active from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Add active to clicked tab
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // ================================
    // EDIT/SAVE TOGGLE
    // ================================
    const saveBtn = document.getElementById('saveChangesBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            if (!isEditing) {
                // Enable editing mode
                enableEditMode();
            } else {
                // Save changes
                saveChanges();
            }
        });
    }

    function enableEditMode() {
        isEditing = true;
        setFormReadonly(false);

        // Update button
        saveBtn.innerHTML = '<i class="ri-save-line"></i> Save Changes';
        saveBtn.style.background = 'linear-gradient(135deg, #4caf50 0%, #388e3c 100%)';

        // Add cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn btn-cancel';
        cancelBtn.id = 'cancelChangesBtn';
        cancelBtn.innerHTML = '<i class="ri-close-line"></i> Cancel';
        cancelBtn.style.cssText = 'margin-left: 10px; background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);';
        saveBtn.parentNode.insertBefore(cancelBtn, saveBtn.nextSibling);

        cancelBtn.addEventListener('click', function () {
            if (hasUnsavedChanges) {
                if (!confirm('Discard all changes?')) return;
            }
            disableEditMode();
            resetForm();
        });

        showNotification('Edit mode enabled', 'info');
    }

    function disableEditMode() {
        isEditing = false;
        hasUnsavedChanges = false;
        setFormReadonly(true);

        // Update button
        saveBtn.innerHTML = '<i class="ri-edit-line"></i> Edit Profile';
        saveBtn.style.background = '';

        // Remove cancel button
        const cancelBtn = document.getElementById('cancelChangesBtn');
        if (cancelBtn) cancelBtn.remove();
    }

    function setFormReadonly(readonly) {
        // Personal info inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.readOnly = readonly;
            if (readonly) {
                input.style.background = '#f5f5f5';
                input.style.cursor = 'not-allowed';
            } else {
                input.style.background = '';
                input.style.cursor = 'text';
            }
        });

        // Blood type select
        const bloodTypeSelect = document.getElementById('bloodTypeSelect');
        if (bloodTypeSelect) {
            bloodTypeSelect.disabled = readonly;
        }

        // SMS alert textarea
        const smsTemplate = document.getElementById('smsTemplate');
        if (smsTemplate) {
            smsTemplate.readOnly = readonly;
            if (readonly) {
                smsTemplate.style.background = '#f5f5f5';
            } else {
                smsTemplate.style.background = '';
            }
        }

        // Hide/show action buttons
        document.querySelectorAll('.tag-remove, .item-remove, .action-btn.delete, .btn-add').forEach(btn => {
            btn.style.display = readonly ? 'none' : 'flex';
        });

        // Time picker buttons
        document.querySelectorAll('.btn-set-time').forEach(btn => {
            btn.style.display = readonly ? 'none' : 'inline-flex';
        });

        // Toggle medication reminder editing
        document.querySelectorAll('.reminder-card').forEach(card => {
            const nameDisplay = card.querySelector('.reminder-name-display');
            const nameEdit = card.querySelector('.reminder-name-edit');
            const freqDisplay = card.querySelector('.reminder-frequency-display');
            const freqEdit = card.querySelector('.reminder-frequency-edit');
            const deleteBtn = card.querySelector('.btn-delete-reminder');

            if (nameDisplay && nameEdit) {
                nameDisplay.style.display = readonly ? 'block' : 'none';
                nameEdit.style.display = readonly ? 'none' : 'block';
                if (!readonly) {
                    nameEdit.readOnly = false;
                    nameEdit.style.cursor = 'text';
                }
            }
            if (freqDisplay && freqEdit) {
                freqDisplay.style.display = readonly ? 'inline-block' : 'none';
                freqEdit.style.display = readonly ? 'none' : 'inline-block';
                freqEdit.disabled = readonly;
            }
            if (deleteBtn) {
                deleteBtn.style.display = readonly ? 'none' : 'inline-flex';
            }
        });

    }


    // ================================
    // ADD REMINDER FUNCTIONALITY  
    // ================================

    // ADD THIS EVENT LISTENER for adding new reminders:

    const addReminderBtn = document.getElementById('addReminderBtn');
    if (addReminderBtn) {
        addReminderBtn.addEventListener('click', function () {
            // Create modal/prompt for new reminder
            const name = prompt('Enter medication name:');
            if (!name || name.trim() === '') return;

            const frequencies = [
                'Once daily',
                'Twice daily',
                'Three times daily',
                'Every 4 hours',
                'Every 6 hours',
                'Every 8 hours',
                'As needed'
            ];

            let freqChoice = prompt(
                'Select frequency (enter number):\n' +
                frequencies.map((f, i) => `${i + 1}. ${f}`).join('\n')
            );

            if (!freqChoice || freqChoice < 1 || freqChoice > frequencies.length) {
                freqChoice = 1; // default to once daily
            }

            const frequency = frequencies[parseInt(freqChoice) - 1];
            const time = '8:00 AM'; // default time

            addMedicationReminder(name.trim(), frequency, time);
            hasUnsavedChanges = true;
        });
    }

    // ================================
    // DELETE REMINDER FUNCTIONALITY
    // ================================

    // ADD THIS EVENT DELEGATION for delete buttons:

    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-delete-reminder')) {
            if (confirm('Delete this medication reminder?')) {
                e.target.closest('.reminder-card').remove();
                hasUnsavedChanges = true;
                showNotification('Reminder deleted', 'info');
            }
        }
    });

    // ================================
    // HELPER FUNCTION TO ADD REMINDER
    // ================================

    function addMedicationReminder(name, frequency, time) {
        const container = document.querySelector('.reminders-list');
        if (!container) return;

        const colors = ['#4caf50', '#2196f3', '#ff9800', '#e53935', '#9c27b0'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];

        const card = document.createElement('div');
        card.className = 'reminder-card';
        card.style.animation = 'fadeIn 0.3s ease';

        card.innerHTML = `
        <div class="reminder-icon" style="background: ${randomColor};">
            <i class="ri-capsule-fill"></i>
        </div>
        <div class="reminder-info">
            <h4 class="reminder-name-display" style="display: none;">${name}</h4>
            <input type="text" class="reminder-name-edit form-control" 
                   value="${name}" 
                   style="margin-bottom: 8px; font-size: 1.1rem; font-weight: 600;"
                   placeholder="Enter medication name">
            
            <span class="reminder-frequency-display reminder-frequency" style="display: none;">${frequency}</span>
            <select class="reminder-frequency-edit form-control" style="margin-bottom: 8px; width: auto;">
                <option value="Once daily" ${frequency === 'Once daily' ? 'selected' : ''}>Once daily</option>
                <option value="Twice daily" ${frequency === 'Twice daily' ? 'selected' : ''}>Twice daily</option>
                <option value="Three times daily" ${frequency === 'Three times daily' ? 'selected' : ''}>Three times daily</option>
                <option value="Every 4 hours" ${frequency === 'Every 4 hours' ? 'selected' : ''}>Every 4 hours</option>
                <option value="Every 6 hours" ${frequency === 'Every 6 hours' ? 'selected' : ''}>Every 6 hours</option>
                <option value="Every 8 hours" ${frequency === 'Every 8 hours' ? 'selected' : ''}>Every 8 hours</option>
                <option value="As needed" ${frequency === 'As needed' ? 'selected' : ''}>As needed</option>
            </select>
            
            <span class="reminder-time"><i class="ri-time-line"></i> ${time}</span>
        </div>
        <div class="reminder-actions">
            <button class="btn btn-set-time">Set Time</button>
            <button class="btn btn-delete-reminder" style="background: #f44336; margin-left: 5px; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer;">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    `;

        container.appendChild(card);
        showNotification('Reminder added! Remember to save.', 'success');
    }

    function saveChanges() {
        // Collect form data
        const formData = collectFormData();

        // Show saving state
        saveBtn.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';
        saveBtn.disabled = true;

        // Send to server
        fetch(BASE_URL + 'index.php?action=save-medical-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update original data
                    originalFormData = formData;
                    hasUnsavedChanges = false;

                    saveBtn.innerHTML = '<i class="ri-check-line"></i> Saved!';
                    showNotification('Changes saved successfully!', 'success');

                    // Reset button and exit edit mode after delay
                    setTimeout(() => {
                        saveBtn.disabled = false;
                        disableEditMode();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to save');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                saveBtn.innerHTML = '<i class="ri-error-warning-line"></i> Save Failed';
                saveBtn.style.background = 'linear-gradient(135deg, #f44336 0%, #d32f2f 100%)';
                showNotification('Failed to save changes: ' + error.message, 'error');

                setTimeout(() => {
                    saveBtn.innerHTML = '<i class="ri-save-line"></i> Save Changes';
                    saveBtn.style.background = '';
                    saveBtn.disabled = false;
                }, 3000);
            });
    }

    function resetForm() {
    if (!originalFormData) return;

    // Reset all form inputs by name - data is now flat, not nested
    document.querySelectorAll('.form-control[name]').forEach(input => {
        const name = input.getAttribute('name');
        if (originalFormData[name] !== undefined) {
            input.value = originalFormData[name];
        }
    });

    hasUnsavedChanges = false;
}

    // ================================
    // TRACK CHANGES
    // ================================
    document.addEventListener('input', function (e) {
        if (isEditing && e.target.matches('.form-control, #bloodTypeSelect, #smsTemplate, .reminder-name-edit, .reminder-frequency-edit')) {
            hasUnsavedChanges = true;
        }
    });

    // ================================
    // BLOOD TYPE EDITOR
    // ================================
    const bloodTypeDisplay = document.querySelector('.blood-type-value');
    if (bloodTypeDisplay) {
        // Create hidden select
        const bloodTypeSelect = document.createElement('select');
        bloodTypeSelect.id = 'bloodTypeSelect';
        bloodTypeSelect.className = 'form-control';
        bloodTypeSelect.style.cssText = 'font-size: 1.5rem; font-weight: bold; text-align: center; display: none;';
        bloodTypeSelect.disabled = true;

        const bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        bloodTypes.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            if (type === bloodTypeDisplay.textContent.trim()) {
                option.selected = true;
            }
            bloodTypeSelect.appendChild(option);
        });

        bloodTypeDisplay.parentNode.appendChild(bloodTypeSelect);

        // Toggle display/select based on edit mode
        const originalSetReadonly = setFormReadonly;
        setFormReadonly = function (readonly) {
            originalSetReadonly(readonly);
            if (readonly) {
                bloodTypeDisplay.style.display = '';
                bloodTypeSelect.style.display = 'none';
                bloodTypeDisplay.textContent = bloodTypeSelect.value;
            } else {
                bloodTypeDisplay.style.display = 'none';
                bloodTypeSelect.style.display = 'block';
            }
        };

        bloodTypeSelect.addEventListener('change', function () {
            hasUnsavedChanges = true;
        });
    }

    // ================================
    // SMS ALERT CONFIGURATION
    // ================================
    const smsPreviewContent = document.querySelector('.sms-preview-content');
    if (smsPreviewContent) {
        // Make SMS template editable
        const smsText = smsPreviewContent.innerHTML;
        const smsTemplate = document.createElement('textarea');
        smsTemplate.id = 'smsTemplate';
        smsTemplate.className = 'form-control';
        smsTemplate.style.cssText = 'width: 100%; min-height: 300px; font-family: monospace; font-size: 13px; display: none; resize: vertical;';
        smsTemplate.value = smsPreviewContent.textContent.trim();
        smsTemplate.readOnly = true;

        smsPreviewContent.parentNode.appendChild(smsTemplate);

        // Add to readonly toggle
        const originalSetReadonly2 = setFormReadonly;
        setFormReadonly = function (readonly) {
            originalSetReadonly2(readonly);
            if (readonly) {
                smsPreviewContent.style.display = '';
                smsTemplate.style.display = 'none';
                // Update preview with template
                const lines = smsTemplate.value.split('\n');
                let html = '';
                lines.forEach(line => {
                    if (line.includes('EMERGENCY ALERT')) {
                        html += `<p class="sms-header">${line}</p>`;
                    } else if (line.includes('DEAF/MUTE')) {
                        html += `<p class="sms-warning">${line}</p>`;
                    } else if (line.trim() === '') {
                        html += '<br>';
                    } else {
                        html += `<p>${line}</p>`;
                    }
                });
                smsPreviewContent.innerHTML = html;
            } else {
                smsPreviewContent.style.display = 'none';
                smsTemplate.style.display = 'block';
            }
        };
    }

    // ================================
    // MEDICATION REMINDER TIME PICKER
    // ================================
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-set-time')) {
            const reminderCard = e.target.closest('.reminder-card');
            const timeSpan = reminderCard.querySelector('.reminder-time');

            // Create time picker modal
            const modal = createTimePickerModal(timeSpan.textContent, reminderCard);
            document.body.appendChild(modal);

            modal.style.display = 'flex';

            hasUnsavedChanges = true;
        }
    });

    function createTimePickerModal(currentTime, reminderCard) {
        const modal = document.createElement('div');
        modal.className = 'time-picker-modal';
        modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;

        // Extract current times or use defaults
        let time1 = '08:00';
        let time2 = '20:00';

        const timeText = currentTime.replace(/.*?(\d+:\d+\s*[AP]M)/gi, '$1');
        const timeMatch = currentTime.match(/(\d+:\d+\s*[AP]M)/gi);

        if (timeMatch && timeMatch.length >= 2) {
            time1 = convertTo24Hour(timeMatch[0]);
            time2 = convertTo24Hour(timeMatch[1]);
        } else if (timeMatch && timeMatch.length === 1) {
            time1 = convertTo24Hour(timeMatch[0]);
        }

        modal.innerHTML = `
        <div style="background: white; border-radius: 15px; padding: 30px; width: 400px; max-width: 90%;">
            <h3 style="margin: 0 0 20px 0; color: #333;">Set Reminder Times</h3>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 600;">Morning Time</label>
                <input type="time" class="time-input-1" value="${time1}" 
                       style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 600;">Evening Time</label>
                <input type="time" class="time-input-2" value="${time2}" 
                       style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn-modal-save" style="flex: 1; padding: 12px; background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="ri-check-line"></i> Save Times
                </button>
                <button class="btn-modal-cancel" style="flex: 1; padding: 12px; background: #e0e0e0; color: #666; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="ri-close-line"></i> Cancel
                </button>
            </div>
        </div>
    `;

        // Save button handler
        modal.querySelector('.btn-modal-save').addEventListener('click', function () {
            const newTime1 = modal.querySelector('.time-input-1').value;
            const newTime2 = modal.querySelector('.time-input-2').value;

            if (!newTime1 || !newTime2) {
                alert('Please set both morning and evening times.');
                return;
            }

            const formatted1 = convertTo12Hour(newTime1);
            const formatted2 = convertTo12Hour(newTime2);

            // Update the time display in the reminder card
            const timeSpan = reminderCard.querySelector('.reminder-time');
            timeSpan.innerHTML = `<i class="ri-time-line"></i> ${formatted1}, ${formatted2}`;

            modal.remove();
            showNotification('Reminder times updated', 'success');
            hasUnsavedChanges = true;
        });

        // Cancel button handler
        modal.querySelector('.btn-modal-cancel').addEventListener('click', function () {
            modal.remove();
        });

        // Click outside to close
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.remove();
            }
        });

        return modal;
    }

    function convertTo24Hour(time12h) {
        // Clean up the input
        time12h = time12h.trim();

        // Check if already in 24-hour format
        if (/^\d{2}:\d{2}$/.test(time12h)) {
            return time12h;
        }

        // Parse 12-hour format
        const match = time12h.match(/(\d+):(\d+)\s*(AM|PM)/i);
        if (!match) {
            return '08:00'; // Default fallback
        }

        let [, hours, minutes, modifier] = match;
        hours = parseInt(hours, 10);

        if (modifier.toUpperCase() === 'PM' && hours !== 12) {
            hours += 12;
        } else if (modifier.toUpperCase() === 'AM' && hours === 12) {
            hours = 0;
        }

        return `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    function convertTo12Hour(time24h) {
        if (!time24h) return '8:00 AM';

        let [hours, minutes] = time24h.split(':');
        hours = parseInt(hours, 10);

        const modifier = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;

        return `${hours}:${minutes} ${modifier}`;
    }

    // ================================
    // ADD ALLERGY
    // ================================
    const addAllergyBtn = document.getElementById('addAllergyBtn');
    if (addAllergyBtn) {
        addAllergyBtn.addEventListener('click', function () {
            const allergy = prompt('Enter allergy:');
            if (allergy && allergy.trim()) {
                addTag('allergiesContainer', allergy.trim(), 'red');
                hasUnsavedChanges = true;
            }
        });
    }

    // ================================
    // ADD MEDICATION
    // ================================
    const addMedicationBtn = document.getElementById('addMedicationBtn');
    if (addMedicationBtn) {
        addMedicationBtn.addEventListener('click', function () {
            const medication = prompt('Enter medication name and dosage:');
            if (medication && medication.trim()) {
                addMedication('medicationsContainer', medication.trim());
                hasUnsavedChanges = true;
            }
        });
    }

    // ================================
    // ADD MEDICAL CONDITION
    // ================================
    const addConditionBtn = document.getElementById('addConditionBtn');
    if (addConditionBtn) {
        addConditionBtn.addEventListener('click', function () {
            const condition = prompt('Enter medical condition:');
            if (condition && condition.trim()) {
                addTag('conditionsContainer', condition.trim(), 'yellow');
                hasUnsavedChanges = true;
            }
        });
    }

    // ================================
    // ADD CONTACT
    // ================================
    const addContactBtn = document.getElementById('addContactBtn');
    if (addContactBtn) {
        addContactBtn.addEventListener('click', function () {
            const name = prompt('Enter contact name:');
            if (name && name.trim()) {
                const relation = prompt('Enter relation (e.g., Mother, Father, Spouse):');
                const phone = prompt('Enter phone number:');

                if (relation && phone) {
                    addContact(name.trim(), relation.trim(), phone.trim());
                    hasUnsavedChanges = true;
                }
            }
        });
    }

    // ================================
    // TAG REMOVAL EVENT DELEGATION
    // ================================
    document.addEventListener('click', function (e) {
        if (e.target.closest('.tag-remove')) {
            const tag = e.target.closest('.tag');
            if (tag) {
                tag.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => tag.remove(), 300);
                hasUnsavedChanges = true;
            }
        }

        if (e.target.closest('.item-remove')) {
            const item = e.target.closest('.medication-item');
            if (item) {
                item.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => item.remove(), 300);
                hasUnsavedChanges = true;
            }
        }

        if (e.target.closest('.action-btn.delete')) {
            const card = e.target.closest('.contact-card');
            if (card && confirm('Remove this contact?')) {
                card.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => card.remove(), 300);
                hasUnsavedChanges = true;
            }
        }
    });

    // ================================
    // PREVENT NAVIGATION WITH UNSAVED CHANGES
    // ================================
    window.addEventListener('beforeunload', function (e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });

    // Intercept sidebar links
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', function (e) {
            if (hasUnsavedChanges) {
                if (!confirm('You have unsaved changes. Do you want to discard them and leave this page?')) {
                    e.preventDefault();
                }
            }
        });
    });

    // ================================
    // HELPER FUNCTIONS
    // ================================

    function addTag(containerId, text, color) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const tag = document.createElement('div');
        tag.className = `tag tag-${color}`;
        tag.innerHTML = `
            <span>${text}</span>
            <button class="tag-remove"><i class="ri-close-line"></i></button>
        `;
        tag.style.animation = 'fadeIn 0.3s ease';

        // Hide remove button if not editing
        if (!isEditing) {
            tag.querySelector('.tag-remove').style.display = 'none';
        }

        container.appendChild(tag);
    }

    function addMedication(containerId, text) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const item = document.createElement('div');
        item.className = 'medication-item';
        item.innerHTML = `
            <i class="ri-medicine-bottle-line"></i>
            <span>${text}</span>
            <button class="item-remove"><i class="ri-close-line"></i></button>
        `;
        item.style.animation = 'fadeIn 0.3s ease';

        // Hide remove button if not editing
        if (!isEditing) {
            item.querySelector('.item-remove').style.display = 'none';
        }

        container.appendChild(item);
    }

    function addContact(name, relation, phone) {
        const container = document.querySelector('.contacts-list');
        if (!container) return;

        const colors = ['#e53935', '#43a047', '#1e88e5', '#8e24aa', '#ff9800'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

        const card = document.createElement('div');
        card.className = 'contact-card';
        card.innerHTML = `
            <div class="contact-avatar" style="background: ${randomColor};">
                ${initials}
            </div>
            <div class="contact-info">
                <h4>${name}</h4>
                <span class="contact-relation">${relation}</span>
                <span class="contact-phone">${phone}</span>
            </div>
            <div class="contact-actions">
                <button class="action-btn call"><i class="ri-phone-fill"></i></button>
                <button class="action-btn delete"><i class="ri-close-line"></i></button>
            </div>
        `;
        card.style.animation = 'fadeIn 0.3s ease';

        // Hide delete button if not editing
        if (!isEditing) {
            card.querySelector('.action-btn.delete').style.display = 'none';
        }

        container.appendChild(card);
        showNotification('Contact added successfully!', 'success');
    }

    function collectFormData() {
    const data = {};

    // Get all form inputs with name attribute - FLATTEN to root level
    document.querySelectorAll('.form-control[name]').forEach(input => {
        const name = input.getAttribute('name');
        data[name] = input.value;
    });

    // Get blood type
    const bloodTypeSelect = document.getElementById('bloodTypeSelect');
    if (bloodTypeSelect) {
        data.bloodType = bloodTypeSelect.value;
    } else {
        const bloodTypeDisplay = document.querySelector('.blood-type-value');
        if (bloodTypeDisplay) {
            data.bloodType = bloodTypeDisplay.textContent.trim();
        }
    }

    // Get disability type
    data.disabilityType = 'Deaf/Mute'; // or get from form if editable

    // Get SMS template
    const smsTemplate = document.getElementById('smsTemplate');
    if (smsTemplate) {
        data.smsTemplate = smsTemplate.value;
    }

    // Get allergies
    data.allergies = [];
    document.querySelectorAll('#allergiesContainer .tag span').forEach(tag => {
        data.allergies.push(tag.textContent);
    });

    // Get medications
    data.medications = [];
    document.querySelectorAll('#medicationsContainer .medication-item span').forEach(item => {
        data.medications.push(item.textContent);
    });

    // Get medical conditions - CORRECT KEY NAME
    data.medicalConditions = [];
    document.querySelectorAll('#conditionsContainer .tag span').forEach(tag => {
        data.medicalConditions.push(tag.textContent);
    });

    // Get emergency contacts - CORRECT KEY NAME
    data.emergencyContacts = [];
    document.querySelectorAll('.contact-card').forEach(card => {
        const name = card.querySelector('h4').textContent;
        const relation = card.querySelector('.contact-relation').textContent;
        const phone = card.querySelector('.contact-phone').textContent;
        const initials = card.querySelector('.contact-avatar').textContent.trim();
        const color = card.querySelector('.contact-avatar').style.background;
        
        data.emergencyContacts.push({
            name: name,
            relation: relation,
            phone: phone,
            initials: initials,
            color: color
        });
    });

    // Get medication reminders
    data.medicationReminders = [];
    document.querySelectorAll('.reminder-card').forEach(card => {
        const name = card.querySelector('h4').textContent;
        const frequency = card.querySelector('.reminder-frequency').textContent;
        const timeElement = card.querySelector('.reminder-time');
        const time = timeElement ? timeElement.textContent.replace(/.*?(\d+:\d+\s*[AP]M.*)/i, '$1') : '';
        const iconElement = card.querySelector('.reminder-icon');
        const color = iconElement ? iconElement.style.background : '';
        
        data.medicationReminders.push({
            name: name,
            frequency: frequency,
            time: time,
            color: color
        });
    });

    return data;
}
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'notification';

        let icon = 'checkbox-circle';
        let color = '#4caf50';

        if (type === 'error') {
            icon = 'error-warning';
            color = '#f44336';
        } else if (type === 'info') {
            icon = 'information';
            color = '#2196f3';
        }

        notification.innerHTML = `
            <i class="ri-${icon}-fill"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 15px 20px;
            background: ${color};
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            z-index: 10000;
            animation: slideIn 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // ================================
    // CSS ANIMATIONS
    // ================================
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(20px); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
    `;
    document.head.appendChild(style);

    // Initialize on load
    init();
});