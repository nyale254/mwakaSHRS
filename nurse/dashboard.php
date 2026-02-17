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
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'nurse') {
    header("Location: ../index.php");
    exit();
}

$studentsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
$totalStudents = mysqli_fetch_assoc($studentsResult)['total'];


$today = date("Y-m-d");
$appointmentsResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = '$today'"
);
$todayAppointments = mysqli_fetch_assoc($appointmentsResult)['total'];

$treatmentsResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM treatments WHERE treatment_date = '$today'"
);
$todayTreatments = mysqli_fetch_assoc($treatmentsResult)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nurse Dashboard | SHRS</title>
    <link rel="stylesheet" href="../styles/nurse_dashboard.css">
</head>
<body>

<div class="container">
    <aside class="sidebar" id="sidebar">
        <h2>SHRS</h2>
        <ul>
            <li><a href="nurse_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="student_list.php">Students</a></li>
            <li><a href="appointment.php">Appointments</a></li>
            <li><a href="vaccination.php">Vaccination</a></li>
            <li><a href="#">Treatment&Medications</a></li>
            <li><a href="report.php">Reports</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </aside>

    <main class="main">

        <header class="topbar">
            <div class="header-content">
                <div class="header-left">
                    <div class="logo-section">
                        <div class="logo-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div>
                            <h1>Student Health Records</h1>
                            <p class="subtitle">Nurse Dashboard</p>
                        </div>
                    </div>
                    <div class="search-box">
                        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="search" placeholder="Search students..." id="searchInput">
                    </div>
                </div>
                <div class="header-right">
                    <button class="icon-btn notification-btn" onclick="toggleNotifications()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="badge">
                            <?php 
                            $notifCountQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM notifications
                            WHERE user_id={$_SESSION['user_id']} AND status=0");
                            $notifCount = mysqli_fetch_assoc($notifCountQuery)['total'];
                            echo $notifCount;
                            ?>
                        </span>
                    </button>

                    <div class="notification-dropdown" id="notificationDropdown">
                        <h4>Notifications</h4>
                        <ul>
                            <?php
                            $notifQuery = mysqli_query($conn, "SELECT * FROM notifications 
                            WHERE user_id={$_SESSION['user_id']} 
                            ORDER BY created_at DESC LIMIT 5");
                            if(mysqli_num_rows($notifQuery) > 0) {
                                while($notif = mysqli_fetch_assoc($notifQuery)) {
                                    $readClass = $notif['status'] ? 'read' : 'unread';
                                    echo "<li class='$readClass'>{$notif['message']} <span class='time'>".date('d M H:i', strtotime($notif['created_at']))."</span></li>";
                                }
                            } else {
                                echo "<li>No notifications</li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="user-avatar">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                   </div>
                </div>
                
            </div>
            
        </header>

        <div class="cards">
            <div class="card">
                <h3>Total Students</h3>
                <p><?php echo $totalStudents; ?></p>
            </div>

            <div class="card">
                <h3>Today's Appointments</h3>
                <p><?php echo $todayAppointments; ?></p>
            </div>

            <div class="card">
                <h3>Treatments Today</h3>
                <p><?php echo $todayTreatments; ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Recent Student Records</h2>
            </div>

            <div class="card-content">
                <div class="table-container">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Reg_No</th>
                                <th>Allergies</th>
                                <th>Last Visit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                        <?php
                        $query = "
                            SELECT 
                                s.student_id,
                                s.full_name,
                                s.course,
                                s.reg_no,
                                c.allergies,
                                MAX(a.appointment_date) AS last_visit,
                                s.status
                            FROM students s
                            LEFT JOIN appointments a ON s.student_id = a.student_id
                            LEFT JOIN conditions_allergies c ON s.student_id = c.student_id
                            GROUP BY s.student_id
                            ORDER BY last_visit DESC
                        ";

                        $result = mysqli_query($conn, $query);

                        $count = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $lastVisit = $row['last_visit'] ?? 'Never';
                            $statusClass = ($row['status'] === 'Active') ? 'active' : 'inactive';

                            echo "
                            <tr class='clickable-row' data-id='{$row['student_id']}'>
                                <td>{$count}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['course']}</td>
                                <td>{$row['reg_no']}</td>
                                <td>{$row['allergies']}</td>
                                <td>{$lastVisit}</td>
                                <td><span class='status {$statusClass}'>{$row['status']}</span></td>
                                <td>View</td>
                            </tr>
                            ";
                            $count++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="content_rightside">
            <h2>Recent Appointments</h2>
            <table>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Reg No</th>
                    <th>Date</th>
                    <th>reason</th>
                </tr>

                <?php
                $recentQuery = "
                    SELECT a.appointment_date, a.reason, s.full_name, s.reg_no
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

        </div>

    </main>
</div>

<script src="/Mwaka.SHRS.2/scripts/nurse_dashboard.js"></script>

</body>
</html>


