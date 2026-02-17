<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'nurse') {
    echo json_encode([]);
    exit();
}

$query = "
    SELECT 
        a.appointment_id,
        a.student_id,
        a.appointment_date,
        a.reason,
        a.status,
        s.full_name AS student_name
    FROM appointments a
    JOIN students s ON a.student_id = s.student_id
    ORDER BY a.appointment_date ASC
";

$result = mysqli_query($conn, $query);

$appointments = [];
while($row = mysqli_fetch_assoc($result)) {
    $appointments[] = [
        'appointment_id' => (int)$row['appointment_id'],
        'student_id' => (int)$row['student_id'],
        'appointment_date' => $row['appointment_date'],
        'reason' => $row['reason'],
        'status' => $row['status'],
        'student_name' => $row['student_name']
    ];
}

echo json_encode($appointments);
exit();
?>
