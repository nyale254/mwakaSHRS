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

                $_SESSION['username']  = $row['username'];
                $_SESSION['fullname']  = $row['fullname'];
                $_SESSION['role']      = $row['role'];
                $_SESSION['user_id']   = $row['user_id'];


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

<div class="login-box">
    <h2>SHRS Login</h2>

    <?php  if ($submitted && $error != "") { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required >
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login" value="login">Login</button>
    </form>
</div>

</body>
</html>
