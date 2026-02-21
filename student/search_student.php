<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$search = $_GET['q'] ?? '';
$search = mysqli_real_escape_string($conn, $search);

$query = "SELECT full_name, student_id FROM students WHERE ful_lname LIKE '%$search%' LIMIT 10";
$result = mysqli_query($conn, $query);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

header('Content-Type: application/json');
echo json_encode($students);