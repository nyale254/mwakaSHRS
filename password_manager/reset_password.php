<?php
session_start();
include '../connect.php';

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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/reset_pass.css">
</head>
<body>
<div class="reset-box">
    <h2>Reset Password</h2>

    <?php if(!empty($error)) echo "<p class='message error'>$error</p>"; ?>
    <?php if(!empty($success)) echo "<p class='message success'>$success</p>"; ?>

    <form method="POST">
        <div  class= 'password_box'>
            <label>New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" required>
            <span class="password-toggle"  data-toggle="password">👁️</span>
        </div>
        <div id="password-strength"></div>

        <div  class= 'password_box'>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
            <span class="password-toggle"  data-toggle="confirm_password">👁️</span>
        </div>
        <div id="password-match"></div>
        <button type="submit">Reset Password</button>
    </form>

    <a href="index.php" class="back-link">Back to Login</a>
</div>

<script>
document.querySelectorAll(".password-toggle").forEach(icon => {
    icon.addEventListener("click", function(){

        const inputId = this.getAttribute("data-toggle");
        const field = document.getElementById(inputId);

        if(field.type === "password"){
            field.type = "text";
            this.textContent = "👁️‍🗨️";
        }else{
            field.type = "password";
            this.textContent = "👁️";
        }

    });
});

const password = document.getElementById("password");
const strengthText = document.getElementById("password-strength");

password.addEventListener("keyup", function(){

    const value = password.value;
    let strength = "";

    if(value.length < 6){
        strength = "Weak ❌";
        strengthText.style.color = "red";
    }
    else if(value.match(/[A-Z]/) && value.match(/[0-9]/) && value.length >= 8){
        strength = "Strong 💪";
        strengthText.style.color = "green";
    }
    else{
        strength = "Medium ⚠️";
        strengthText.style.color = "orange";
    }

    strengthText.textContent = "Password Strength: " + strength;

});


const confirmPassword = document.getElementById("confirm_password");
const matchText = document.getElementById("password-match");

function checkMatch(){

    if(confirmPassword.value === ""){
        matchText.textContent = "";
        return;
    }

    if(password.value === confirmPassword.value){
        matchText.textContent = "Passwords match ✔️";
        matchText.style.color = "green";
    }else{
        matchText.textContent = "Passwords do not match ❌";
        matchText.style.color = "red";
    }

}

password.addEventListener("keyup", checkMatch);
confirmPassword.addEventListener("keyup", checkMatch);

</script>
</body>
</html>