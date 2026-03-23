<?php
include "../connect.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

if (empty($q)) {
    echo json_encode([]);
    exit();
}

$search = "%$q%";
$results = [];

$stmt1 = $conn->prepare("
    SELECT student_id AS id, full_name AS name, 'student' AS type
    FROM students
    WHERE full_name LIKE ? OR student_id LIKE ? OR email LIKE ?
    LIMIT 5
");
$stmt1->bind_param("sss", $search, $search, $search);
$stmt1->execute();
$res1 = $stmt1->get_result();

while ($row = $res1->fetch_assoc()) {
    $results[] = $row;
}
$stmt2 = $conn->prepare("
    SELECT appointment_id AS id, reason AS name, 'appointment' AS type
    FROM appointments
    WHERE reason LIKE ? OR status LIKE ?
    LIMIT 5
");
$stmt2->bind_param("ss", $search, $search);
$stmt2->execute();
$res2 = $stmt2->get_result();

while ($row = $res2->fetch_assoc()) {
    $results[] = $row;
}

$stmt3 = $conn->prepare("
    SELECT user_id AS id, fullname AS name, role AS type
    FROM users
    WHERE fullname LIKE ? OR email LIKE ?
    LIMIT 5
");
$stmt3->bind_param("ss", $search, $search);
$stmt3->execute();
$res3 = $stmt3->get_result();

while ($row = $res3->fetch_assoc()) {
    $results[] = $row;
}

$stmt4 = $conn->prepare("
    SELECT notification_id AS id, message AS name, 'notification' AS type
    FROM notifications
    WHERE message LIKE ?
    LIMIT 5
");
$stmt4->bind_param("s", $search);
$stmt4->execute();
$res4 = $stmt4->get_result();

while ($row = $res4->fetch_assoc()) {
    $results[] = $row;
}

echo json_encode($results);
?>