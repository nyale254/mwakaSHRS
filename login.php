<?php
session_start();
include "connect.php";

$error = "";
$submitted = false;

if (isset($_POST["login"])) {
    $submitted = true;

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]); 
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {

            if (password_verify($password,$row['password'])) {
                $user_id = $row['user_id'];

                $_SESSION['username']  = $row['username'];
                $_SESSION['fullname']  = $row['fullname'];
                $_SESSION['role']      = $row['role'];
                $_SESSION['user_id']   = $row['user_id'];
                $_SESSION['student_id'] = $row['student_id'];
                
                $action = "Logging in";
                $detail = "Logged in to the system: $user_id with treatment ID: $username";
                $ip_address = $_SERVER['REMOTE_ADDR'];

                $log_query = "INSERT INTO activity_log(user_id, action, detail, ip_address, created_at)
                VALUES(
                    '$user_id',
                    '".mysqli_real_escape_string($conn,$action)."',
                    '".mysqli_real_escape_string($conn,$detail)."',
                    '$ip_address',
                    NOW()
                )";
                mysqli_query($conn,$log_query);


                if ($row['role'] === 'student') {
                    $_SESSION['student_id'] = $row['student_id'];
                    header("Location: student/dashboard.php");
                    exit();
                }

                if ($row['role'] === 'nurse') {
                    header("Location: nurse/dashboard.php");
                    exit();
                }

                if ($row['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                    exit();
                }

                $error = "Unknown user role.";
            } else {
                $error = "Wrong username or password.";
            }
        } else {
            $error = "Wrong username or password.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $error = "Database error.";
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SHRS | Login</title>
    <link rel="stylesheet" href="styles/Login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>SHRS Login</h2>

        <?php if ($submitted && $error != "") { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <form method="POST">
            <div>
                <label>Username:</label>
                <input type="text" name="username" placeholder="enter your username" required>
            </div>
            
            <div class= 'password_box'>
                <label>Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
                <span class="password-toggle" id="toggleIcon">👁️</span>
            </div>

            <button type="submit" name="login" value="login">Login</button>

            <a href="/Mwaka.SHRS.2/password_manager/forgot_password.php" class="forget_btn">
                Forgot your password
            </a>
        </form>
    </div>
</div>
<script>
    const toggleIcon = document.getElementById('toggleIcon');
    const passwordField = document.getElementById('password');
    
    toggleIcon.addEventListener('click', function() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.textContent = '👁️‍🗨️'; 
        } else {
            passwordField.type = 'password';
            toggleIcon.textContent = '👁️'; 
        }
    });
</script>
</body>
</html>
