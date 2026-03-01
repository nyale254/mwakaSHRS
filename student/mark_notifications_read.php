<?php
session_start();
include '../connect.php';

$user_id = $_SESSION['user_id'];

mysqli_query($conn, "
    UPDATE notifications 
    SET status = 1 
    WHERE user_id = $user_id AND status = 0
");

echo json_encode(["success" => true]);
?>