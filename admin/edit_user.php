<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$success = "";
$error   = "";


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = (int) $_GET['id'];
$query = "
    SELECT user_id, fullname, email, username, role, status
    FROM users
    WHERE user_id = ?
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    die("User not found.");
}

if (isset($_POST['update_user'])) {

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $username = trim($_POST['username']);
    $role     = $_POST['role'];
    $status   = $_POST['status'];

    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($fullname === "" || $email === "" || $username === "" || $role === "" || $status === "") {
        $error = "All fields except password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($new_password !== "" && strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($new_password !== "" && $new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {

        $check = mysqli_prepare(
            $conn,
            "SELECT user_id FROM users WHERE username = ? AND user_id != ?"
        );
        mysqli_stmt_bind_param($check, "si", $username, $user_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Username already exists.";
            mysqli_stmt_close($check);
        } else {

            mysqli_stmt_close($check);

            if ($new_password === "") {

                $update = "
                    UPDATE users
                    SET fullname = ?, email = ?, username = ?, role = ?, status = ?
                    WHERE user_id = ?
                ";
                $stmt = mysqli_prepare($conn, $update);
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssssi",
                    $fullname,
                    $email,
                    $username,
                    $role,
                    $status,
                    $user_id
                );

            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update = "
                    UPDATE users
                    SET fullname = ?, email = ?, username = ?, role = ?, status = ?, password = ?
                    WHERE user_id = ?
                ";
                $stmt = mysqli_prepare($conn, $update);
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssssi",
                    $fullname,
                    $email,
                    $username,
                    $role,
                    $status,
                    $hashed_password,
                    $user_id
                );
            }

            if (mysqli_stmt_execute($stmt)) {
                $success = "User updated successfully.";
            } else {
                $error = "Failed to update user.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/edit.css">
</head>
<body>

<div class="topbar-admin">
    <a href="dashboard.php" class="btn">Dashboard</a>
    <a href="users_management.php" class="btn">← Back</a>
    <a href="/Mwaka.SHRS.2/logout.php" class="logout">Logout</a>
</div>

<div class="form-container">

    <h2>Edit User</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="fullname"
               value="<?= htmlspecialchars($user['fullname']) ?>" required>

        <label>Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Username</label>
        <input type="text" name="username"
               value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Role</label>
        <select name="role" required>
            <option value="admin"   <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
            <option value="doctor"  <?= $user['role'] === 'doctor' ? 'selected' : '' ?>>Doctor</option>
        </select>

        <label>Status</label>
        <select name="status" required>
            <option value="active"   <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <hr>

        <h4>Change Password (Optional)</h4>

        <label>New Password</label>
        <input type="password" name="new_password" placeholder="Leave blank to keep current">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password">

        <div class="buttons">
            <button type="submit" name="update_user">Update User</button>
            <a href="user_management.php" class="cancel">Cancel</a>
        </div>

    </form>

</div>

</body>
</html>
