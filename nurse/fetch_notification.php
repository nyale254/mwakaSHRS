<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT notification_id, type, message, status, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY is_read ASC, created_at DESC
    LIMIT 10
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = [
        'id' => (int)$row['notification_id'],
        'type' => $row['type'],
        'message' => $row['message'],
        'status' => (bool)$row['status'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode($notifications);
exit();
?>
