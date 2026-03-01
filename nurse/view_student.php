<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    echo "<script>
            alert('Access denied. Nurse only.');
            window.location.href='../index.php';
          </script>";
    exit();
}

if (!$conn) {
    die("Database connection failed");
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('No student selected.');
            window.location.href='student_list.php';
          </script>";
    exit();
}

$student_id = intval($_GET['id']);

$query = "
SELECT student_id, full_name, reg_no, gender, course, DoB, blood_type
FROM students 
WHERE student_id = ?
ORDER BY course DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$studentResult = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($studentResult) == 0) {
    echo "<script>
            alert('Student not found.');
            window.location.href='student_list.php';
          </script>";
    exit();
}

$student = mysqli_fetch_assoc($studentResult);


$visitStmt = mysqli_prepare($conn, 
    "SELECT * FROM visits WHERE student_id = ? ORDER BY visit_date DESC"
);
mysqli_stmt_bind_param($visitStmt, "i", $student_id);
mysqli_stmt_execute($visitStmt);
$visitResult = mysqli_stmt_get_result($visitStmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Health Profile | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/view_student.css">
</head>
<body>

<div class="container">
    <main class="main">

        <header class="topbar">
            <h1>Student Health Profile</h1>
            <div>
                <a href="dashboard.php" class="primary_btn">Dashboard</a>
                <a href="../logout.php" class= "secondary_btn">Logout</a>
                <a href="print_medic_report.php?id=<?= $student_id ?>" 
                    class="btn" target="_blank">
                    Print Medical Report (PDF)
                </a>
            </div>
        </header>

        <section class="profile-card">
            <h2>Student Information</h2>
            <p><strong>Full Name:</strong> <?= htmlspecialchars($student['full_name']) ?></p>
            <p><strong>Registration No:</strong> <?= htmlspecialchars($student['reg_no']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['DoB']) ?></p>
            <p><strong>Blood Group:</strong> <?= htmlspecialchars($student['blood_type']) ?></p>

            <br>
            <a href="treatment.php?id=<?= $student_id ?>" class="btn">Add Treatment</a>
        </section>
        <section class="table-section">
            <h2>Medical History</h2>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Complaint</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                </tr>

                <?php if (mysqli_num_rows($visitResult) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($visitResult)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['visit_date']) ?></td>
                            <td><?= htmlspecialchars($row['complain']) ?></td>
                            <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                            <td><?= htmlspecialchars($row['treatment']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No medical records found</td>
                    </tr>
                <?php endif; ?>
            </table>
        </section>

    </main>
</div>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}
</script>

</body>
</html>