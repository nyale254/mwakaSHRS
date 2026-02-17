<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'nurse') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$appointment_id = $input['appointment_id'] ?? 0;
$status = $input['status'] ?? '';
$student_id = $input['student_id'] ?? 0;

if(!$appointment_id || !in_array($status,['confirmed','rejected'])) {
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit();
}

$stmt = mysqli_prepare($conn, "UPDATE appointments SET status=?, nurse_id=?, updated_at=NOW() WHERE appointment_id=?");
$nurse_id = $_SESSION['user_id'];
mysqli_stmt_bind_param($stmt, "iii", $status, $nurse_id, $appointment_id);
if(mysqli_stmt_execute($stmt)) {

    $message = "Your appointment has been " . $status . ".";
    $notify = mysqli_prepare($conn, "INSERT INTO notifications (user_id, type, message) VALUES (?, 'appointment', ?)");
    mysqli_stmt_bind_param($notify, "is", $student_id, $message);
    mysqli_stmt_execute($notify);
    mysqli_stmt_close($notify);

    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>mysqli_error($conn)]);
}
mysqli_stmt_close($stmt);
?>
