<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$success = "";
$error = "";

$parentsResult = mysqli_query($conn, "SELECT parent_id, fullname FROM parents ORDER BY fullname ASC");

if (isset($_POST['add_student'])) {

    $full_name = trim($_POST['full_name']);
    $reg_no    = trim($_POST['reg_no']);
    $gender    = $_POST['gender'];
    $course    = trim($_POST['course']);
    $phone     = trim($_POST['phone']);
    $emergency_contact = trim($_POST['emergency_contact']);
    $email     = trim($_POST['email']);
    $Year_of_study   = $_POST['year_of_study'];

    if ($full_name == "" || $reg_no == "" || $gender == "" || $course == "" || $phone == "" ||
        $emergency_contact == "" || $email == "" ||$Year_of_study=="" ) {
        $error = "All fields are required.";
    } else {

        $check = mysqli_prepare($conn, "SELECT student_id FROM students WHERE reg_no = ?");
        mysqli_stmt_bind_param($check, "s", $reg_no);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Student with this Registration Number already exists.";
        } else {

            $password = password_hash("student123", PASSWORD_DEFAULT); 
            $role = "student";

            $userStmt = mysqli_prepare($conn, "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($userStmt, "ssss", $full_name, $email, $password, $role);
            mysqli_stmt_execute($userStmt);

            $user_id = mysqli_insert_id($conn); 
            mysqli_stmt_close($userStmt);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO students (
                    full_name, reg_no, gender, course, phone, emergency_contact, email, user_id,year_of_study, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?, NOW())"
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
                $user_id,
                $Year_of_study
            );

            if (mysqli_stmt_execute($stmt)) {
                $success = "Student added successfully!";
            } else {
                $error = "Database error. Please try again.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/add_student.css">
</head>
<body>

<div class="topbar-admin">
    <a href="dashboard.php" class="btn">Dashboard</a>
    <a href="student_management.php" class="btn">← Back</a>
    <a href="/Mwaka.SHRS.2/logout.php" class="logout">Logout</a>
</div>

<div class="form-container">

    <h2>Add New Student</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Registration Number</label>
        <input type="text" name="reg_no" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Phone</label>
        <input type="tel" name="phone" required>

        <label>Emergency Contact</label>
        <input type="tel" name="emergency_contact" required>

        <label>Course</label>
        <input type="text" name="course" required>

        <label>Year of Study</label>
        <select name="year_of_study" required>
            <option value="">-- Select Year --</option>
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
            <option value="3">Year 3</option>
            <option value="4">Year 4</option>
        </select>


        <label>Gender</label>
        <select name="gender" required>
            <option value="">-- Select --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <div class="buttons">
            <button type="submit" name="add_student">Add Student</button>
            <a href="student_management.php" class="cancel">Cancel</a>
        </div>

    </form>

</div>

</body>
</html>
