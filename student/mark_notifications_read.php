<?php
session_start();
include '../connect.php';

$user_id = $_SESSION['user_id'];

$getStudent = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id=?");
mysqli_stmt_bind_param($getStudent, "i", $user_id);
mysqli_stmt_execute($getStudent);
$result = mysqli_stmt_get_result($getStudent);
$row = mysqli_fetch_assoc($result);
$student_id = $row ? $row['student_id'] : 0;
mysqli_stmt_close($getStudent);

$updateStmt = mysqli_prepare($conn, "
    UPDATE notifications 
    SET status = 1 
    WHERE user_id = ? AND status = 0
");
mysqli_stmt_bind_param($updateStmt, "i", $user_id);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

echo json_encode(["success" => true]);
?>