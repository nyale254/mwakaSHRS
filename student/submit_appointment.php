<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
    exit();
}
$user_id = $_SESSION['user_id'];

$res = mysqli_query($conn, "SELECT student_id FROM students WHERE user_id = $user_id");
$student = mysqli_fetch_assoc($res);

if (!$student) {
    echo json_encode(['success'=>false,'message'=>'No student record found']);
    exit();
}

$student_id = $student['student_id'];
$appointment_date = $_POST['appointment_date'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if(!$appointment_date || !$reason){
    echo json_encode(['success'=>false,'message'=>'All fields are required']);
    exit();
}

$stmt = mysqli_prepare($conn, "INSERT INTO appointments (student_id, appointment_date, reason) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iss", $student_id, $appointment_date, $reason);
if(mysqli_stmt_execute($stmt)){
    $appointment_id = mysqli_insert_id($conn);

    $nurses = mysqli_query($conn, "SELECT user_id FROM users WHERE role='nurse'");
    while($nurse = mysqli_fetch_assoc($nurses)){
        $msg = "New appointment request from student ID $student_id.";
        $notify = mysqli_prepare($conn, "INSERT INTO notifications (user_id, type, message) VALUES (?, 'appointment', ?)");
        mysqli_stmt_bind_param($notify, "is", $nurse['user_id'], $msg);
        mysqli_stmt_execute($notify);
        mysqli_stmt_close($notify);
    }
    echo json_encode(['success'=>true,'message'=>'Appointment requested successfully']);
}else {
    echo json_encode([
        'success'=>false,
        'message'=>'Error booking appointment: ' . mysqli_error($conn)
    ]);
}
mysqli_stmt_close($stmt);
?>
