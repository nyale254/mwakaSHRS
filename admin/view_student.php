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
    SELECT s.*, u.username, u.status AS account_status, u.user_id,
           m.blood_group,
           a.allergies,
    FROM students s
    LEFT JOIN users u ON s.user_id = u.user_id
    LEFT JOIN medical_records m ON s.student_id = m.student_id
    LEFT JOIN allergies_conditions a ON s.student_id = a.student_id
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student | SHRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; margin: 0; padding: 0; }
        .topbar { display: flex; justify-content: space-between; align-items: center; background: #0077cc; padding: 15px 30px; color: #fff; }
        .topbar h2 { margin: 0; font-size: 24px; }
        .topbar a { color: #fff; margin-left: 20px; text-decoration: none; font-weight: 600; }

        .container { max-width: 1000px; margin: 30px auto; padding: 20px; }

        .profile-header { display: flex; align-items: center; gap: 20px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .profile-header img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #0077cc; }
        .profile-header h3 { margin: 0; font-size: 22px; }
        .profile-header p { margin: 5px 0 0; color: #555; }

        .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card h4 { display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px; font-size: 18px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; }
        .grid p { margin: 0; padding: 8px 0; }

        .highlight { background: #f9f9f9; }

        .actions { display: flex; gap: 15px; margin-top: 20px; }
        .actions a { text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; }
        .btn { background: #0077cc; color: #fff; }
        .btn:hover { background: #005fa3; }
        .btn.secondary { background: #f0f0f0; color: #333; }
        .btn.secondary:hover { background: #e0e0e0; }

        @media (max-width: 600px) {
            .profile-header { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <h2>SHRS</h2>
    <div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="student_management.php"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="/Mwaka.SHRS.2/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">

    <div class="profile-header">
        <img src="/Mwaka.SHRS.2/assets/profile.png" alt="Profile Photo">
        <div>
            <h3><?= htmlspecialchars($student['full_name']) ?></h3>
            <p><i class="fas fa-id-badge"></i> <?= htmlspecialchars($student['reg_no']) ?></p>
            <p class="status"><i class="fas fa-user-check"></i> Active Student</p>
        </div>
    </div>

    <div class="card">
        <h4><i class="fas fa-user"></i> Personal Information</h4>
        <div class="grid">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($student['full_name']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></p>
            <p><strong>Emergency Contact:</strong> <?= htmlspecialchars($student['emergency_contact']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($student['address'] ?? 'Not set') ?></p>
            <p><strong>Parent:</strong> <?= htmlspecialchars($student['parent_name'] ?? 'Not assigned') ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['DoB'] ?? 'Not set') ?></p>
        </div>
    </div>

    <div class="card">
        <h4><i class="fas fa-graduation-cap"></i> Academic Information</h4>
        <div class="grid">
            <p><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?></p>
            <p><strong>Year of Study:</strong> <?= htmlspecialchars($student['year_of_study']) ?></p>
        </div>
    </div>

    <div class="card highlight">
        <h4><i class="fas fa-heartbeat"></i> Health Information</h4>
        <div class="grid">
            <p><strong>Blood Group:</strong> <?= htmlspecialchars($student['blood_group'] ?? 'Not set') ?></p>
            <p><strong>Allergies:</strong> <?= htmlspecialchars($student['allergies'] ?? 'None') ?></p>
        </div>
    </div>

    <?php if ($student['user_id']): ?>
    <div class="card">
        <h4><i class="fas fa-user-cog"></i> Login Account</h4>
        <div class="grid">
            <p><strong>Username:</strong> <?= htmlspecialchars($student['username']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($student['account_status'])) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="actions">
        <a href="edit_student.php?id=<?= $student['student_id'] ?>" class="btn"><i class="fas fa-edit"></i> Edit Student</a>
        <a href="student_management.php" class="btn secondary"><i class="fas fa-list"></i> Back to List</a>
    </div>

</div>

</body>
</html>
