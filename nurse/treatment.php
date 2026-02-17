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
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('No student selected.');
            window.location.href='student_list.php';
          </script>";
    exit();
}

$student_id = intval($_GET['id']);
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<script>
            alert('Student not found.');
            window.location.href='student_list.php';
          </script>";
    exit();
}

$student = mysqli_fetch_assoc($result);
$treatStmt = mysqli_prepare($conn, "SELECT * FROM treatments WHERE student_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($treatStmt, "i", $student_id);
mysqli_stmt_execute($treatStmt);
$treatResult = mysqli_stmt_get_result($treatStmt);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medication & Treatment | EduHealth</title>
    <link rel="stylesheet" href="../styles/treatment.css">
</head>
<body>

<div class="topbar">
    <h1>Medication & Treatment</h1>
    <div>
        <a href="student_list.php" class="btn primary_btn">Back</a>
        <a href="../logout.php" class="btn secondary_btn">Logout</a>
    </div>
</div>

<div class="container">

    <div class="card">
        <h2>Student Information</h2>
        <div class="info-grid">
            <p><strong>Name:</strong> <?= htmlspecialchars($student['full_name']) ?></p>
            <p><strong>Reg No:</strong> <?= htmlspecialchars($student['reg_no']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['DoB']) ?></p>
        </div>
    </div>

    <div class="card">
        <h2>Add Treatment</h2>
        <form method="POST" action="save_treatment.php" class="treatment-form">
            <input type="hidden" name="student_id" value="<?= $student_id ?>">
            
            <label>Chief Complaint / Symptoms <span class="required">*</span></label>
            <textarea name="complain" placeholder="e.g. Fever, headache, cough" required></textarea>
            
            <label>Diagnosis <span class="required">*</span></label>
            <textarea name="diagnosis" placeholder="e.g. Upper respiratory tract infection" required></textarea>
            
            <label>Medication</label>
            <input type="text" name="medication" placeholder="e.g. Paracetamol 500mg">
            
            <label>Dosage / Instructions</label>
            <textarea name="dosage" placeholder="e.g. Twice daily after meals for 3 days"></textarea>
            
            <label>Category <span class="required">*</span></label>
            <select name="category" required>
                <option value="">-- Select --</option>
                <option>Medication Given</option>
                <option>Injection</option>
                <option>Rest / Observation</option>
                <option>Wound Care</option>
                <option>Laboratory Test</option>
                <option>Referral</option>
                <option>Counseling</option>
                <option>Vaccination</option>
                <option>Follow-up Advice</option>
            </select>
            
            <label>Additional Notes</label>
            <textarea name="notes" placeholder="Any additional observations or instructions"></textarea>
            <button type="submit" class="btn primary_btn">Save Treatment</button>
        </form>
    </div>
    <div class="card">
        <h2>Previous Treatments</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Diagnosis</th>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Category</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($treatResult) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($treatResult)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                        <td><?= htmlspecialchars($row['medication']) ?></td>
                        <td><?= htmlspecialchars($row['dosage']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No previous treatments found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>