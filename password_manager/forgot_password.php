<?php
session_start();
include '../connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/PHPMailer-PHPMailer/src/Exception.php';
require '../PHPMailer/PHPMailer-PHPMailer/src/PHPMailer.php';
require '../PHPMailer/PHPMailer-PHPMailer/src/SMTP.php';

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
            
            $delete = $conn->prepare("DELETE FROM password_resets WHERE user_id=?");
            $delete->bind_param("i", $user_id);
            $delete->execute();
            $delete->close();

            $insert = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $user_id, $token, $expires);
            $insert->execute();
            $insert->close();

            $reset_link = "http://localhost/Mwaka.SHRS.2/password_manager/reset_password.php?token=$token";
            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'titusnyale1@gmail.com';
                $mail->Password   = 'gszs lpsp ovpk dzxh'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('support@shrs.ac.ke', 'SHRS Support');
                $mail->addAddress($email, $fullname);

                $mail->isHTML(true);
                $mail->Subject = 'Reset Your Password - SHRS';

                $mail->Body = "
                <div style='font-family:Arial; max-width:600px; margin:auto; padding:20px;'>

                    <h2 style='color:#1e81c4;'>Student Health Record System</h2>

                    <p>Hello <b>$fullname</b>,</p>

                    <p>We received a request to reset your password.</p>

                    <p>
                    Click the button below to reset your password:
                    </p>

                    <p style='text-align:center'>
                        <a href='$reset_link'
                        style='display:inline-block;
                        padding:12px 20px;
                        background:#1e81c4;
                        color:white;
                        text-decoration:none;
                        border-radius:5px;
                        font-size:15px'>
                        Reset Password
                        </a>
                    </p>

                    <p>This link will expire in <b>1 hour</b>.</p>

                    <p>If you did not request this password reset, please ignore this email.</p>

                    <hr>

                    <small>Student Health Record System (SHRS)</small>

                </div>
                ";

                $mail->AltBody = "Hello $fullname,\n\nReset your password using this link:\n$reset_link\n\nThis link expires in 1 hour.";

                $mail->send();

                $success = "A password reset link has been sent to your email.";

            } catch (Exception $e) {

                $error = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            $action = "Forgot Password Request";
            $details = "User ($email) requested password reset";
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $logStmt = $conn->prepare("INSERT INTO activity_log (user_id, action,fullname, details, ip_address) VALUES (?, ?, ?, ?)");
            $logStmt->bind_param("issss", $user_id, $action, $details, $ip_address $fullname);
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

    <a href="../login.php" class="back-link">Back to Login</a>
</div>
</body>
</html>