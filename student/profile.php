<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT
        s.full_name, s.reg_no,s.gender, s.DoB AS date_of_birth,s.phone,s.course,s.year_of_study,
        s.address,s.email,s.emergency_contact,
        m.blood_group,
        a.allergies
    FROM students s
    LEFT JOIN medical_records m ON s.student_id = m.student_id
    LEFT JOIN allergies_conditions a ON s.student_id = a.student_id
    WHERE s.user_id = ?
";


$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    die("Student profile not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/profile.css">
</head>
<body>

<div class="topbar">
    <h2>SHRS</h2>
    <div class="topbar-right">
        <span><?= htmlspecialchars($student['full_name']) ?></span>
        <a href="/Mwaka.SHRS.2/logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <div class="profile-header">
        <img src="/Mwaka.SHRS.2/assets/profile.png" alt="Profile Photo">
        <div>
            <h3><?= htmlspecialchars($student['full_name']) ?></h3>
            <p><?= htmlspecialchars($student['reg_no']) ?></p>
            <span class="status">Active Student</span>
        </div>
    </div>

    <div class="card">
        <h4>Personal Information</h4>
        <div class="grid">
            <p><strong>Gender:</strong> <?= $student['gender'] ?></p>
            <p><strong>Date of Birth:</strong> <?= $student['date_of_birth'] ?></p>
            <p><strong>Phone:</strong> <?= $student['phone'] ?></p>
            <p><strong>Email:</strong> <?= $student['email'] ?></p>
            <p><strong>Address:</strong> <?= $student['address'] ?></p>
        </div>
    </div>

    <div class="card">
        <h4>Academic Information</h4>
        <div class="grid">
            <p><strong>Course:</strong> <?= $student['course'] ?></p>
            <p><strong>Year of Study:</strong> <?= $student['year_of_study'] ?></p>
        </div>
    </div>

    <div class="card highlight">
        <h4>Health Information</h4>
        <div class="grid">
            <p><strong>Blood Group:</strong> <?= $student['blood_group'] ?? 'Not Set' ?></p>
            <p><strong>Allergies:</strong> <?= $student['allergies'] ?: 'None' ?></p>
            <p><strong>Emergency Contact:</strong> <?= $student['emergency_contact'] ?></p>
        </div>
    </div>

    <div class="actions">
        <a href="appointments.php" class="btn secondary">Request Appointment</a>
    </div>

</div>

<script src="/Mwaka.SHRS.2/scripts/profile.js"></script>
</body>
</html>
