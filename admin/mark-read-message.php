<?php
session_start();
include '../connect.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
    exit();
}

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE contact_messages SET status='Read' WHERE id=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'error'=>'Database error']);
    }
    $stmt->close();
} else {
    echo json_encode(['success'=>false, 'error'=>'Invalid ID']);
}

$conn->close();
?>