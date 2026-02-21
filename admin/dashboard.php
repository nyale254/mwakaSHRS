<?php
session_start();
include "../connect.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}
$inactiveLimit = 600;

if (isset($_SESSION['last_activity'])) {
    $inactiveTime = time() - $_SESSION['last_activity'];

    if ($inactiveTime > $inactiveLimit) {
        session_unset();
        session_destroy();
        header("Location:/Mwaka.SHRS.2/index.php?timeout=1");
        exit();
    }
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$totalStudentsQuery = "SELECT COUNT(*) as total FROM users WHERE role='student'";
$totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
$totalStudents = mysqli_fetch_assoc($totalStudentsResult)['total'] ?? 0;

$recordsQuery = "SELECT COUNT(*) as total FROM medical_records";
$recordsResult = mysqli_query($conn, $recordsQuery);
$totalRecords = mysqli_fetch_assoc($recordsResult)['total'] ?? 0;

$today = date("Y-m-d");
$appointmentsQuery = "SELECT * FROM appointments WHERE appointment_date='$today'";
$appointmentsResult = mysqli_query($conn, $appointmentsQuery);
$totalAppointments = mysqli_num_rows($appointmentsResult);

$criticalQuery = "SELECT COUNT(*) as total FROM health_records WHERE severity='Critical'";
$criticalResult = mysqli_query($conn, $criticalQuery);
$totalCritical = mysqli_fetch_assoc($criticalResult)['total'] ?? 0;

$trendQuery = "
SELECT MONTH(created_at) as month, COUNT(*) as total
FROM health_records
GROUP BY MONTH(created_at)
ORDER BY month ASC
";
$trendResult = mysqli_query($conn, $trendQuery);

$months = [];
$totals = [];

while($row = mysqli_fetch_assoc($trendResult)){
    $months[] = date("M", mktime(0,0,0,$row['month'],1));
    $totals[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SHRS Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="\Mwaka.SHRS.2\styles\dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
            <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
        </svg>
        SHRS SYSTEM
    </div>
    <ul>
        <li><a class="active">Dashboard</a></li>

        <li><a href="student_management.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/>
            </svg>
            Manage Students
        </a></li>

        <li><a href="users_management.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/>
            </svg>
            Manage Students
        </a></li>

        <li><a href="records.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="M200-200h560v-367L567-760H200v560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h400l240 240v400q0 33-23.5 56.5T760-120H200Zm80-160h400v-80H280v80Zm0-160h400v-80H280v80Zm0-160h280v-80H280v80Zm-80 400v-560 560Z"/>
            </svg>
            Health Records
        </a></li>

        <li><a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="M345-120q-94 0-159.5-65.5T120-345q0-45 17-86t49-73l270-270q32-32 73-49t86-17q94 0 159.5 65.5T840-615q0 45-17 86t-49 73L504-186q-32 32-73 49t-86 17Zm266-286 107-106q20-20 31-47t11-56q0-60-42.5-102.5T615-760q-29 0-56 11t-47 31L406-611l205 205ZM345-200q29 0 56-11t47-31l106-107-205-205-107 106q-20 20-31 47t-11 56q0 60 42.5 102.5T345-200Z"/>
            </svg>
            Medications
        </a></li>

        <li><a href="audit_logs.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="M640-160v-280h160v280H640Zm-240 0v-640h160v640H400Zm-240 0v-440h160v440H160Z"/>
            </svg>
            Audit logs
        </a></li>

        <li><a href="report.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q13-36 43.5-58t68.5-22q38 0 68.5 22t43.5 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm80-80h280v-80H280v80Zm0-160h400v-80H280v80Zm0-160h400v-80H280v80Zm221.5-198.5Q510-807 510-820t-8.5-21.5Q493-850 480-850t-21.5 8.5Q450-833 450-820t8.5 21.5Q467-790 480-790t21.5-8.5ZM200-200v-560 560Z"/>
            </svg>
            Report
        </a></li>

        <li><a href="#" class="nav-link" data-page="settings.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/>
            </svg>
            Settings
        </a></li>
    </ul>
</div>

<div class="main">

<div class="topbar">
    <div class="search">
        <input type="text" id="searchBox" placeholder="Search students...">
    </div>
    <div class="profile">
        <span><?= $_SESSION['fullname'] ?? 'Admin'; ?></span>
        <a href="../logout.php" class="logoutBtn">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
            </svg>
            Logout
        </a>
    </div>
</div>

<div class="content" id="main-content">
<h1>Dashboard Overview</h1>

<div class="cards">

    <div class="card">
        <h3>Total Students</h3>
        <div class="number"><?= $totalStudents ?></div>
    </div>

    <div class="card">
        <h3>Health Records</h3>
        <div class="number"><?= $totalRecords ?></div>
    </div>

    <div class="card">
        <h3>Today's Appointments</h3>
        <div class="number"><?= $totalAppointments ?></div>
    </div>

    <div class="card">
        <h3>Critical Cases</h3>
        <div class="number"><?= $totalCritical ?></div>
    </div>

</div>

<div class="grid-2">

    <div class="box">
        <h2>Health Records Trend</h2>
        <canvas id="trendChart"></canvas>
    </div>

    <div class="box">
        <h2>Today's Appointments</h2>

        <?php 
        mysqli_data_seek($appointmentsResult, 0);
        while($row = mysqli_fetch_assoc($appointmentsResult)): 
        ?>
            <div class="appointment">
                <?= $row['full_name']; ?>
                <span class="status <?= strtolower($row['status']); ?>">
                    <?= $row['status']; ?>
                </span>
                <div class="time"><?= date("h:i A", strtotime($row['appointment_date'])); ?></div>
            </div>
        <?php endwhile; ?>

    </div>
</div>

<div class="box">
<h2>Recent Activities</h2>

<?php
$activityQuery = "SELECT * FROM visits ORDER BY created_at DESC LIMIT 5";
$activityResult = mysqli_query($conn, $activityQuery);

while($activity = mysqli_fetch_assoc($activityResult)):
?>
    <div class="activity">
        <?= $activity['reason']; ?>
        <div class="time"><?= date("h:i A", strtotime($activity['created_at'])); ?></div>
    </div>
<?php endwhile; ?>

</div>

</div>
</div>

<script>
const ctx = document.getElementById('trendChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($months); ?>,
        datasets: [{
            label: 'Health Records',
            data: <?= json_encode($totals); ?>,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        }
    }
});

document.getElementById("searchBox").addEventListener("keyup", function(){
    let value = this.value.toLowerCase();
    console.log("Searching:", value);
});

const links = document.querySelectorAll('.nav-link');
const content = document.getElementById('main-content');

links.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        // Remove active class from all links
        links.forEach(l => l.classList.remove('active'));
        this.classList.add('active');

        const page = this.getAttribute('data-page');

        fetch(page)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;

                // Optional: re-initialize charts or JS inside the new content
                if(page.includes('dashboard')) {
                    initDashboardChart(); // define this in a function
                }
            })
            .catch(err => {
                content.innerHTML = "<p>Error loading page.</p>";
                console.error(err);
            });
    });
});

// Example: wrap your existing chart code in a function
function initDashboardChart() {
    const ctx = document.getElementById('trendChart')?.getContext('2d');
    if(!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($months); ?>,
            datasets: [{
                label: 'Health Records',
                data: <?= json_encode($totals); ?>,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });
}

// Initialize dashboard chart on first load
initDashboardChart();

</script>

</body>
</html>