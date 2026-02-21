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
    "SELECT COUNT(*) total FROM visits WHERE student_id = $student_id"
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
</head>
<body>

<div class="container">
    <main class="main">
        <section class="topbar">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Welcome back,<?=htmlspecialchars($student['fullname']);?>
                    ! Here's your health overview.</p>
                </div>
                <div class="nav">
                    <a href="profile.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                            <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="appointment.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                            <path d="M509-269q-29-29-29-71t29-71q29-29 71-29t71 29q29 29 29 71t-29 71q-29 29-71 29t-71-29ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/>
                        </svg>
                        Book Appointmentb
                    </a>
                    <a href="../logout.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                            <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                        </svg>
                        Logout
                    </a>
                </div>
                
                <div class="header-right">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search students..." />
                        <button class="searchBtn" id="searchBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </button>
                    </div>

                    <button  id="notificationBtn" class="icon-btn"onclick="toggleNotifications()" >
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                            <path d="M160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z"/>
                        </svg>
                        <span class="notification-badge" id="notificationBadge">
                            <?php 
                            $notifCountQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM notifications
                            WHERE user_id={$_SESSION['user_id']} AND status=0");
                            $notifCount = mysqli_fetch_assoc($notifCountQuery)['total'];
                            echo $notifCount;
                            ?>
                        </span>
                    </button>
                    <div id="notifDropdown" class="notif-dropdown" style="display:none;">
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
                </div>
            </header>

        </section>

        <section class="main_content">
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
                        <th>Diagnosis</th>
                    </tr>

                    <?php
                    $recentVisits = mysqli_query(
                        $conn,
                        "SELECT visit_date, complain, diagnosis
                        FROM visits
                        WHERE student_id = $student_id
                        ORDER BY visit_date DESC
                        LIMIT 5"
                    );

                    $count = 1;
                    if (mysqli_num_rows($recentVisits) > 0) {
                        while ($row = mysqli_fetch_assoc($recentVisits)) {
                            echo "<tr>
                                    <td>{$count}</td>
                                    <td>{$row['visit_date']}</td>
                                    <td>{$row['complain']}</td>
                                    <td>{$row['diagnosis']}</td>
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
                    <a class="quick-action-btn"  href="appointment.php">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>Book Appointment</span>
                    </a>
                    
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

        </section>
        

    </main>
</div>

<script src="/Mwaka.SHRS.2/scripts/student.js"></script>

</body>
</html>
