<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode([]);
    exit();
}

$student_id = $_SESSION['user_id'];

$query = "
    SELECT 
        appointment_id,
        appointment_date,
        reason,
        status
    FROM appointments
    WHERE student_id = ?
    ORDER BY appointment_date ASC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$appointments = [];
while($row = mysqli_fetch_assoc($result)) {
    $status = strtolower(trim($row['status'] ?? 'pending'));
    $appointments[] = [
        'appointment_id' => (int)$row['appointment_id'],
        'appointment_date' => $row['appointment_date'],
        'reason' => $row['reason'],
        'status' => $status
    ];
}

echo json_encode($appointments);
exit();
?>
