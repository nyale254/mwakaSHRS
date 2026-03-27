<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode([]);
    exit();
}
$session_user_id = $_SESSION['user_id'];

$getStudent = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id=?");
mysqli_stmt_bind_param($getStudent, "i", $session_user_id);
mysqli_stmt_execute($getStudent);
$result = mysqli_stmt_get_result($getStudent);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($getStudent);

if (!$row) {
    echo json_encode([]);
    exit();
}
$student_id = $row['student_id'];

$getUser = mysqli_prepare($conn, "SELECT user_id FROM students WHERE student_id=?");
mysqli_stmt_bind_param($getUser, "i", $student_id);
mysqli_stmt_execute($getUser);
$resUser = mysqli_stmt_get_result($getUser);
$userRow = mysqli_fetch_assoc($resUser);
mysqli_stmt_close($getUser);

if (!$userRow) {
    echo json_encode([]);
    exit();
}

$student_user_id = $userRow['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT notification_id, message, status, created_at 
    FROM notifications 
    WHERE user_id=? 
    ORDER BY created_at DESC 
    LIMIT 5
");
mysqli_stmt_bind_param($stmt, "i", $session_user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notes[] = $row;
}

mysqli_stmt_close($stmt);

// Return as JSON
echo json_encode($notes);
?>