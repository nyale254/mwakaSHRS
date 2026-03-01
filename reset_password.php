<?php
/*session_start();
include 'connect.php';

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($user_id, $expires);

if ($stmt->num_rows == 0) {
    die("Invalid or expired token.");
}

$stmt->fetch();
$stmt->close();

if (strtotime($expires) < time()) {
    die("Token has expired. Please request a new password reset.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if ($password != $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $update->bind_param("si", $hashed, $user_id);
        $update->execute();
        $update->close();

        $del = $conn->prepare("DELETE FROM password_resets WHERE token=?");
        $del->bind_param("s", $token);
        $del->execute();
        $del->close();

        $action = "Password Reset";
        $details = "User (ID: $user_id) reset their password via forgot password link.";
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $logStmt = $conn->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $logStmt->bind_param("isss", $user_id, $action, $details, $ip_address);
        $logStmt->execute();
        $logStmt->close();

        $success = "Password reset successfully! You can now <a href='login.php'>login</a>.";
    }
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | SHRS</title>
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


        .reset-box {
            background: #ffffff;
            width: 400px;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .reset-box h2 {
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

        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s ease;
        }

        input[type="password"]:focus {
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
<div class="reset-box">
    <h2>Reset Password</h2>

    <?php if(!empty($error)) echo "<p class='message error'>$error</p>"; ?>
    <?php if(!empty($success)) echo "<p class='message success'>$success</p>"; ?>

    <form method="POST">
        <label>New Password</label>
        <input type="password" name="password" placeholder="Enter new password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm new password" required>

        <button type="submit">Reset Password</button>
    </form>

    <a href="index.php" class="back-link">Back to Login</a>
</div>
</body>
</html>