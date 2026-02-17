<?php
session_start();
include "../connect.php";

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

$studentsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
$totalStudents = mysqli_fetch_assoc($studentsResult)['total'];

$nursesResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='nurse'");
$totalNurses = mysqli_fetch_assoc($nursesResult)['total'];

$doctorsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='doctor'");
$totalDoctors = mysqli_fetch_assoc($doctorsResult)['total'];

$today = date("Y-m-d");
$appointmentsResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date='$today'"
);
$todayAppointments = mysqli_fetch_assoc($appointmentsResult)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | SHRS</title>
   <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/nurse_dashboard.css">

</head>
<body>

<div class="container">

    <aside class="sidebar">
    <h2>SHRS Admin</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="users_management.php">User Management</a></li>
        <li><a href="student_management.php">Student Management</a></li>
        <li><a href="report.php">Reports</a></li>
        <li><a href="audit_logs.php">Audit Logs</a></li>
        <li class="divider">.....................</li>
        <li><a href="settings.php">Settings</a></li>
        <li><a href="/Mwaka.SHRS.2/logout.php" class="logout">Logout</a></li>
    </ul>
</aside>


    <main class="main">

        <header class="topbar">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></p>
        </header>

        <section class="cards">
            <div class="card">
                <h3>Total Students</h3>
                <p><?php echo $totalStudents; ?></p>
            </div>
            <div class="card">
                <h3>Total Nurses</h3>
                <p><?php echo $totalNurses; ?></p>
            </div>
            <div class="card">
                <h3>Total Doctors</h3>
                <p><?php echo $totalDoctors; ?></p>
            </div>
            <div class="card">
                <h3>Today's Appointments</h3>
                <p><?php echo $todayAppointments; ?></p>
            </div>
        </section>

        <section class="table-section">
            <h2>Recent Appointments</h2>
            <table>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Reg No</th>
                    <th>Date</th>
                    <th>Purpose</th>
                </tr>

                <?php
                $recentQuery = "
                    SELECT a.appointment_date, a.reason, s.full_name,s.reg_no
                    FROM appointments a
                    JOIN students s ON a.student_id = s.student_id
                    ORDER BY a.appointment_date DESC
                    LIMIT 5
                ";
                $recentResult = mysqli_query($conn, $recentQuery);

                $count = 1;
                if (mysqli_num_rows($recentResult) > 0) {
                    while ($row = mysqli_fetch_assoc($recentResult)) {
                        echo "<tr>
                                <td>{$count}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['reg_no']}</td>
                                <td>{$row['appointment_date']}</td>
                                <td>{$row['reason']}</td>
                              </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No appointments found</td></tr>";
                }
                ?>
            </table>
        </section>

    </main>
</div>

<script>
setTimeout(() => { location.reload(); }, 60000); 

</script>

</body>
</html>
