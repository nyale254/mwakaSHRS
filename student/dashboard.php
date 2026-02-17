<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$studentQuery = "SELECT user_id, fullname FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $studentQuery);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    session_destroy();
    header("Location: ../index.php?error=student_not_found");
    exit();
}

$visits = $appointments = $treatments =  0; 

$visitsQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) total FROM visits WHERE user_id = $student_id"
);
if ($visitsQuery) {
    $visits = mysqli_fetch_assoc($visitsQuery)['total'] ?? 0;
} else {
    echo "Error in visits query: " . mysqli_error($conn);
}

$appointmentsQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) total FROM appointments WHERE user_id = $student_id"
);
if ($appointmentsQuery) {
    $appointments = mysqli_fetch_assoc($appointmentsQuery)['total'] ?? 0;
}

$notificationCount = 0;
$notifQuery = "SELECT COUNT(*) count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt2 = mysqli_prepare($conn, $notifQuery);
if (!$stmt2) {
    die("Prepare failed for notifications: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt2, "i", $student_id);
mysqli_stmt_execute($stmt2);
$notifResult = mysqli_stmt_get_result($stmt2);
$notificationCount = mysqli_fetch_assoc($notifResult)['count'] ?? 0;
mysqli_stmt_close($stmt2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/student_dashboard.css">
</head>
<body>

<div class="container">

    <aside class="sidebar">
        <h2>SHRS Student</h2>
        <div class="menu">
            <div class="menu-item">
                <a href="dashboard.php" class="active">Dashboard</a>
            </div>

            <div class="menu-item">
                <a href="profile.php">My Profile</a>
            </div>

            <div class="menu-item">
                <a href="appointment.php">Appointments</a>
            </div>

            <div class="menu-item">
                <a href="medical_history.php">Medical History</a>
            </div>

            <div class="menu-item">
                <a href="prescriptions.php">Prescriptions</a>
            </div>

            <div class="menu-divider"></div>

            <div class="menu-item">
                <a href="/Mwaka.SHRS.2/logout.php" class="logout">Logout</a>
            </div>
        </div>

         <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar"></div>
                <div class="user-info">
                    <div class="user-name"><?=htmlspecialchars($student['fullname']);?></div>
                    <div class="user-role">Student</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main">

        <header class="top-header">
            <div class="header-left">
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Welcome back,<?=htmlspecialchars($student['fullname']);?>
                ! Here's your health overview.</p>
            </div>
            
            <div class="header-right">
                <div class="search-bar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"></svg>
                    <input type="text" placeholder="Search...">
                </div>

                <button class="icon-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"></svg>
                    <span class="notification-badge">3</span>
                </button>
            </div>
        </header>

        <section class="cards">
            <div class="card">
                <h3>My Visits</h3>
                <p><?= $visits ?></p>
            </div>
            <div class="card">
                <div class="stat-label">Appointments</div>
                <div class="stat-value"><?= $appointments ?></div>
            </div>
        </section>

        <section class="table-section">
            <h2>Recent Medical Visits</h2>
            <table>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Complaint</th>
                    <th>Action Taken</th>
                </tr>

                <?php
                $recentVisits = mysqli_query(
                    $conn,
                    "SELECT visit_date, complain, action_taken
                     FROM visits
                     WHERE user_id = $student_id
                     ORDER BY visit_date DESC
                     LIMIT 5"
                );

                $count = 1;
                if (mysqli_num_rows($recentVisits) > 0) {
                    while ($row = mysqli_fetch_assoc($recentVisits)) {
                        echo "<tr>
                                <td>{$count}</td>
                                <td>{$row['visit_date']}</td>
                                <td>{$row['complaint']}</td>
                                <td>{$row['action_taken']}</td>
                              </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='4'>No medical records found</td></tr>";
                }
                ?>
            </table>
        </section>

        <div class="left-column">
            <div class="card">
                
                <div class="card-header">
                    <h3 class="card-title">Today's Schedule</h3>
                    <button class="btn-text" >View All</button>
                </div>
                <div class="card-content">
                    <div id="schedule-list" class="schedule-list"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today's Health Goals</h3>
                </div>
                <div class="card-content">
                    <div id="goals-list" class="goals-list"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Weekly Activity</h3>
                </div>
                <div class="card-content">
                    <div id="activity-chart" class="activity-chart"></div>
                </div>
            </div>
        </div>

        <div class="card-content">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="quick-actions-grid">
                <button class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>Book Appointment</span>
                </button>
                
                <button class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span>View Records</span>
                </button>
            </div>
        </div>

        <div class="card-content">
            <div class="card-header">
                <h3 class="card-title">Health Tips</h3>
            </div>
            <div class="health-tips">
                <div class="tip-item">
                    <div class="tip-icon">💧</div>
                    <div class="tip-content">
                        <h4>Stay Hydrated</h4>
                        <p>Drink at least 8 glasses of water daily</p>
                    </div>
                </div>
                <div class="tip-item">
                    <div class="tip-icon">🏃</div>
                    <div class="tip-content">
                        <h4>Daily Exercise</h4>
                        <p>30 minutes of activity keeps you healthy</p>
                    </div>
                </div>
                <div class="tip-item">
                    <div class="tip-icon">😴</div>
                    <div class="tip-content">
                        <h4>Quality Sleep</h4>
                        <p>Aim for 7-9 hours of sleep each night</p>
                    </div>
                </div>
            </div>
         </div>


    </main>
</div>

<script>
setTimeout(() => location.reload(), 60000);

document.querySelector('.notification')?.addEventListener('click', () => {
    fetch('mark_notifications_read.php');
});
</script>

</body>
</html>
