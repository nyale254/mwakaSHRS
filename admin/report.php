<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

$role = $_SESSION['role'];
$period = $_GET['period'] ?? 'monthly';
$year = $_GET['year'] ?? date('Y');

$whereConditions = [];
$params = [];
$types = '';

if ($role === 'admin') {
} elseif ($role === 'nurse') {
    $whereConditions[] = "nurse_id = ?";
    $params[] = $_SESSION['user_id'];
    $types .= 'i'; 
} else {
    $whereConditions[] = "student_id = ?";
    $params[] = $_SESSION['user_id'];
    $types .= 'i'; 
}

$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = "WHERE " . implode(" AND ", $whereConditions);
}

if ($period === 'yearly') {
    $query = "
        SELECT YEAR(visit_date) as label, COUNT(*) as total
        FROM visit
        $whereClause
        GROUP BY YEAR(visit_date)
        ORDER BY YEAR(visit_date)
    ";
} else {
    if (!empty($whereClause)) {
        $whereClause .= " AND YEAR(visit_date) = ?";
    } else {
        $whereClause = "WHERE YEAR(visit_date) = ?";
    }
    $params[] = $year;
    $types .= 'i'; 
    
    $query = "
        SELECT MONTH(visit_date) as label, COUNT(*) as total
        FROM visit
        $whereClause
        GROUP BY MONTH(visit_date)
        ORDER BY MONTH(visit_date)
    ";
}

$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
} else {
    die("Prepare failed: " . mysqli_error($conn));
}

$labels = [];
$totals = [];

while ($row = mysqli_fetch_assoc($result)) {
    if ($period === 'monthly') {
        $labels[] = date('F', mktime(0, 0, 0, $row['label'], 1));
    } else {
        $labels[] = $row['label'];
    }
    $totals[] = $row['total'];
}

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports & Export | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/report.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>Reports & Export</h2>

<form method="GET">
    <label>Period</label>
    <select name="period" onchange="this.form.submit()">
        <option value="monthly" <?= $period=='monthly'?'selected':'' ?>>Monthly</option>
        <option value="yearly" <?= $period=='yearly'?'selected':'' ?>>Yearly</option>
    </select>

    <?php if ($period === 'monthly') { ?>
        <label>Year</label>
        <input type="number" name="year" value="<?= htmlspecialchars($year) ?>" min="2000" max="<?= date('Y') ?>">
    <?php } ?>

    <button type="submit">Generate</button>

    <a class="btn" href="export.php?period=<?= urlencode($period) ?>&year=<?= urlencode($year) ?>">
        Export PDF
    </a>
    <a href="dashboard.php" class="back-btn">← Back</a>
</form>

<?php if (empty($labels)) { ?>
    <p>No data found for the selected period.</p>
<?php } else { ?>
    <div class="chart-container">
        <canvas id="reportChart"></canvas>
    </div>

    <script>
    new Chart(document.getElementById('reportChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Total Visits',
                data: <?= json_encode($totals) ?>,
                backgroundColor: '#3498db'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Visits'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '<?= $period === 'monthly' ? 'Month' : 'Year' ?>'
                    }
                }
            }
        }
    });
    </script>
<?php } ?>

</body>
</html>