<?php

include '../connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header("Location: ../index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

$student_id = $_SESSION['user_id'];
$student_query = "SELECT * FROM users WHERE student_id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();

if (!$student) {
    $_SESSION['error_message'] = "Student not found.";
    header("Location: students.php");
    exit();
}

$allergies_query = "SELECT * FROM conditions_allergies WHERE student_id = ? ORDER BY severity";
$allergies_stmt = $conn->prepare($allergies_query);
$allergies_stmt->bind_param("i", $student_id);
$allergies_stmt->execute();
$allergies_result = $allergies_stmt->get_result();

// Fetch treatments/conditions
$conditions_query = "SELECT * FROM treatments WHERE student_id = ? ORDER BY treatment_date DESC";
$conditions_stmt = $conn->prepare($conditions_query);
$conditions_stmt->bind_param("i", $student_id);
$conditions_stmt->execute();
$conditions_result = $conditions_stmt->get_result();

// Fetch medications
$medications_query = "SELECT * FROM medications WHERE student_id = ? ORDER BY start_date DESC";
$medications_stmt = $conn->prepare($medications_query);
$medications_stmt->bind_param("i", $student_id);
$medications_stmt->execute();
$medications_result = $medications_stmt->get_result();

// Fetch immunizations
$immunizations_query = "SELECT * FROM immunizations WHERE student_id = ? ORDER BY date_administered DESC";
$immunizations_stmt = $conn->prepare($immunizations_query);
$immunizations_stmt->bind_param("i", $student_id);
$immunizations_stmt->execute();
$immunizations_result = $immunizations_stmt->get_result();

// Fetch visits
$visits_query = "SELECT * FROM visits WHERE student_id = ? ORDER BY visit_date DESC, visit_time DESC LIMIT 10";
$visits_stmt = $conn->prepare($visits_query);
$visits_stmt->bind_param("i", $student_id);
$visits_stmt->execute();
$visits_result = $visits_stmt->get_result();

