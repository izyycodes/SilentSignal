// Medical Profile & Pre-Registration JavaScript

document.addEventListener('DOMContentLoaded', function () {

    // ================================
    // STATE MANAGEMENT
    // ================================
    let isEditing = false;
    let hasUnsavedChanges = false;
    let originalFormData = null;

    // ================================
    // CUSTOM MODAL SYSTEM
    // ================================
    const modalOverlay = document.getElementById('customModalOverlay');
    const modal        = document.getElementById('customModal');
    const modalIcon    = document.getElementById('customModalIcon');
    const modalTitle   = document.getElementById('customModalTitle');
    const modalBody    = document.getElementById('customModalBody');
    const modalFooter  = document.getElementById('customModalFooter');
    const modalClose   = document.getElementById('customModalClose');

    function openModal() {
        modalOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.style.animation = 'none';
        modalOverlay.style.animation = 'none';
        modalOverlay.style.display = 'none';
        modal.style.animation = '';
        modalOverlay.style.animation = '';
        document.body.style.overflow = '';
    }

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function (e) {
            if (e.target === modalOverlay) closeModal();
        });
    }

    /**
     * showInputModal — replaces prompt()
     * fields: [{ name, label, type='text', placeholder, value, options (for select) }]
     * Returns a Promise that resolves with { fieldName: value, ... } or null if cancelled.
     */
    function showInputModal({ title, icon = 'ri-edit-line', iconClass = 'icon-teal', description = '', fields = [] }) {
        return new Promise((resolve) => {
            modalIcon.className = 'custom-modal-icon ' + iconClass;
            modalIcon.innerHTML = `<i class="${icon}"></i>`;
            modalTitle.textContent = title;

            let bodyHTML = description ? `<p class="modal-description">${description}</p><hr class="modal-divider">` : '';

            fields.forEach(field => {
                bodyHTML += `<div class="modal-field">`;
                bodyHTML += `<label for="mf_${field.name}">${field.label}</label>`;

                if (field.type === 'select') {
                    bodyHTML += `<select id="mf_${field.name}" class="form-control">`;
                    field.options.forEach(opt => {
                        const selected = field.value === opt.value ? 'selected' : '';
                        bodyHTML += `<option value="${opt.value}" ${selected}>${opt.label}</option>`;
                    });
                    bodyHTML += `</select>`;
                } else {
                    bodyHTML += `<input type="${field.type || 'text'}" 
                        id="mf_${field.name}" 
                        class="form-control" 
                        placeholder="${field.placeholder || ''}" 
                        value="${field.value || ''}">`;
                }
                bodyHTML += `</div>`;
            });

            modalBody.innerHTML = bodyHTML;
            modalFooter.innerHTML = `
                <button class="modal-btn modal-btn-secondary" id="mf_cancel"><i class="ri-close-line"></i> Cancel</button>
                <button class="modal-btn modal-btn-primary" id="mf_confirm"><i class="ri-check-line"></i> Confirm</button>
            `;

            openModal();

            // Focus first input
            const firstInput = modalBody.querySelector('input, select');
            if (firstInput) setTimeout(() => firstInput.focus(), 100);

            // Allow Enter to submit
            modalBody.addEventListener('keydown', function handler(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('mf_confirm')?.click();
                    modalBody.removeEventListener('keydown', handler);
                }
            });

            document.getElementById('mf_cancel').addEventListener('click', () => {
                closeModal();
                resolve(null);
            });

            document.getElementById('mf_confirm').addEventListener('click', () => {
                const result = {};
                fields.forEach(field => {
                    const el = document.getElementById('mf_' + field.name);
                    result[field.name] = el ? el.value.trim() : '';
                });
                closeModal();
                resolve(result);
            });
        });
    }

    /**
     * showConfirmModal — replaces confirm()
     * Returns a Promise that resolves with true (confirmed) or false (cancelled).
     */
    function showConfirmModal({ title, message, icon = 'ri-alert-line', iconClass = 'icon-red', confirmLabel = 'Confirm', confirmClass = 'modal-btn-danger', warning = '' }) {
        return new Promise((resolve) => {
            modalIcon.className = 'custom-modal-icon ' + iconClass;
            modalIcon.innerHTML = `<i class="${icon}"></i>`;
            modalTitle.textContent = title;

            let bodyHTML = `<p class="modal-confirm-text">${message}</p>`;
            if (warning) {
                bodyHTML += `<div class="modal-warning-box"><i class="ri-error-warning-line"></i> ${warning}</div>`;
            }
            modalBody.innerHTML = bodyHTML;

            modalFooter.innerHTML = `
                <button class="modal-btn modal-btn-secondary" id="mc_cancel"><i class="ri-close-line"></i> Cancel</button>
                <button class="modal-btn ${confirmClass}" id="mc_confirm"><i class="ri-check-line"></i> ${confirmLabel}</button>
            `;

            openModal();

            document.getElementById('mc_cancel').addEventListener('click', () => {
                closeModal();
                resolve(false);
            });
            document.getElementById('mc_confirm').addEventListener('click', () => {
                closeModal();
                resolve(true);
            });
        });
    }

    /**
     * showAlertModal — replaces alert()
     */
    function showAlertModal({ title, message, icon = 'ri-information-line', iconClass = 'icon-blue', buttonLabel = 'OK' }) {
        return new Promise((resolve) => {
            modalIcon.className = 'custom-modal-icon ' + iconClass;
            modalIcon.innerHTML = `<i class="${icon}"></i>`;
            modalTitle.textContent = title;
            modalBody.innerHTML = `<p class="modal-confirm-text">${message}</p>`;
            modalFooter.innerHTML = `
                <button class="modal-btn modal-btn-primary" id="ma_ok"><i class="ri-check-line"></i> ${buttonLabel}</button>
            `;
            openModal();
            document.getElementById('ma_ok').addEventListener('click', () => {
                closeModal();
                resolve();
            });
        });
    }

    // ================================
    // INITIALIZE
    // ================================
    function init() {
        originalFormData = collectFormData();
        setFormReadonly(true);
    }

    // ================================
    // TAB NAVIGATION
    // ================================
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', async function (e) {
            if (hasUnsavedChanges) {
                const confirmed = await showConfirmModal({
                    title: 'Unsaved Changes',
                    message: 'You have unsaved changes on this tab. Switching tabs will discard them.',
                    icon: 'ri-edit-line',
                    iconClass: 'icon-orange',
                    confirmLabel: 'Discard & Switch',
                    confirmClass: 'modal-btn-danger',
                    warning: 'This action cannot be undone.'
                });
                if (!confirmed) return;
                resetForm();
            }

            const tabId = this.getAttribute('data-tab');
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
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
                enableEditMode();
            } else {
                saveChanges();
            }
        });
    }

    function enableEditMode() {
        isEditing = true;
        setFormReadonly(false);

        saveBtn.innerHTML = '<i class="ri-save-line"></i> Save Changes';
        saveBtn.style.background = 'linear-gradient(135deg, #4caf50 0%, #388e3c 100%)';

        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn btn-cancel';
        cancelBtn.id = 'cancelChangesBtn';
        cancelBtn.innerHTML = '<i class="ri-close-line"></i> Cancel';
        cancelBtn.style.cssText = 'margin-left: 10px; background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: #fff;';
        saveBtn.parentNode.insertBefore(cancelBtn, saveBtn.nextSibling);

        cancelBtn.addEventListener('click', async function () {
            if (hasUnsavedChanges) {
                const confirmed = await showConfirmModal({
                    title: 'Discard Changes',
                    message: 'Are you sure you want to discard all unsaved changes?',
                    icon: 'ri-edit-line',
                    iconClass: 'icon-orange',
                    confirmLabel: 'Discard',
                    confirmClass: 'modal-btn-danger'
                });
                if (!confirmed) return;
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

        saveBtn.innerHTML = '<i class="ri-edit-line"></i> Edit Profile';
        saveBtn.style.background = '';

        const cancelBtn = document.getElementById('cancelChangesBtn');
        if (cancelBtn) cancelBtn.remove();
    }

    function setFormReadonly(readonly) {
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

        // Disability is always readonly — no edit mode toggle needed
        // The hidden input always carries "Deaf/Mute"

        // Blood type select
        const bloodTypeSelect = document.getElementById('bloodTypeSelect');
        if (bloodTypeSelect) {
            bloodTypeSelect.disabled = readonly;
        }

        // SMS alert textarea
        const smsTemplate = document.getElementById('smsTemplate');
        if (smsTemplate) {
            smsTemplate.readOnly = readonly;
            smsTemplate.style.background = readonly ? '#f5f5f5' : '';
        }

        // Hide/show action buttons
        document.querySelectorAll('.tag-remove, .item-remove, .action-btn.delete, .btn-add').forEach(btn => {
            btn.style.display = readonly ? 'none' : 'flex';
        });

        const addContactBtn = document.getElementById('addContactBtn');
        if (addContactBtn) addContactBtn.style.display = readonly ? 'none' : 'inline-flex';

        const addReminderBtn = document.getElementById('addReminderBtn');
        if (addReminderBtn) addReminderBtn.style.display = readonly ? 'none' : 'inline-flex';

        document.querySelectorAll('.btn-set-time').forEach(btn => {
            btn.style.display = readonly ? 'none' : 'inline-flex';
        });

        // Toggle medication reminder editing
        // IMPORTANT: when switching TO readonly, sync input values → display elements first
        document.querySelectorAll('.reminder-card').forEach(card => {
            const nameDisplay = card.querySelector('.reminder-name-display');
            const nameEdit    = card.querySelector('.reminder-name-edit');
            const freqDisplay = card.querySelector('.reminder-frequency-display');
            const freqEdit    = card.querySelector('.reminder-frequency-edit');
            const deleteBtn   = card.querySelector('.btn-delete-reminder');

            if (nameDisplay && nameEdit) {
                // Sync input → h4 before switching to readonly
                if (readonly && nameEdit.value.trim()) {
                    nameDisplay.textContent = nameEdit.value.trim();
                }
                nameDisplay.style.display = readonly ? 'block' : 'none';
                nameEdit.style.display    = readonly ? 'none'  : 'block';
                if (!readonly) {
                    nameEdit.readOnly     = false;
                    nameEdit.style.cursor = 'text';
                    // Sync h4 → input when entering edit mode
                    nameEdit.value = nameDisplay.textContent.trim();
                }
            }
            if (freqDisplay && freqEdit) {
                // Sync select → span before switching to readonly
                if (readonly && freqEdit.value) {
                    freqDisplay.textContent = freqEdit.value;
                }
                freqDisplay.style.display = readonly ? 'inline-block' : 'none';
                freqEdit.style.display    = readonly ? 'none'         : 'inline-block';
                freqEdit.disabled         = readonly;
                if (!readonly) {
                    // Sync span → select when entering edit mode
                    freqEdit.value = freqDisplay.textContent.trim();
                }
            }
            if (deleteBtn) deleteBtn.style.display = readonly ? 'none' : 'inline-flex';
        });
    }

    // ================================
    // ADD REMINDER — uses modal
    // ================================
    const addReminderBtn = document.getElementById('addReminderBtn');
    if (addReminderBtn) {
        addReminderBtn.addEventListener('click', async function () {
            const result = await showInputModal({
                title: 'Add Medication Reminder',
                icon: 'ri-capsule-line',
                iconClass: 'icon-blue',
                description: 'Fill in the details for your new medication reminder.',
                fields: [
                    { name: 'name', label: 'Medication Name', placeholder: 'e.g. Metformin 500mg', value: '' },
                    {
                        name: 'frequency', label: 'Frequency', type: 'select',
                        value: 'Once daily',
                        options: [
                            { value: 'Once daily',        label: 'Once daily' },
                            { value: 'Twice daily',       label: 'Twice daily' },
                            { value: 'Three times daily', label: 'Three times daily' },
                            { value: 'Every 4 hours',     label: 'Every 4 hours' },
                            { value: 'Every 6 hours',     label: 'Every 6 hours' },
                            { value: 'Every 8 hours',     label: 'Every 8 hours' },
                            { value: 'As needed',         label: 'As needed' }
                        ]
                    }
                ]
            });

            if (!result || !result.name) return;

            addMedicationReminder(result.name, result.frequency, '8:00 AM');
            hasUnsavedChanges = true;
        });
    }

    // ================================
    // DELETE REMINDER — uses modal
    // ================================
    document.addEventListener('click', async function (e) {
        if (e.target.closest('.btn-delete-reminder')) {
            const card = e.target.closest('.reminder-card');
            const medName = card?.querySelector('h4')?.textContent || 'this reminder';

            const confirmed = await showConfirmModal({
                title: 'Delete Reminder',
                message: `Are you sure you want to delete the reminder for <strong>${medName}</strong>?`,
                icon: 'ri-delete-bin-line',
                iconClass: 'icon-red',
                confirmLabel: 'Delete',
                confirmClass: 'modal-btn-danger'
            });

            if (confirmed) {
                card.remove();
                hasUnsavedChanges = true;
                showNotification('Reminder deleted', 'info');
            }
        }
    });

    // ================================
    // HELPER: ADD REMINDER CARD
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
                    <option value="Once daily"        ${frequency === 'Once daily'        ? 'selected' : ''}>Once daily</option>
                    <option value="Twice daily"       ${frequency === 'Twice daily'       ? 'selected' : ''}>Twice daily</option>
                    <option value="Three times daily" ${frequency === 'Three times daily' ? 'selected' : ''}>Three times daily</option>
                    <option value="Every 4 hours"     ${frequency === 'Every 4 hours'     ? 'selected' : ''}>Every 4 hours</option>
                    <option value="Every 6 hours"     ${frequency === 'Every 6 hours'     ? 'selected' : ''}>Every 6 hours</option>
                    <option value="Every 8 hours"     ${frequency === 'Every 8 hours'     ? 'selected' : ''}>Every 8 hours</option>
                    <option value="As needed"         ${frequency === 'As needed'         ? 'selected' : ''}>As needed</option>
                </select>
                <span class="reminder-time"><i class="ri-time-line"></i> ${time}</span>
            </div>
            <div class="reminder-actions">
                <button class="btn btn-set-time">Set Time</button>
                <button class="btn btn-delete-reminder" style="background: #f44336; margin-left: 5px; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center;">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;

        container.appendChild(card);
        showNotification('Reminder added! Remember to save.', 'success');
    }

    // ================================
    // SAVE CHANGES
    // ================================
    function saveChanges() {
        const formData = collectFormData();

        saveBtn.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';
        saveBtn.disabled = true;

        fetch(BASE_URL + 'index.php?action=save-medical-profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                originalFormData = formData;
                hasUnsavedChanges = false;

                const disabilityDisplay = document.getElementById('disabilityDisplay');
                if (disabilityDisplay) disabilityDisplay.textContent = 'Deaf/Mute';

                saveBtn.innerHTML = '<i class="ri-check-line"></i> Saved!';
                showNotification('Changes saved successfully!', 'success');

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
        const bloodTypeSelect = document.createElement('select');
        bloodTypeSelect.id = 'bloodTypeSelect';
        bloodTypeSelect.className = 'form-control';
        bloodTypeSelect.style.cssText = 'font-size: 1.5rem; font-weight: bold; text-align: center; display: none;';
        bloodTypeSelect.disabled = true;

        ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            if (type === bloodTypeDisplay.textContent.trim()) option.selected = true;
            bloodTypeSelect.appendChild(option);
        });

        bloodTypeDisplay.parentNode.appendChild(bloodTypeSelect);

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
        const smsTemplate = document.createElement('textarea');
        smsTemplate.id = 'smsTemplate';
        smsTemplate.className = 'form-control';
        smsTemplate.style.cssText = 'width: 100%; min-height: 300px; font-family: monospace; font-size: 13px; display: none; resize: vertical;';
        smsTemplate.value = smsPreviewContent.textContent.trim();
        smsTemplate.readOnly = true;
        smsPreviewContent.parentNode.appendChild(smsTemplate);

        const originalSetReadonly2 = setFormReadonly;
        setFormReadonly = function (readonly) {
            originalSetReadonly2(readonly);
            if (readonly) {
                smsPreviewContent.style.display = '';
                smsTemplate.style.display = 'none';
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
    // MEDICATION REMINDER TIME PICKER — modal
    // ================================
    document.addEventListener('click', async function (e) {
        if (e.target.closest('.btn-set-time')) {
            const reminderCard = e.target.closest('.reminder-card');
            const timeSpan = reminderCard.querySelector('.reminder-time');

            // Parse current times
            const timeMatch = timeSpan.textContent.match(/(\d+:\d+\s*[AP]M)/gi);
            const defaultTime1 = timeMatch && timeMatch[0] ? convertTo24Hour(timeMatch[0]) : '08:00';
            const defaultTime2 = timeMatch && timeMatch[1] ? convertTo24Hour(timeMatch[1]) : '20:00';

            // Build a custom time picker modal
            modalIcon.className = 'custom-modal-icon icon-blue';
            modalIcon.innerHTML = '<i class="ri-time-line"></i>';
            modalTitle.textContent = 'Set Reminder Times';
            modalBody.innerHTML = `
                <p class="modal-description">Choose when you want to be reminded to take this medication.</p>
                <hr class="modal-divider">
                <div class="time-fields-row">
                    <div class="modal-field">
                        <label for="tp_time1">Morning Time</label>
                        <input type="time" id="tp_time1" class="form-control" value="${defaultTime1}">
                    </div>
                    <div class="modal-field">
                        <label for="tp_time2">Evening Time</label>
                        <input type="time" id="tp_time2" class="form-control" value="${defaultTime2}">
                    </div>
                </div>
            `;
            modalFooter.innerHTML = `
                <button class="modal-btn modal-btn-secondary" id="tp_cancel"><i class="ri-close-line"></i> Cancel</button>
                <button class="modal-btn modal-btn-save" id="tp_save"><i class="ri-save-line"></i> Save Times</button>
            `;
            openModal();

            document.getElementById('tp_cancel').addEventListener('click', closeModal);
            document.getElementById('tp_save').addEventListener('click', function () {
                const t1 = document.getElementById('tp_time1').value;
                const t2 = document.getElementById('tp_time2').value;

                if (!t1 || !t2) {
                    showAlertModal({
                        title: 'Both Times Required',
                        message: 'Please set both morning and evening reminder times before saving.',
                        icon: 'ri-alert-line',
                        iconClass: 'icon-orange'
                    });
                    return;
                }

                timeSpan.innerHTML = `<i class="ri-time-line"></i> ${convertTo12Hour(t1)}, ${convertTo12Hour(t2)}`;
                closeModal();
                showNotification('Reminder times updated', 'success');
                hasUnsavedChanges = true;
            });
        }
    });

    function convertTo24Hour(time12h) {
        time12h = time12h.trim();
        if (/^\d{2}:\d{2}$/.test(time12h)) return time12h;
        const match = time12h.match(/(\d+):(\d+)\s*(AM|PM)/i);
        if (!match) return '08:00';
        let [, hours, minutes, modifier] = match;
        hours = parseInt(hours, 10);
        if (modifier.toUpperCase() === 'PM' && hours !== 12) hours += 12;
        else if (modifier.toUpperCase() === 'AM' && hours === 12) hours = 0;
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
    // ADD ALLERGY — modal
    // ================================
    const addAllergyBtn = document.getElementById('addAllergyBtn');
    if (addAllergyBtn) {
        addAllergyBtn.addEventListener('click', async function () {
            const result = await showInputModal({
                title: 'Add Allergy',
                icon: 'ri-alert-line',
                iconClass: 'icon-red',
                fields: [
                    { name: 'allergy', label: 'Allergy', placeholder: 'e.g. Penicillin, Peanuts, Latex', value: '' }
                ]
            });
            if (result && result.allergy) {
                addTag('allergiesContainer', result.allergy, 'red');
                hasUnsavedChanges = true;
            }
        });
    }

    // ================================
    // ADD MEDICATION — modal
    // ================================
    const addMedicationBtn = document.getElementById('addMedicationBtn');
    if (addMedicationBtn) {
        addMedicationBtn.addEventListener('click', async function () {
            const result = await showInputModal({
                title: 'Add Medication',
                icon: 'ri-capsule-line',
                iconClass: 'icon-green',
                fields: [
                    { name: 'medication', label: 'Medication Name & Dosage', placeholder: 'e.g. Metformin 500mg twice daily', value: '' }
                ]
            });
            if (result && result.medication) {
                addMedication('medicationsContainer', result.medication);
                hasUnsavedChanges = true;
            }
        });
    }

    // ================================
    // ADD MEDICAL CONDITION — modal
    // ================================
    const addConditionBtn = document.getElementById('addConditionBtn');
    if (addConditionBtn) {
        addConditionBtn.addEventListener('click', async function () {
            const result = await showInputModal({
                title: 'Add Medical Condition',
                icon: 'ri-stethoscope-line',
                iconClass: 'icon-yellow',
                fields: [
                    { name: 'condition', label: 'Medical Condition', placeholder: 'e.g. Type 2 Diabetes, Hypertension', value: '' }
                ]
            });
            if (result && result.condition) {
                addTag('conditionsContainer', result.condition, 'yellow');
                hasUnsavedChanges = true;
            }
        });
    }

    // ================================
    // ADD CONTACT — modal
    // ================================
    const addContactBtn = document.getElementById('addContactBtn');
    if (addContactBtn) {
        addContactBtn.addEventListener('click', async function () {
            const result = await showInputModal({
                title: 'Add Emergency Contact',
                icon: 'ri-user-add-line',
                iconClass: 'icon-teal',
                description: 'This contact will be alerted in case of an emergency.',
                fields: [
                    { name: 'name',     label: 'Full Name',   placeholder: 'e.g. Maria Santos', value: '' },
                    { name: 'relation', label: 'Relation',    placeholder: 'e.g. Mother, Spouse, Friend', value: '' },
                    { name: 'phone',    label: 'Phone Number', placeholder: 'e.g. 0912 345 6789', value: '' }
                ]
            });
            if (result && result.name && result.relation && result.phone) {
                addContact(result.name, result.relation, result.phone);
                hasUnsavedChanges = true;
            } else if (result) {
                showAlertModal({
                    title: 'Incomplete Details',
                    message: 'Please fill in all fields: name, relation, and phone number.',
                    icon: 'ri-alert-line',
                    iconClass: 'icon-orange'
                });
            }
        });
    }

    // ================================
    // TAG / ITEM / CONTACT REMOVAL — modal confirms
    // ================================
    document.addEventListener('click', async function (e) {
        if (e.target.closest('.tag-remove')) {
            const tag = e.target.closest('.tag');
            const label = tag?.querySelector('span')?.textContent || 'this item';
            const confirmed = await showConfirmModal({
                title: 'Remove Item',
                message: `Remove <strong>${label}</strong> from the list?`,
                icon: 'ri-close-circle-line',
                iconClass: 'icon-red',
                confirmLabel: 'Remove',
                confirmClass: 'modal-btn-danger'
            });
            if (confirmed && tag) {
                tag.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => tag.remove(), 300);
                hasUnsavedChanges = true;
            }
        }

        if (e.target.closest('.item-remove')) {
            const item = e.target.closest('.medication-item');
            const label = item?.querySelector('span')?.textContent || 'this medication';
            const confirmed = await showConfirmModal({
                title: 'Remove Medication',
                message: `Remove <strong>${label}</strong> from your medication list?`,
                icon: 'ri-capsule-line',
                iconClass: 'icon-red',
                confirmLabel: 'Remove',
                confirmClass: 'modal-btn-danger'
            });
            if (confirmed && item) {
                item.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => item.remove(), 300);
                hasUnsavedChanges = true;
            }
        }

        if (e.target.closest('.action-btn.delete')) {
            const card = e.target.closest('.contact-card');
            const name = card?.querySelector('h4')?.textContent || 'this contact';
            const confirmed = await showConfirmModal({
                title: 'Remove Contact',
                message: `Remove <strong>${name}</strong> from your emergency contacts?`,
                icon: 'ri-user-unfollow-line',
                iconClass: 'icon-red',
                confirmLabel: 'Remove',
                confirmClass: 'modal-btn-danger',
                warning: 'This contact will no longer receive emergency alerts.'
            });
            if (confirmed && card) {
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

    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', async function (e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                const href = this.href;
                const confirmed = await showConfirmModal({
                    title: 'Unsaved Changes',
                    message: 'You have unsaved changes. Leaving this page will discard them.',
                    icon: 'ri-edit-line',
                    iconClass: 'icon-orange',
                    confirmLabel: 'Leave Page',
                    confirmClass: 'modal-btn-danger',
                    warning: 'All unsaved changes will be permanently lost.'
                });
                if (confirmed) window.location.href = href;
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
        if (!isEditing) tag.querySelector('.tag-remove').style.display = 'none';
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
        if (!isEditing) item.querySelector('.item-remove').style.display = 'none';
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
            <div class="contact-avatar" style="background: ${randomColor};">${initials}</div>
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
        if (!isEditing) card.querySelector('.action-btn.delete').style.display = 'none';
        container.appendChild(card);
        showNotification('Contact added successfully!', 'success');
    }

    function collectFormData() {
        const data = {};

        document.querySelectorAll('.form-control[name]').forEach(input => {
            const name = input.getAttribute('name');
            data[name] = input.value;
        });

        // Disability is always Deaf/Mute
        data.disabilityType = 'Deaf/Mute';

        const bloodTypeSelect = document.getElementById('bloodTypeSelect');
        if (bloodTypeSelect) {
            data.bloodType = bloodTypeSelect.value;
        } else {
            const bloodTypeDisplay = document.querySelector('.blood-type-value');
            if (bloodTypeDisplay) data.bloodType = bloodTypeDisplay.textContent.trim();
        }

        const smsTemplate = document.getElementById('smsTemplate');
        if (smsTemplate) data.smsTemplate = smsTemplate.value;

        data.allergies = [];
        document.querySelectorAll('#allergiesContainer .tag span').forEach(tag => {
            data.allergies.push(tag.textContent);
        });

        data.medications = [];
        document.querySelectorAll('#medicationsContainer .medication-item span').forEach(item => {
            data.medications.push(item.textContent);
        });

        data.medicalConditions = [];
        document.querySelectorAll('#conditionsContainer .tag span').forEach(tag => {
            data.medicalConditions.push(tag.textContent);
        });

        data.emergencyContacts = [];
        document.querySelectorAll('.contact-card').forEach(card => {
            data.emergencyContacts.push({
                name:     card.querySelector('h4').textContent,
                relation: card.querySelector('.contact-relation').textContent,
                phone:    card.querySelector('.contact-phone').textContent,
                initials: card.querySelector('.contact-avatar').textContent.trim(),
                color:    card.querySelector('.contact-avatar').style.background
            });
        });

        data.medicationReminders = [];
        document.querySelectorAll('.reminder-card').forEach(card => {
            // Read from the edit input/select (source of truth), fall back to display element
            const nameInput   = card.querySelector('.reminder-name-edit');
            const nameDisplay = card.querySelector('.reminder-name-display');
            const freqSelect  = card.querySelector('.reminder-frequency-edit');
            const freqDisplay = card.querySelector('.reminder-frequency-display');
            const timeEl      = card.querySelector('.reminder-time');
            const iconEl      = card.querySelector('.reminder-icon');

            const name      = (nameInput && nameInput.value.trim())  ? nameInput.value.trim()  : (nameDisplay ? nameDisplay.textContent.trim() : '');
            const frequency = (freqSelect && freqSelect.value)        ? freqSelect.value        : (freqDisplay ? freqDisplay.textContent.trim() : '');

            data.medicationReminders.push({
                name,
                frequency,
                time:  timeEl ? timeEl.textContent.replace(/[^\d:APMapm ,]/g, '').trim() : '',
                color: iconEl ? iconEl.style.background : ''
            });
        });

        return data;
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        let icon = 'checkbox-circle';
        let color = '#4caf50';
        if (type === 'error') { icon = 'error-warning'; color = '#f44336'; }
        else if (type === 'info') { icon = 'information'; color = '#2196f3'; }

        notification.innerHTML = `<i class="ri-${icon}-fill"></i><span>${message}</span>`;
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
            z-index: 100001;
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
            to   { opacity: 0; transform: translateX(20px); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(100px); }
            to   { opacity: 1; transform: translateX(0); }
        }
    `;
    document.head.appendChild(style);

    // Initialize
    init();
});