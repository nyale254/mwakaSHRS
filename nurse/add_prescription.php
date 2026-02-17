<?php
$conn = mysqli_connect("localhost", "root", "", "SHRS_db");
if (!$conn) die("Database connection failed");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $medication = $_POST['medication'];
    $instructions = $_POST['instructions'];
    $date = date('Y-m-d');

    $sql = "INSERT INTO prescriptions(student_id, date_given, medication, instructions) 
            VALUES('$student_id', '$date', '$medication', '$instructions')";
    mysqli_query($conn, $sql);
    header("Location: prescriptions.php");
    exit();
}

// Fetch students for dropdown
$students = mysqli_query($conn, "SELECT id, full_name FROM students ORDER BY full_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Prescription | SHRS</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
<div class="container">
    <main class="main">
        <h1>Add Prescription</h1>
        <form method="POST">
            <label>Student:</label>
            <select name="student_id" required>
                <?php while ($s = mysqli_fetch_assoc($students)) { ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo $s['full_name']; ?></option>
                <?php } ?>
            </select>

            <label>Medication / Treatment:</label>
            <input type="text" name="medication" required>

            <label>Dosage / Instructions:</label>
            <textarea name="instructions" required></textarea>

            <button type="submit" class="btn">Save</button>
        </form>
    </main>
</div>
</body>
</html>