// Get nurse/navigator name for display
$nurse_name = $_SESSION['full_name'] ?? 'Nurse';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Health Record - <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/xamp.css">
    <style>
        /* Additional styles for nurse-specific interface */
        .nurse-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: var(--radius-lg);
            margin-bottom: 1rem;
        }
        
        .nurse-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .nurse-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .quick-actions {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }
        
        .nurse-action-btn {
            background: white;
            color: var(--primary-700);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nurse-action-btn:hover {
            background: var(--primary-50);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .emergency-alert {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            animation: pulse 2s infinite;
        }
        
        .permission-note {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-left: 0.5rem;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Nurse Header -->
    <div class="nurse-header">
        <div class="nurse-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8 10a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
            <span>Logged in as: <strong><?php echo htmlspecialchars($nurse_name); ?></strong></span>
            <span class="nurse-badge">Nurse</span>
        </div>
        <div class="quick-actions">
            <button class="nurse-action-btn" onclick="quickAssessment(<?php echo $student_id; ?>)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                Quick Assessment
            </button>
            <button class="nurse-action-btn" onclick="window.location.href='messaging.php?student=<?php echo $student_id; ?>'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                Contact Parent
            </button>
        </div>
    </div>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <a href="students.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Students
                </a>
                <h1>Student Health Record</h1>
            </div>
            <div class="header-actions">
                <button class="btn-primary" onclick="window.print()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <path d="M6 9V3h12v6"></path>
                        <rect x="6" y="15" width="12" height="6" rx="2"></rect>
                    </svg>
                    Print Record
                </button>
                <button class="btn-secondary" onclick="exportHealthRecord(<?php echo $student_id; ?>)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export
                </button>
            </div>
        </header>
        
        <!-- Student Header Card -->
        <div class="student-header-card">
            <div class="student-avatar">
                <?php if (!empty($student['profile_image'])): ?>
                    <img src="<?php echo htmlspecialchars($student['profile_image']); ?>" alt="Profile">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="student-info">
                <h2><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                <div class="student-meta">
                    <span class="meta-item">
                        <strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?>
                    </span>
                    <span class="meta-item">
                        <strong>Grade:</strong> <?php echo htmlspecialchars($student['grade'] ?? 'N/A'); ?>
                    </span>
                    <span class="meta-item">
                        <strong>Class:</strong> <?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?>
                    </span>
                    <span class="meta-item">
                        <strong>DOB:</strong> <?php echo $student['date_of_birth'] ? date('M d, Y', strtotime($student['date_of_birth'])) : 'N/A'; ?>
                    </span>
                    <span class="meta-item">
                        <strong>Blood Type:</strong> 
                        <span class="blood-type"><?php echo htmlspecialchars($student['blood_type'] ?? 'N/A'); ?></span>
                    </span>
                </div>
            </div>
            <div class="student-alerts">
                <?php 
                // Reset pointer and check for severe allergies
                $allergies_stmt->execute();
                $allergies_result = $allergies_stmt->get_result();
                $has_severe_allergy = false;
                while ($allergy = $allergies_result->fetch_assoc()) {
                    if (isset($allergy['severity']) && $allergy['severity'] === 'Severe') {
                        $has_severe_allergy = true;
                        break;
                    }
                }
                // Reset for display
                $allergies_stmt->execute();
                $allergies_result = $allergies_stmt->get_result();
                ?>
                <?php if ($has_severe_allergy): ?>
                    <div class="alert-badge alert-danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <circle cx="12" cy="16" r="1" fill="currentColor"></circle>
                        </svg>
                        Severe Allergies
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Emergency Contact Card -->
        <div class="emergency-card">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8 10a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                Emergency Contact
            </h3>
            <div class="emergency-grid">
                <div class="emergency-item">
                    <span class="label">Name:</span>
                    <span class="value"><?php echo htmlspecialchars($student['emergency_contact_name'] ?? 'Not provided'); ?></span>
                </div>
                <div class="emergency-item">
                    <span class="label">Relationship:</span>
                    <span class="value"><?php echo htmlspecialchars($student['emergency_contact_relationship'] ?? 'Not provided'); ?></span>
                </div>
                <div class="emergency-item">
                    <span class="label">Phone:</span>
                    <span class="value"><?php echo htmlspecialchars($student['emergency_contact_phone'] ?? 'Not provided'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Medical Information Tabs -->
        <div class="tabs-container">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="allergies">Allergies</button>
                <button class="tab-btn" data-tab="conditions">Medical Conditions</button>
                <button class="tab-btn" data-tab="medications">Medications</button>
                <button class="tab-btn" data-tab="immunizations">Immunizations</button>
                <button class="tab-btn" data-tab="visits">Visit History</button>
            </div>
            
            <!-- Allergies Tab -->
            <div class="tab-content active" id="tab-allergies">
                <div class="card">
                    <div class="card-header">
                        <h2>Allergies <span class="permission-note">(Nurse can add/edit)</span></h2>
                        <button class="btn-small" onclick="addRecord('allergy', <?php echo $student_id; ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Allergy
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Allergen</th>
                                    <th>Severity</th>
                                    <th>Reaction</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($allergies_result->num_rows > 0): ?>
                                    <?php while ($allergy = $allergies_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($allergy['allergy_type'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($allergy['allergen'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo ($allergy['severity'] ?? 'Mild') === 'Severe' ? 'danger' : 
                                                    (($allergy['severity'] ?? 'Mild') === 'Moderate' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo htmlspecialchars($allergy['severity'] ?? 'Mild'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($allergy['reaction'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($allergy['notes'] ?? ''); ?></td>
                                        <td>
                                            <button class="icon-btn" onclick="editRecord('allergy', <?php echo $allergy['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                </svg>
                                            </button>
                                            <button class="icon-btn text-danger" onclick="deleteRecord('allergy', <?php echo $allergy['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--gray-500);">
                                            No allergy records found. Click "Add Allergy" to add one.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Conditions Tab -->
            <div class="tab-content" id="tab-conditions">
                <div class="card">
                    <div class="card-header">
                        <h2>Medical Conditions <span class="permission-note">(Nurse can add/edit)</span></h2>
                        <button class="btn-small" onclick="addRecord('condition', <?php echo $student_id; ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Condition
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Condition</th>
                                    <th>Diagnosis Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($conditions_result->num_rows > 0): ?>
                                    <?php while ($condition = $conditions_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($condition['treatment_name'] ?? $condition['condition_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo isset($condition['diagnosis_date']) && $condition['diagnosis_date'] ? date('M d, Y', strtotime($condition['diagnosis_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo ($condition['status'] ?? 'Active') === 'Active' ? 'warning' : 
                                                    (($condition['status'] ?? '') === 'Resolved' ? 'success' : 'info'); 
                                            ?>">
                                                <?php echo htmlspecialchars($condition['status'] ?? 'Active'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($condition['notes'] ?? ''); ?></td>
                                        <td>
                                            <button class="icon-btn" onclick="editRecord('condition', <?php echo $condition['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                </svg>
                                            </button>
                                            <button class="icon-btn text-danger" onclick="deleteRecord('condition', <?php echo $condition['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--gray-500);">
                                            No medical condition records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Medications Tab -->
            <div class="tab-content" id="tab-medications">
                <div class="card">
                    <div class="card-header">
                        <h2>Current Medications <span class="permission-note">(Nurse can add/edit)</span></h2>
                        <button class="btn-small" onclick="addRecord('medication', <?php echo $student_id; ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Medication
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Prescribing Physician</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($medications_result->num_rows > 0): ?>
                                    <?php while ($medication = $medications_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($medication['medication_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($medication['dosage'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($medication['frequency'] ?? 'N/A'); ?></td>
                                        <td><?php echo isset($medication['start_date']) && $medication['start_date'] ? date('M d, Y', strtotime($medication['start_date'])) : 'N/A'; ?></td>
                                        <td><?php echo isset($medication['end_date']) && $medication['end_date'] ? date('M d, Y', strtotime($medication['end_date'])) : 'Ongoing'; ?></td>
                                        <td><?php echo htmlspecialchars($medication['prescribing_physician'] ?? 'N/A'); ?></td>
                                        <td>
                                            <button class="icon-btn" onclick="editRecord('medication', <?php echo $medication['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                </svg>
                                            </button>
                                            <button class="icon-btn text-danger" onclick="deleteRecord('medication', <?php echo $medication['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; color: var(--gray-500);">
                                            No medication records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Immunizations Tab -->
            <div class="tab-content" id="tab-immunizations">
                <div class="card">
                    <div class="card-header">
                        <h2>Immunization Record <span class="permission-note">(Nurse can add/edit)</span></h2>
                        <button class="btn-small" onclick="addRecord('immunization', <?php echo $student_id; ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Immunization
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Vaccine</th>
                                    <th>Date Administered</th>
                                    <th>Administered By</th>
                                    <th>Lot Number</th>
                                    <th>Next Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($immunizations_result->num_rows > 0): ?>
                                    <?php while ($immunization = $immunizations_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($immunization['vaccine_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo isset($immunization['date_administered']) ? date('M d, Y', strtotime($immunization['date_administered'])) : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($immunization['administered_by'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($immunization['lot_number'] ?? 'N/A'); ?></td>
                                        <td><?php echo isset($immunization['next_due_date']) && $immunization['next_due_date'] ? date('M d, Y', strtotime($immunization['next_due_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <button class="icon-btn" onclick="editRecord('immunization', <?php echo $immunization['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                </svg>
                                            </button>
                                            <button class="icon-btn text-danger" onclick="deleteRecord('immunization', <?php echo $immunization['id']; ?>)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--gray-500);">
                                            No immunization records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Visits Tab -->
            <div class="tab-content" id="tab-visits">
                <div class="card">
                    <div class="card-header">
                        <h2>Visit History</h2>
                        <div>
                            <button class="btn-small" onclick="addRecord('visit', <?php echo $student_id; ?>)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                New Visit
                            </button>
                            <button class="btn-small" style="margin-left: 0.5rem;" onclick="window.location.href='visits.php?student=<?php echo $student_id; ?>'">
                                View All
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Reason</th>
                                    <th>Temperature</th>
                                    <th>Treatment</th>
                                    <th>Outcome</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($visits_result->num_rows > 0): ?>
                                    <?php while ($visit = $visits_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo isset($visit['visit_date']) ? date('M d, Y', strtotime($visit['visit_date'])) : 'N/A'; ?></td>
                                        <td><?php echo isset($visit['visit_time']) ? date('g:i A', strtotime($visit['visit_time'])) : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($visit['reason'] ?? 'N/A'); ?></td>
                                        <td><?php echo isset($visit['temperature']) ? $visit['temperature'] . '°F' : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars(substr($visit['treatment_provided'] ?? '', 0, 50)) . (strlen($visit['treatment_provided'] ?? '') > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                $outcome = $visit['outcome'] ?? 'Returned to Class';
                                                echo $outcome === 'Returned to Class' ? 'success' : 
                                                    ($outcome === 'Sent Home' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo htmlspecialchars($outcome); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--gray-500);">
                                            No visit records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Modal -->
    <div id="recordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Record</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="recordForm" onsubmit="event.preventDefault(); saveRecord();">
                    <input type="hidden" id="recordId" name="id">
                    <input type="hidden" id="recordType" name="type">
                    <input type="hidden" id="studentId" name="student_id" value="<?php echo $student_id; ?>">
                    <input type="hidden" id="nurseId" name="nurse_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <div id="formFields"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn-primary" onclick="saveRecord()">Save Record</button>
            </div>
        </div>
    </div>
    
    <!-- Quick Assessment Modal -->
    <div id="assessmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Quick Health Assessment</h3>
                <button class="close-btn" onclick="closeAssessmentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="assessmentForm" onsubmit="event.preventDefault(); submitAssessment();">
                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                    <input type="hidden" name="nurse_id" value="<?php echo $_SESSION['user_id']; ?>">
                    
                    <div class="form-group">
                        <label>Assessment Date & Time</label>
                        <input type="datetime-local" name="assessment_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Chief Complaint</label>
                        <input type="text" name="complaint" placeholder="e.g., Headache, Fever, Injury" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Symptoms</label>
                        <textarea name="symptoms" rows="3" placeholder="Describe symptoms in detail"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Vital Signs</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                            <input type="text" name="temperature" placeholder="Temperature (°F)">
                            <input type="text" name="blood_pressure" placeholder="Blood Pressure">
                            <input type="text" name="heart_rate" placeholder="Heart Rate (bpm)">
                            <input type="text" name="respiratory_rate" placeholder="Respiratory Rate">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Preliminary Assessment</label>
                        <textarea name="assessment" rows="3" placeholder="Your assessment findings"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Action Taken</label>
                        <select name="action_taken" required>
                            <option value="">Select action</option>
                            <option value="Returned to Class">Returned to Class</option>
                            <option value="Sent Home">Sent Home</option>
                            <option value="Referred to Doctor">Referred to Doctor</option>
                            <option value="Emergency Services">Emergency Services</option>
                            <option value="Observation">Kept for Observation</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Follow-up Required?</label>
                        <div style="display: flex; gap: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.25rem;">
                                <input type="radio" name="follow_up" value="1"> Yes
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.25rem;">
                                <input type="radio" name="follow_up" value="0" checked> No
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea name="notes" rows="2" placeholder="Any additional notes"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeAssessmentModal()">Cancel</button>
                <button class="btn-primary" onclick="submitAssessment()">Complete Assessment</button>
            </div>
        </div>
    </div>
    
    <script src="/Mwaka.SHRS.2/scripts/scr.js"></script>
    <script>
        // Nurse-specific JavaScript functions
        
        // Quick assessment function
        function quickAssessment(studentId) {
            document.getElementById('assessmentModal').classList.add('show');
        }
        
        function closeAssessmentModal() {
            document.getElementById('assessmentModal').classList.remove('show');
            document.getElementById('assessmentForm').reset();
        }
        
        function submitAssessment() {
            const form = document.getElementById('assessmentForm');
            const formData = new FormData(form);
            
            // Show loading state
            const submitBtn = document.querySelector('#assessmentModal .btn-primary');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            // Send data via AJAX
            fetch('save_assessment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Assessment saved successfully!');
                    closeAssessmentModal();
                    location.reload(); // Refresh to show new visit
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        }
        
        // Export health record
        function exportHealthRecord(studentId) {
            window.location.href = 'export_record.php?student_id=' + studentId;
        }
        
        // Contact parent
        function contactParent(studentId, phone) {
            if (confirm('Call emergency contact?')) {
                window.location.href = 'tel:' + phone;
            }
        }
        
        // Add this to your existing JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Add nurse identifier to all AJAX requests
            const nurseId = <?php echo $_SESSION['user_id']; ?>;
            
            // Override saveRecord to include nurse info
            window.saveRecord = function() {
                const form = document.getElementById('recordForm');
                const formData = new FormData(form);
                formData.append('nurse_id', nurseId);
                formData.append('action', 'save');
                
                // Show loading
                const saveBtn = document.querySelector('#recordModal .btn-primary');
                const originalText = saveBtn.textContent;
                saveBtn.textContent = 'Saving...';
                saveBtn.disabled = true;
                
                fetch('save_health_record.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Record saved successfully!');
                        closeModal();
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving.');
                })
                .finally(() => {
                    saveBtn.textContent = originalText;
                    saveBtn.disabled = false;
                });
            };
            
            // Override deleteRecord
            window.deleteRecord = function(type, id) {
                if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                    const nurseId = <?php echo $_SESSION['user_id']; ?>;
                    
                    fetch('delete_health_record.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            type: type,
                            id: id,
                            nurse_id: nurseId,
                            student_id: <?php echo $student_id; ?>
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Record deleted successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting.');
                    });
                }
            };
        });
    </script>
</body>
</html>