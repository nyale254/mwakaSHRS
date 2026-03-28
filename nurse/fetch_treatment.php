<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$student_id = $_GET['student_id'] ?? '';

if (!$student_id) {
    echo json_encode([]); 
    exit;
}

$stmt = $conn->prepare("
    SELECT treatment_date, category, medication, symptoms, diagnosis, dosage, notes, referral 
    FROM treatments 
    WHERE student_id = ? 
    ORDER BY treatment_date DESC
    LIMIT 1
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$latest = $result->fetch_assoc();
echo json_encode($latest);