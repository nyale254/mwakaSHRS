<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $subject  = trim($_POST['subject']);
    $message  = trim($_POST['message']);

    if (empty($fullname) || empty($email) || empty($subject) || empty($message)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }
    $adminQuery = "SELECT user_id FROM users WHERE role = 'admin' LIMIT 1";
    $adminResult = $conn->query($adminQuery);

    if ($adminResult->num_rows > 0) {
        $admin = $adminResult->fetch_assoc();
        $admin_id = $admin['user_id'];
    } else {
        die("No admin found.");
    }

    $stmt = $conn->prepare("INSERT INTO contact_messages 
        (fullname, email, subject, message, admin_id, sender_type, status) 
        VALUES (?, ?, ?, ?, ?, 'Guest', 'Unread')");

    if ($stmt) {
        $stmt->bind_param("ssssi", $fullname, $email, $subject, $message, $admin_id);

        if ($stmt->execute()) {
            $user_id = null; 
            $action = "Send Message";
            $details = "Guest ($fullname, $email) sent a message with subject: $subject";
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $logStmt = $conn->prepare("INSERT INTO activity_log (user_id, action, details,ip_address)
             VALUES (?, ?, ?, ?)");
            $logStmt->bind_param("isss", $user_id, $action, $details, $ip_address);
            $logStmt->execute();
            $logStmt->close();

            echo "<script>
                    alert('Message sent successfully to Admin!');
                    window.location.href='index.php';
                  </script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Database error.";
    }

    $conn->close();
}
?>