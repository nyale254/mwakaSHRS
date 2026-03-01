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
            <li><a href="dashboard.php" class="active">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="M480-340q33 0 56.5-23.5T560-420q0-33-23.5-56.5T480-500q-33 0-56.5 23.5T400-420q0 33 23.5 56.5T480-340ZM160-120q-33 0-56.5-23.5T80-200v-440q0-33 23.5-56.5T160-720h160v-80q0-33 23.5-56.5T400-880h160q33 0 56.5 23.5T640-800v80h160q33 0 56.5 23.5T880-640v440q0 33-23.5 56.5T800-120H160Zm0-80h640v-440H160v440Zm240-520h160v-80H400v80ZM160-200v-440 440Z"/>
                </svg>
                Dashboard
            </a></li>

            <li><a href="student_list.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/>
                </svg>
                Students
            </a></li>

            <li><a href="#" class="nav-link" data-page="appointment.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="M509-269q-29-29-29-71t29-71q29-29 71-29t71 29q29 29 29 71t-29 71q-29 29-71 29t-71-29ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/>
                </svg>
                Appointments
            </a></li>

            <li><a href="vaccination.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="m320-60-80-60v-160h-40q-33 0-56.5-23.5T120-360v-300q-17 0-28.5-11.5T80-700q0-17 11.5-28.5T120-740h120v-60h-20q-17 0-28.5-11.5T180-840q0-17 11.5-28.5T220-880h120q17 0 28.5 11.5T380-840q0 17-11.5 28.5T340-800h-20v60h120q17 0 28.5 11.5T480-700q0 17-11.5 28.5T440-660v300q0 33-23.5 56.5T360-280h-40v220ZM200-360h160v-60h-70q-12 0-21-9t-9-21q0-12 9-21t21-9h70v-60h-70q-12 0-21-9t-9-21q0-12 9-21t21-9h70v-60H200v300ZM600-80q-33 0-56.5-23.5T520-160v-260q0-29 10-48t21-33q11-14 20-22.5t9-16.5v-20q-17 0-28.5-11.5T540-600q0-17 11.5-28.5T580-640h200q17 0 28.5 11.5T820-600q0 17-11.5 28.5T780-560v20q0 8 10 18t22 24q11 14 19.5 33t8.5 45v260q0 33-23.5 56.5T760-80H600Zm0-320h160v-20q0-15-9-26t-20-24q-11-13-21-29t-10-41v-20h-40v20q0 24-9.5 40T630-471q-11 13-20.5 24.5T600-420v20Zm0 120h160v-60H600v60Zm0 120h160v-60H600v60Zm0-120h160-160Z"/>
                </svg>
                Vaccination
            </a></li>

            <li><a href="xamp.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="M420-260h120v-100h100v-120H540v-100H420v100H320v120h100v100ZM280-120q-33 0-56.5-23.5T200-200v-440q0-33 23.5-56.5T280-720h400q33 0 56.5 23.5T760-640v440q0 33-23.5 56.5T680-120H280Zm0-80h400v-440H280v440Zm-40-560v-80h480v80H240Zm40 120v440-440Z"/>
                </svg>
                Treatments
            </a></li>
 
            <li><a href="#" class="nav-link" data-page="medication.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="M345-120q-94 0-159.5-65.5T120-345q0-45 17-86t49-73l270-270q32-32 73-49t86-17q94 0 159.5 65.5T840-615q0 45-17 86t-49 73L504-186q-32 32-73 49t-86 17Zm266-286 107-106q20-20 31-47t11-56q0-60-42.5-102.5T615-760q-29 0-56 11t-47 31L406-611l205 205ZM345-200q29 0 56-11t47-31l106-107-205-205-107 106q-20 20-31 47t-11 56q0 60 42.5 102.5T345-200Z"/>
                </svg>
                Medications
            </a></li>

            <li><a href="report.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q13-36 43.5-58t68.5-22q38 0 68.5 22t43.5 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm80-80h280v-80H280v80Zm0-160h400v-80H280v80Zm0-160h400v-80H280v80Zm221.5-198.5Q510-807 510-820t-8.5-21.5Q493-850 480-850t-21.5 8.5Q450-833 450-820t8.5 21.5Q467-790 480-790t21.5-8.5ZM200-200v-560 560Z"/>
                </svg>
                Reports
            </a></li>

            <li><a href="../logout.php" class="logout">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                </svg>
                Logout
            </a></li>
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
                    <button class="icon-btn" id ="notification-btn" onclick="toggleNotifications()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
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

        <div class="content" id="main-content">
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
        </div>

    </main>
</div>

<script src="/Mwaka.SHRS.2/scripts/nurse_dashboard.js"></script>

</body>
</html>


