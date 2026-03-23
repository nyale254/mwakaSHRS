<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

include '../connect.php';
include 'reply_functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/PHPMailer-PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/PHPMailer-PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/PHPMailer-PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$message_id = intval($_POST['message_id'] ?? 0);
$reply_text = trim($_POST['reply_text'] ?? '');
$admin_id = $_SESSION['user_id'];

if (!$message_id || empty($reply_text)) {
    echo json_encode(['success' => false, 'error' => 'Message ID and reply text are required']);
    exit();
}
$saveResult = saveReply($conn, $message_id, $admin_id, $reply_text);

if (!$saveResult['success']) {
    echo json_encode(['success' => false, 'error' => 'Failed to save reply: ' . $saveResult['error']]);
    exit();
}

$stmt = $conn->prepare("SELECT fullname, email, subject, message FROM contact_messages WHERE id=?");
$stmt->bind_param("i", $message_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Original message not found']);
    exit();
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username   = 'titusnyale1@gmail.com';
    $mail->Password   = 'gszs lpsp ovpk dzxh'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('noreply@shrs.com', 'Admin - SHRS');
    $mail->addAddress($user['email'], $user['fullname']);
    $mail->addReplyTo('noreply@shrs.com', 'Admin');

    $mail->isHTML(true);
    $mail->Subject = "Re: " . $user['subject'];
    $mail->Body = "
        <p>Dear {$user['fullname']},</p>
        <p>Thank you for contacting us. Here is our reply:</p>
        <blockquote>{$reply_text}</blockquote>
        <hr>
        <p><strong>Original Message:</strong></p>
        <blockquote>{$user['message']}</blockquote>
        <p>Best regards,<br>Admin - SHRS</p>
    ";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Reply saved and email sent successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Reply saved but email could not be sent: ' . $mail->ErrorInfo]);
}

$conn->close();