<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$search = $_GET['q'] ?? '';
$search = mysqli_real_escape_string($conn, $search);

$results = [];

$studentQuery = "SELECT 'student' AS type, full_name AS name, student_id AS id
                 FROM students
                 WHERE full_name LIKE '%$search%'
                 LIMIT 10";
$res1 = mysqli_query($conn, $studentQuery);
while ($row = mysqli_fetch_assoc($res1)) {
    $results[] = $row;
}

$appointmentQuery = "SELECT  reason AS name, appointment_id AS id
                     FROM appointments
                     WHERE reason LIKE '%$search%'
                     LIMIT 10";
$res2 = mysqli_query($conn, $appointmentQuery);
while ($row = mysqli_fetch_assoc($res2)) {
    $results[] = $row;
}

$notificationQuery = "SELECT type, message AS name, notification_id AS id
                      FROM notifications
                      WHERE message LIKE '%$search%'
                      LIMIT 10";
$res3 = mysqli_query($conn, $notificationQuery);
while ($row = mysqli_fetch_assoc($res3)) {
    $results[] = $row;
}

header('Content-Type: application/json');
echo json_encode($results);
?>