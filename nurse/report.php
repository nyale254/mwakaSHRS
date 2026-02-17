<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header("Location: ../index.php");
    exit();
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date   = $_GET['end_date'] ?? date('Y-m-d');

$totaltreatments = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total 
    FROM treatments 
    WHERE treatment_date BETWEEN '$start_date' AND '$end_date'
"))['total'];

$totalStudents = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(DISTINCT student_id) AS total 
    FROM treatments 
    WHERE treatment_date BETWEEN '$start_date' AND '$end_date'
"))['total'];

$totalReferrals = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total 
    FROM treatments 
    WHERE category='Referral'
    AND treatment_date BETWEEN '$start_date' AND '$end_date'
"))['total'];

$categoryQuery = mysqli_query($conn,"
    SELECT category, COUNT(*) AS total 
    FROM treatments 
    WHERE treatment_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY category
");

$categories = [];
$categoryCounts = [];

while($row = mysqli_fetch_assoc($categoryQuery)){
    $categories[] = $row['category'];
    $categoryCounts[] = $row['total'];
}

$trendQuery = mysqli_query($conn,"
    SELECT treatment_date, COUNT(*) as total
    FROM treatments
    WHERE treatment_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY treatment_date
    ORDER BY treatment_date ASC
");

$trendDates = [];
$trendCounts = [];

while($row = mysqli_fetch_assoc($trendQuery)){
    $trendDates[] = $row['treatment_date'];
    $trendCounts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nurse Report | SHRS</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/report.css">
</head>
<body>
<div class="topbar">
    <h2>SHRS Nurse Report</h2>
    <div>
        <a href="/Mwaka.SHRS.2/nurse/dashboard.php" class= 'primary_btn'>Back</a>
        <a href="../logout.php" class="secondary_btn">Logout</a>
        <a href="javascript:window.print()" class="btn">Print</a>
    </div>
</div>
<form class="filters" method="GET">
    <input type="date" name="start_date" value="<?= $start_date ?>">
    <input type="date" name="end_date" value="<?= $end_date ?>">
    <a href="generate_report.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" target="_blank">
        <button type="button" class="generate-btn">Generate PDF Report</button>
    </a>
</form>
<div class="summary">
    <div class="card">
        <h3>Total treatments</h3>
        <p><?= $totaltreatments ?></p>
    </div>
    <div class="card">
        <h3>Students Treated</h3>
        <p><?= $totalStudents ?></p>
    </div>
    <div class="card">
        <h3>Referrals Made</h3>
        <p><?= $totalReferrals ?></p>
    </div>
</div>
<div class="charts">
    <div class="chart-box">
        <canvas id="categoryChart"></canvas>
    </div>
    <div class="chart-box">
        <canvas id="trendChart"></canvas>
    </div>
</div>
<div class="table-section">
    <h3>Detailed Treatment Records</h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Student</th>
            <th>Category</th>
            <th>Diagnosis</th>
            <th>Treatment</th>
        </tr>
        <?php
        $records = mysqli_query($conn,"
            SELECT t.treatment_date, s.full_name, t.category, t.diagnosis, t.medication
            FROM treatments t
            JOIN students s ON t.student_id = s.student_id
            WHERE t.treatment_date BETWEEN '$start_date' AND '$end_date'
            ORDER BY t.treatment_date DESC
        ");

        while($row = mysqli_fetch_assoc($records)){
            echo "<tr>
                <td>{$row['treatment_date']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['category']}</td>
                <td>{$row['diagnosis']}</td>
                <td>{$row['medication']}</td>
            </tr>";
        }
        ?>
    </table>
</div>

<script>
new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
            label: 'Cases',
            data: <?= json_encode($categoryCounts) ?>,
            backgroundColor: '#0b7285'
        }]
    }
});

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($trendDates) ?>,
        datasets: [{
            label: 'Daily treatments',
            data: <?= json_encode($trendCounts) ?>,
            borderColor: '#198754',
            fill:false
        }]
    }
});
</script>

</body>
</html>