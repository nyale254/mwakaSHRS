<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = (int) $_GET['id'];

if ($user_id === $_SESSION['user_id']) {
    die("You cannot delete your own account.");
}

$query = "SELECT user_id, username, fullname, role FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    die("User not found.");
}

if (isset($_POST['confirm_delete'])) {

    mysqli_begin_transaction($conn);

    try {
        if ($user['role'] === 'student') {
            $depTables = ['students', 'medical_records', 'allergies_conditions'];
            foreach ($depTables as $dep) {
                $depStmt = mysqli_prepare($conn, "DELETE FROM $dep WHERE user_id = ?");
                mysqli_stmt_bind_param($depStmt, "i", $user_id);
                mysqli_stmt_execute($depStmt);
                mysqli_stmt_close($depStmt);
            }
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);

        header("Location: users_management.php?deleted=1");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        die("Delete failed. Please try again.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete User | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/delete.css">
</head>
<body>

<div class="container">

    <h2>Confirm Deletion</h2>

    <div class="warning-box">
        <p>
            You are about to permanently delete the user <strong><?= htmlspecialchars($user['username']) ?></strong> 
            (<?= htmlspecialchars($user['fullname']) ?>).<br>
            <strong>This action cannot be undone.</strong>
        </p>
    </div>

    <form method="POST" class="actions">
        <button type="submit" name="confirm_delete" class="btn danger">
            Yes, Delete
        </button>
        <a href="users_management.php" class="btn secondary">
            Cancel
        </a>
    </form>

</div>

<script>
    const btn = document.querySelector(".btn.danger");
    btn.addEventListener("click", function(e) {
        if(!confirm("Are you sure you want to delete this user? This cannot be undone.")) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>
