<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    echo "<script>
            alert('Access denied. Nurse only.');
            window.location.href='../index.php';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student_list.php");
    exit();
}

if (!isset($_POST['student_id']) || empty($_POST['student_id']) || 
    !isset($_POST['diagnosis']) || empty(trim($_POST['diagnosis']))) {
    echo "<script>
            alert('Missing required fields.');
            window.location.href='treatment.php?id=' + " . intval($_POST['student_id'] ?? 0) . ";
          </script>";
    exit();
}

$student_id = intval($_POST['student_id']);
$diagnosis = trim($_POST['diagnosis']);
$medication = isset($_POST['medication']) ? trim($_POST['medication']) : '';
$dosage = isset($_POST['dosage']) ? trim($_POST['dosage']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
$complaint = isset($_POST['complain']) ? trim($_POST['complain']) : '';

if (empty($category)) {
    echo "<script>
            alert('Please select a category.');
            window.location.href='treatment.php?id=$student_id';
          </script>";
    exit();
}

$checkStmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($checkStmt, "i", $student_id);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    echo "<script>
            alert('Student not found.');
            window.location.href='student_list.php';
          </script>";
    exit();
}

mysqli_begin_transaction($conn);

try {
    $insertTreatStmt = mysqli_prepare($conn, 
        "INSERT INTO treatments (student_id, diagnosis, medication, dosage, category, notes, created_at) 
         VALUES (?, ?, ?, ?, ?, ?, NOW())"
    );
    
    $created_by = $_SESSION['user_id'];
    mysqli_stmt_bind_param($insertTreatStmt, "isssss", 
        $student_id, $diagnosis, $medication, $dosage, $category, $notes
    );
    
    if (!mysqli_stmt_execute($insertTreatStmt)) {
        throw new Exception("Failed to insert into treatments: " . mysqli_error($conn));
    }
    
    $treatment_id = mysqli_insert_id($conn);
    
    $insertMedicalStmt = mysqli_prepare($conn,
        "INSERT INTO medical_records (student_id, complain, notes, diagnosis, last_updated) 
         VALUES (?, ?, ?, ?, NOW())"
    );
    
    mysqli_stmt_bind_param($insertMedicalStmt, "isss", 
        $student_id, $complaint, $notes, $diagnosis
    );
    
    if (!mysqli_stmt_execute($insertMedicalStmt)) {
        throw new Exception("Failed to insert into medical_records: " . mysqli_error($conn));
    }
    
    $medical_record_id = mysqli_insert_id($conn);
    
  
    $treatment_text = $medication;
    if (!empty($dosage)) {
        $treatment_text .= " - " . $dosage;
    }
    if (empty($treatment_text)) {
        $treatment_text = $category;
    }
    
    $insertVisitStmt = mysqli_prepare($conn,
        "INSERT INTO visits (student_id, complain, diagnosis, treatment, visit_date) 
         VALUES (?, ?, ?, ?, NOW())"
    );
    
    mysqli_stmt_bind_param($insertVisitStmt, "isss", 
        $student_id, $complaint, $diagnosis, $treatment_text
    );
    
    if (!mysqli_stmt_execute($insertVisitStmt)) {
        throw new Exception("Failed to insert into visits: " . mysqli_error($conn));
    }
    
    $visit_id = mysqli_insert_id($conn);
    
    $logStmt = mysqli_prepare($conn, 
        "INSERT INTO activity_log (user_id, action, details, ip_address) 
         VALUES (?, 'add_treatment', ?, ?)"
    );
    $details = "Added treatment #$treatment_id, medical record #$medical_record_id, visit #$visit_id for student ID: $student_id";
    $ip = $_SERVER['REMOTE_ADDR'];
    mysqli_stmt_bind_param($logStmt, "iss", $_SESSION['user_id'], $details, $ip);
    mysqli_stmt_execute($logStmt);
    
    mysqli_commit($conn);
    
    echo "<script>
            alert('Treatment record saved successfully! Medical records and visit history updated.');
            window.location.href='treatment.php?id=$student_id';
          </script>";
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    error_log("Failed to save records: " . $e->getMessage());
    echo "<script>
            alert('Error saving records. Please try again.');
            window.location.href='treatment.php?id=$student_id';
          </script>";
}

if (isset($insertTreatStmt)) mysqli_stmt_close($insertTreatStmt);
if (isset($insertMedicalStmt)) mysqli_stmt_close($insertMedicalStmt);
if (isset($insertVisitStmt)) mysqli_stmt_close($insertVisitStmt);
if (isset($logStmt)) mysqli_stmt_close($logStmt);

mysqli_close($conn);
?>