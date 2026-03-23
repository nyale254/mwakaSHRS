<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

include '../connect.php';

if(!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'No ID']);
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($message = $result->fetch_assoc()){
    echo json_encode([
        "success" => true,
        "message" => $message
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Message not found"
    ]);
}

$stmt->close();
$conn->close();
?>