<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode([]);
    exit();
}

$student_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn,
 "SELECT notification_id, message, status, created_at FROM notifications WHERE user_id=? 
 ORDER BY created_at DESC LIMIT 5"
);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notes = [];
while($row = mysqli_fetch_assoc($result)) {
    $notes[] = $row;
}

mysqli_stmt_close($stmt);

echo json_encode($notes);
?>