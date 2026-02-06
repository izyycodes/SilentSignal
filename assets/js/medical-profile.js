// Medical Profile & Pre-Registration JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // TAB NAVIGATION
    // ================================
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
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
    // SAVE CHANGES BUTTON
    // ================================
    const saveBtn = document.getElementById('saveChangesBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            // Collect form data
            const formData = collectFormData();
            
            // Show saving state
            this.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';
            this.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                this.innerHTML = '<i class="ri-check-line"></i> Saved!';
                this.style.background = 'linear-gradient(135deg, #4caf50 0%, #388e3c 100%)';
                
                showNotification('Changes saved successfully!', 'success');
                
                // Reset button after delay
                setTimeout(() => {
                    this.innerHTML = '<i class="ri-save-line"></i> Save Changes';
                    this.style.background = '';
                    this.disabled = false;
                }, 2000);
            }, 1500);
        });
    }
    
    // ================================
    // ADD ALLERGY
    // ================================
    const addAllergyBtn = document.getElementById('addAllergyBtn');
    if (addAllergyBtn) {
        addAllergyBtn.addEventListener('click', function() {
            const allergy = prompt('Enter allergy:');
            if (allergy && allergy.trim()) {
                addTag('allergiesContainer', allergy.trim(), 'red');
            }
        });
    }
    
    // ================================
    // ADD MEDICATION
    // ================================
    const addMedicationBtn = document.getElementById('addMedicationBtn');
    if (addMedicationBtn) {
        addMedicationBtn.addEventListener('click', function() {
            const medication = prompt('Enter medication name and dosage:');
            if (medication && medication.trim()) {
                addMedication('medicationsContainer', medication.trim());
            }
        });
    }
    
    // ================================
    // ADD MEDICAL CONDITION
    // ================================
    const addConditionBtn = document.getElementById('addConditionBtn');
    if (addConditionBtn) {
        addConditionBtn.addEventListener('click', function() {
            const condition = prompt('Enter medical condition:');
            if (condition && condition.trim()) {
                addTag('conditionsContainer', condition.trim(), 'yellow');
            }
        });
    }
    
    // ================================
    // ADD CONTACT
    // ================================
    const addContactBtn = document.getElementById('addContactBtn');
    if (addContactBtn) {
        addContactBtn.addEventListener('click', function() {
            // In real app, this would open a modal
            const name = prompt('Enter contact name:');
            if (name && name.trim()) {
                const relation = prompt('Enter relation (e.g., Mother, Father, Spouse):');
                const phone = prompt('Enter phone number:');
                
                if (relation && phone) {
                    addContact(name.trim(), relation.trim(), phone.trim());
                }
            }
        });
    }
    
    // ================================
    // TAG REMOVAL EVENT DELEGATION
    // ================================
    document.addEventListener('click', function(e) {
        if (e.target.closest('.tag-remove')) {
            const tag = e.target.closest('.tag');
            if (tag) {
                tag.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => tag.remove(), 300);
            }
        }
        
        if (e.target.closest('.item-remove')) {
            const item = e.target.closest('.medication-item');
            if (item) {
                item.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => item.remove(), 300);
            }
        }
        
        if (e.target.closest('.action-btn.delete')) {
            const card = e.target.closest('.contact-card');
            if (card && confirm('Remove this contact?')) {
                card.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => card.remove(), 300);
            }
        }
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
        
        container.appendChild(card);
        showNotification('Contact added successfully!', 'success');
    }
    
    function collectFormData() {
        // Collect all form inputs
        const data = {
            personalInfo: {},
            allergies: [],
            medications: [],
            conditions: [],
            contacts: []
        };
        
        // Get all input values
        document.querySelectorAll('.form-control').forEach(input => {
            if (input.name) {
                data.personalInfo[input.name] = input.value;
            }
        });
        
        // Get allergies
        document.querySelectorAll('#allergiesContainer .tag span').forEach(tag => {
            data.allergies.push(tag.textContent);
        });
        
        // Get medications
        document.querySelectorAll('#medicationsContainer .medication-item span').forEach(item => {
            data.medications.push(item.textContent);
        });
        
        // Get conditions
        document.querySelectorAll('#conditionsContainer .tag span').forEach(tag => {
            data.conditions.push(tag.textContent);
        });
        
        return data;
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <i class="ri-${type === 'success' ? 'checkbox-circle' : 'error-warning'}-fill"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4caf50' : '#f44336'};
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
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
    `;
    document.head.appendChild(style);
    
});