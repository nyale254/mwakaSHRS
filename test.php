<?php
session_start();
include "connect.php";

header('Content-Type: application/json');

/*if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}*/

// =====================
// 1. STATS
// =====================
$stats = [];

// Total students
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM students");
$stats['total_students'] = mysqli_fetch_assoc($res)['total'];

// Total visits
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM health_records");
$stats['total_visits'] = mysqli_fetch_assoc($res)['total'];

// Pending cases
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM health_records WHERE status='Pending'");
$stats['pending_cases'] = mysqli_fetch_assoc($res)['total'];

// Recovered / completed
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM health_records WHERE status='Completed'");
$stats['recovered'] = mysqli_fetch_assoc($res)['total'];


// =====================
// 2. COMMON AILMENTS (BAR CHART)
// =====================
$ailments = [];
$res = mysqli_query($conn, "
    SELECT condition_name, COUNT(*) as total 
    FROM health_records 
    GROUP BY condition_name 
    ORDER BY total DESC 
    LIMIT 5
");

while ($row = mysqli_fetch_assoc($res)) {
    $ailments[] = $row;
}


// =====================
// 3. MONTHLY TREND (LINE CHART)
// =====================
$trend = [];
$res = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(visit_date, '%b') as month,
        COUNT(*) as total
    FROM health_records
    WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY MONTH(visit_date)
");

while ($row = mysqli_fetch_assoc($res)) {
    $trend[] = $row;
}


// =====================
// 4. RECENT RECORDS (TABLE)
// =====================
$records = [];
$res = mysqli_query($conn, "
    SELECT 
        s.student_id,
        CONCAT(s.first_name, ' ', s.last_name) as fullname,
        s.age,
        h.condition_name,
        h.visit_date,
        h.status
    FROM health_records h
    JOIN students s ON h.student_id = s.student_id
    ORDER BY h.visit_date DESC
    LIMIT 10
");

while ($row = mysqli_fetch_assoc($res)) {
    $records[] = $row;
}


// =====================
// 5. ALERTS
// =====================
$alerts = [];

// Example: many visits same condition
$res = mysqli_query($conn, "
    SELECT condition_name, COUNT(*) as total
    FROM health_records
    WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY condition_name
    HAVING total > 5
");

while ($row = mysqli_fetch_assoc($res)) {
    $alerts[] = "High cases of " . $row['condition_name'] . " (" . $row['total'] . " this week)";
}

// If no alerts
if (empty($alerts)) {
    $alerts[] = "No critical alerts";
}


echo json_encode([
    "success" => true,
    "stats" => $stats,
    "ailments" => $ailments,
    "trend" => $trend,
    "records" => $records,
    "alerts" => $alerts,
    "last_updated" => date("Y-m-d H:i:s")
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Health System | Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="/Mwaka.SHRS.2/styles/test.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <div class="report-container" id="reportContainer">
            <!-- Header -->
            <div class="header">
                <div class="title-section">
                    <h1><i class="fas fa-notes-medical"></i> Student Health System</h1>
                    <p>Administrative Report & Clinical Analysis Dashboard</p>
                </div>
                <div class="action-buttons">
                    <button class="btn-refresh" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
                    <button class="btn-pdf" id="exportPdfBtn">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingOverlay" class="loading-overlay" style="display: none;">
                <div class="spinner"></div>
                <p>Loading dashboard data...</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid" id="statsGrid">
                <div class="stat-card skeleton">Loading...</div>
                <div class="stat-card skeleton">Loading...</div>
                <div class="stat-card skeleton">Loading...</div>
                <div class="stat-card skeleton">Loading...</div>
            </div>

            <!-- Analytics Row -->
            <div class="analytics-row">
                <div class="chart-box">
                    <div class="section-title">
                        <i class="fas fa-chart-bar"></i> Common Ailments Distribution
                    </div>
                    <canvas id="ailmentsChart"></canvas>
                </div>
                <div class="alerts-box">
                    <div class="section-title">
                        <i class="fas fa-exclamation-triangle"></i> Critical Alerts
                    </div>
                    <ul class="alert-list" id="alertsList">
                        <li>Loading alerts...</li>
                    </ul>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="chart-box full-width">
                <div class="section-title">
                    <i class="fas fa-chart-line"></i> Monthly Health Center Visits
                </div>
                <canvas id="trendChart"></canvas>
            </div>

            <!-- Student Records Table -->
            <div class="section-title">
                <i class="fas fa-table-list"></i> Recent Student Health Records
            </div>
            <div class="data-table-wrapper">
                <table id="healthTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Condition</th>
                            <th>Visit Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="6" class="loading-cell">Loading records...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="footer-note">
                <i class="fas fa-chart-simple"></i> Real-time data from health system • Last updated: <span id="lastUpdated">--</span>
            </div>
        </div>
    </div>

    <script src="scripts/test.js"></script>
</body>
</html>