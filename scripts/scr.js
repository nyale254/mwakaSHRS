// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to current tab
            this.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        });
    });
    
    // Modal functionality
    const modal = document.getElementById('recordModal');
    if (modal) {
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });
    }
});

// Modal functions
function openModal() {
    const modal = document.getElementById('recordModal');
    modal.classList.add('show');
}

function closeModal() {
    const modal = document.getElementById('recordModal');
    modal.classList.remove('show');
    document.getElementById('recordForm').reset();
}

// Add record
function addRecord(type, studentId) {
    const modalTitle = document.getElementById('modalTitle');
    const formFields = document.getElementById('formFields');
    
    modalTitle.textContent = 'Add ' + type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('recordType').value = type;
    document.getElementById('recordId').value = '';
    
    // Generate form fields based on type
    let fields = '';
    
    switch(type) {
        case 'allergy':
            fields = `
                <div class="form-group">
                    <label>Allergy Type</label>
                    <select name="allergy_type" required>
                        <option value="Food">Food</option>
                        <option value="Medication">Medication</option>
                        <option value="Environmental">Environmental</option>
                        <option value="Insect">Insect</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Allergen</label>
                    <input type="text" name="allergen" required>
                </div>
                <div class="form-group">
                    <label>Severity</label>
                    <select name="severity" required>
                        <option value="Mild">Mild</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Severe">Severe</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Reaction</label>
                    <textarea name="reaction" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
            `;
            break;
            
        case 'condition':
            fields = `
                <div class="form-group">
                    <label>Condition Name</label>
                    <input type="text" name="condition_name" required>
                </div>
                <div class="form-group">
                    <label>Diagnosis Date</label>
                    <input type="date" name="diagnosis_date">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="Active">Active</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Ongoing">Ongoing</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
            `;
            break;
            
        case 'medication':
            fields = `
                <div class="form-group">
                    <label>Medication Name</label>
                    <input type="text" name="medication_name" required>
                </div>
                <div class="form-group">
                    <label>Dosage</label>
                    <input type="text" name="dosage" placeholder="e.g., 10mg">
                </div>
                <div class="form-group">
                    <label>Frequency</label>
                    <input type="text" name="frequency" placeholder="e.g., Twice daily">
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date">
                </div>
                <div class="form-group">
                    <label>Prescribing Physician</label>
                    <input type="text" name="prescribing_physician">
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2"></textarea>
                </div>
            `;
            break;
            
        case 'immunization':
            fields = `
                <div class="form-group">
                    <label>Vaccine Name</label>
                    <input type="text" name="vaccine_name" required>
                </div>
                <div class="form-group">
                    <label>Date Administered</label>
                    <input type="date" name="date_administered" required>
                </div>
                <div class="form-group">
                    <label>Administered By</label>
                    <input type="text" name="administered_by">
                </div>
                <div class="form-group">
                    <label>Lot Number</label>
                    <input type="text" name="lot_number">
                </div>
                <div class="form-group">
                    <label>Next Due Date</label>
                    <input type="date" name="next_due_date">
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2"></textarea>
                </div>
            `;
            break;
    }
    
    formFields.innerHTML = fields;
    openModal();
}

// Edit record
function editRecord(type, id) {
    // In a real application, you would fetch the record data via AJAX
    // and populate the form fields
    console.log('Edit', type, id);
    addRecord(type, document.getElementById('studentId').value);
    document.getElementById('recordId').value = id;
    
    // Simulate loading data
    setTimeout(() => {
        // Populate fields with actual data from server
        alert('In production, this would load the record data via AJAX');
    }, 100);
}

// Delete record
function deleteRecord(type, id) {
    if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
        // In a real application, you would send an AJAX request to delete the record
        console.log('Delete', type, id);
        alert('Record deleted successfully');
        location.reload();
    }
}

// Save record
function saveRecord() {
    const form = document.getElementById('recordForm');
    const formData = new FormData(form);
    
    // In a real application, you would send this data via AJAX
    console.log('Saving record:', Object.fromEntries(formData));
    
    // Show success message
    alert('Record saved successfully');
    closeModal();
    location.reload();
}

// Search functionality
function searchStudents() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        const query = searchInput.value;
        if (query.length > 2) {
            // In production, implement AJAX search
            console.log('Searching for:', query);
        }
    }
}

// Initialize tooltips and other UI elements
document.addEventListener('DOMContentLoaded', function() {
    // Add any additional initialization here
    console.log('Student Health Record System initialized');
});