<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode([]);
    exit();
}

$student_id = $_SESSION['user_id'];
$notes = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=$student_id ORDER BY created_at DESC LIMIT 5");

$result = [];
while($row = mysqli_fetch_assoc($notes)){
    $result[] = $row;
}

echo json_encode($result);
?>
