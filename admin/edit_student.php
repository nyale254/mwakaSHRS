<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid student ID.");
}

$student_id = (int) $_GET['id'];

$query = "
    SELECT s.*, u.username, u.status AS account_status, u.user_id
    FROM students s
    LEFT JOIN users u ON s.user_id = u.user_id
    WHERE s.student_id = ?
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    die("Student not found.");
}

$parentsResult = mysqli_query($conn, "SELECT parent_id, fullname FROM parents ORDER BY fullname ASC");
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $reg_no = trim($_POST['reg_no']);
    $gender = $_POST['gender'];
    $course = trim($_POST['course']);
    $phone = trim($_POST['phone']);
    $emergency_contact = trim($_POST['emergency_contact']);
    $email = trim($_POST['email']);
    $year_of_study = $_POST['year_of_study']; 
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $status = $_POST['status'] ?? 'active';

    if (!$full_name || !$reg_no || !$gender || !$course || !$phone || !$emergency_contact || !$email || !$year_of_study) {
        $error = "All fields except parent are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    }

    if (empty($error)) {
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE students 
                 SET full_name = ?, reg_no = ?, gender = ?, course = ?, phone = ?, emergency_contact = ?, email = ?,
                  year_of_study = ?
                 WHERE student_id = ?"
            );
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssi",
                $full_name,
                $reg_no,
                $gender,
                $course,
                $phone,
                $emergency_contact,
                $email,
                $year_of_study,
                $student_id
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($student['user_id']) {
                if ($password) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare(
                        $conn,
                        "UPDATE users SET username = ?, email = ?, password = ?, status = ? WHERE user_id = ?"
                    );
                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssssi",
                        $username,
                        $email,
                        $hashed_password,
                        $status,
                        $student['user_id']
                    );
                } else {
                    $stmt = mysqli_prepare(
                        $conn,
                        "UPDATE users SET username = ?, email = ?, status = ? WHERE user_id = ?"
                    );
                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssi",
                        $username,
                        $email,
                        $status,
                        $student['user_id']
                    );
                }
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            mysqli_commit($conn);
            $success = "Student updated successfully!";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/add_student.css">
</head>
<body>

<div class="topbar-admin">
    <a href="dashboard.php" class="btn">Dashboard</a>
    <a href="student_management.php" class="btn">← Back</a>
    <a href="/Mwaka.SHRS.2/logout.php" class="logout">Logout</a>
</div>

<div class="form-container">
    <h2>Edit Student</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>

        <label>Registration Number</label>
        <input type="text" name="reg_no" value="<?= htmlspecialchars($student['reg_no']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

        <label>Phone</label>
        <input type="tel" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" required>

        <label>Emergency Contact</label>
        <input type="tel" name="emergency_contact" value="<?= htmlspecialchars($student['emergency_contact']) ?>" required>

        <label>Course</label>
        <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" required>

        <label>Year of Study</label>
        <select name="year_of_study" required>
            <option value="">-- Select Year --</option>
            <?php for ($y=1; $y<=4; $y++): ?>
                <option value="<?= $y ?>" <?= $student['year_of_study']==$y?'selected':'' ?>>Year <?= $y ?></option>
            <?php endfor; ?>
        </select>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">-- Select --</option>
            <option value="Male" <?= $student['gender']=='Male'?'selected':'' ?>>Male</option>
            <option value="Female" <?= $student['gender']=='Female'?'selected':'' ?>>Female</option>
        </select>


        <?php if ($student['user_id']): ?>
            <h4>Login Account</h4>
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($student['username']) ?>" required>

            <label>Password (Leave blank to keep current)</label>
            <input type="password" name="password">

            <label>Status</label>
            <select name="status">
                <option value="active" <?= $student['account_status']=='active'?'selected':'' ?>>Active</option>
                <option value="disabled" <?= $student['account_status']=='disabled'?'selected':'' ?>>Disabled</option>
            </select>
        <?php endif; ?>

        <div class="buttons">
            <button type="submit" class="btn">Update Student</button>
            <a href="student_management.php" class="cancel">Cancel</a>
        </div>

    </form>
</div>

</body>
</html>
