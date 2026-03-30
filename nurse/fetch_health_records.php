<?php
header('Content-Type: application/json'); 
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['full_name'] ?? '';

$history = [];

if ($student_id) {
    $stmt = $conn->prepare("SELECT * FROM health_records WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
} elseif ($student_name) {
    $student_name = "%".trim($student_name)."%";
    $stmt = $conn->prepare("SELECT * FROM health_records WHERE full_name LIKE ? ");
    $stmt->bind_param("s", $student_name);
} else {
    echo json_encode(["latest" => null, "history" => []]);
    exit;
}

if (!$stmt) { 
    echo json_encode(["latest" => null, "history" => [], "error" => $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

$latest = $history ? end($history) : null;

echo json_encode(["latest" => $latest, "history" => $history]);