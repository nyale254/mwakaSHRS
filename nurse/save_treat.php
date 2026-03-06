<?php
session_start();
include "../connect.php";

$user_id = $_SESSION['user_id'] ?? null;

$student_id = $_POST['student_id'];
$symptoms = $_POST['symptoms'];
$referral = $_POST['referral'];
$medications = $_POST['medication'];
$dosage = $_POST['prescribed_dosage'];
$frequency = $_POST['frequency'];


$query = "INSERT INTO treatments(student_id,symptoms,referral,treatment_date)
VALUES('$student_id','$symptoms','$referral',NOW())";

mysqli_query($conn,$query);
$treatment_id = mysqli_insert_id($conn);

for($i=0;$i<count($medications);$i++){
    mysqli_query($conn,"INSERT INTO medication_schedule(
        medication_id, medication, prescribed_dosage, frequency
    ) VALUES(
        '$treatment_id',
        '".mysqli_real_escape_string($conn,$medications[$i])."',
        '".mysqli_real_escape_string($conn,$dosage[$i])."',
        '".mysqli_real_escape_string($conn,$frequency[$i])."'
    )");
}


$visit_date = date('Y-m-d');
$visit_time = date('H:i:s');
$severity = ""; 
$diagnosis = ""; 
$notes = ""; 
$status = "pending";

$health_query = "INSERT INTO health_records(
    student_id,
    attended_by,
    visit_date,
    visit_time,
    complain,
    severity,
    diagnosis,
    notes,
    status,
    created_at
) VALUES(
    '$student_id',
    '$user_id',
    '$visit_date',
    '$visit_time',
    '".mysqli_real_escape_string($conn,$symptoms)."',
    '$severity',
    '$diagnosis',
    '$notes',
    '$status',
    NOW()
)";
mysqli_query($conn,$health_query);

$action = "Save Treatment";
$detail = "Saved treatment for student ID: $student_id with treatment ID: $treatment_id";
$ip_address = $_SERVER['REMOTE_ADDR'];

$log_query = "INSERT INTO activity_log(user_id, action, detail, ip_address, created_at)
VALUES(
    '$user_id',
    '".mysqli_real_escape_string($conn,$action)."',
    '".mysqli_real_escape_string($conn,$detail)."',
    '$ip_address',
    NOW()
)";
mysqli_query($conn,$log_query);

echo "Saved Successfully";

?>