<?php
session_start();
include 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer-PHPMailer-3cd2a2a/src/Exception.php';
require 'PHPMailer/PHPMailer-PHPMailer-3cd2a2a/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-PHPMailer-3cd2a2a/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, fullname FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $fullname);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();

            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $insert = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $user_id, $token, $expires);
            $insert->execute();
            $insert->close();

            $reset_link = "http://localhost/Mwaka.SHRS.2/reset_password.php?token=$token";

            if ($_SERVER['SERVER_NAME'] === 'localhost') {
                file_put_contents('reset_links.log', "User: $email - Link: $reset_link\n", FILE_APPEND);
                $success = "Development Mode: Reset link logged. Check reset_links.log file.";
            } else {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'titusnyale1@gmail.com'; 
                    $mail->Password   = 'gszs lpsp ovpk dzxh';   
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('support@shrs.ac.ke', 'SHRS Support');
                    $mail->addAddress($email, $fullname);

                    $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Request';

                        $mail->Body = "
                            <div style='font-family: Arial; max-width:600px; padding:20px;'>
                                <h2 style='color:#1e81c4;'>Student Health Record System</h2>
                                <p>Hello $fullname,</p>
                                <p>Click the button below to reset your password:</p>

                                <p>
                                    <a href='$reset_link'
                                    style='display:inline-block;
                                            padding:12px 20px;
                                            background:#1e81c4;
                                            color:#ffffff;
                                            text-decoration:none;
                                            border-radius:5px;'>
                                        Reset Password
                                    </a>
                                </p>

                                <p>This link expires in 1 hour.</p>
                                <p>If you did not request this, please ignore this email.</p>
                            </div>
                        ";

                    $mail->AltBody = "Hello $fullname,\n\nCopy and paste this link into your browser:\n$reset_link\n\nThis link expires in 1 hour.";
                    $mail->send();
                    $success = "Password reset link sent to your email!";
                } catch (Exception $e) {
                    $error = "Mailer Error: {$mail->ErrorInfo}";
                }
            }

            $action = "Forgot Password Request";
            $details = "User ($email) requested password reset";
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $logStmt = $conn->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->bind_param("isss", $user_id, $action, $details, $ip_address);
            $logStmt->execute();
            $logStmt->close();

        } else {
            $error = "Email not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | SHRS</title>
    <style>
     
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1e81c4, #0f3057);
        }

        .forgot-box {
            background: #ffffff;
            width: 400px;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .forgot-box h2 {
            margin-bottom: 25px;
            color: #1e81c4;
            font-weight: 600;
        }

        form {
            text-align: left;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 6px;
            display: block;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s ease;
        }

        input[type="email"]:focus {
            border-color: #1e81c4;
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 129, 196, 0.15);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1e81c4;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #155d8b;
        }

        .back-link {
            display: block;
            margin-top: 18px;
            font-size: 13px;
            text-decoration: none;
            color: #1e81c4;
            transition: 0.3s ease;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #155d8b;
        }

        .message {
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .error {
            background: #ffe6e6;
            color: #cc0000;
        }

        .success {
            background: #e6ffee;
            color: #007a33;
        }
    </style>
</head>
<body>
<div class="forgot-box">
    <h2>Forgot Password</h2>

    <?php if(!empty($error)) echo "<p class='message error'>$error</p>"; ?>
    <?php if(!empty($success)) echo "<p class='message success'>$success</p>"; ?>

    <form method="POST">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <a href="login.php" class="back-link">Back to Login</a>
</div>
</body>
</html>