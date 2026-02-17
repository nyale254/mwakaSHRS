<?php
session_start();
include "../connect.php";

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); 
    exit();
}

$nurse_name = "Nurse";

if (isset($_SESSION['user_id'])) {
    $stmt = mysqli_prepare($conn, "SELECT fullname FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $db_name);

    if (mysqli_stmt_fetch($stmt)) {
        $nurse_name = $db_name;
    }
    mysqli_stmt_close($stmt);
}
$today = date('Y-m-d');
$month = date('m');
$year  = date('Y');

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM students");
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalPatients);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM vaccinations WHERE DATE(vaccination_date)=?");
mysqli_stmt_bind_param($stmt, "s", $today);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $vaccToday);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM vaccinations WHERE MONTH(vaccination_date)=? AND YEAR(vaccination_date)=?");
mysqli_stmt_bind_param($stmt, "ii", $month, $year);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $monthVacc);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SHRS | Nurse Vaccination Dashboard</title>
<link rel="stylesheet" href="/Mwaka.SHRS.2/styles/vaccination.css">
</head>

<body>
    <header class="topbar">
        <div class="header-content">
            <div class="logo-text">
                <h1>SHRS| Nurse Vaccination Management panel</h1>
            </div>
            <div class='btn'>
                <a href="dashboard.php" class="btn primary_btn">Back</a>
                <a href="../logout.php" class="btn secondary_btn">Logout</a>
            </div>
            <div class="header-actions">
                <div class="user-profile">
                    <div class="user-avatar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="user-info">
                        <p>Nurse <?php echo htmlspecialchars($nurse_name); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <div>
                    <h2>Welcome Nurse, <?php echo htmlspecialchars($nurse_name); ?></h2>
                </div>
            </div>
        </section>   
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <p>Total Patients</p>
                        <h2><?php echo $totalPatients ?? 0; ?></h2>
                    </div>
                    <div class="stat-icon icon-blue">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

             <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <p>Vaccinations Today</p>
                        <h2><?php echo $vaccToday ?? 0; ?></h2>
                    </div>
                    <div class="stat-icon icon-green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <p>This Month</p>
                        <h2><?php echo $monthVacc ?? 0; ?></h2>
                    </div>
                    <div class="stat-icon icon-orange">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </section>

        <div class="main-grid">
            <section class="card">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3>Today's Schedule (<?php echo date("M d, Y"); ?>)</h3>
                </div>

                <?php
                $stmt = mysqli_prepare($conn, "
                    SELECT 
                        s.full_name,
                        a.updated_at, 
                        v.vaccine_name, 
                        a.status, 
                        a.reason
                    FROM appointments a
                    JOIN students s ON a.student_id = s.student_id
                    LEFT JOIN vaccinations v ON a.appointment_id = v.appointment_id
                    WHERE a.reason = ? 
                    ORDER BY a.updated_at ASC
                ");

                $reason = "vaccination";

                mysqli_stmt_bind_param($stmt, "s", $reason);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                ?>
                    <div class="schedule-item">
                        <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                        <span><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></span>
                        <span><?php echo htmlspecialchars($row['room']); ?></span>
                        <span><?php echo htmlspecialchars($row['vaccine_type']); ?></span>
                        <span><?php echo htmlspecialchars($row['status']); ?></span>
                    </div>
                <?php
                    }
                } else {
                    echo "<p>No appointments today.</p>";
                }
                mysqli_stmt_close($stmt);
                ?>
            </section>

            <section class="card">
                 <div class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Vaccine Inventory
                    </div>
                <?php
                $inventory = mysqli_query($conn, "SELECT * FROM vaccine_inventory");

                while($inv = mysqli_fetch_assoc($inventory)){

                    $available = $inv['quantity_available'];
                    $total     = $inv['total_quantity'];
                    $percentage = ($total > 0) ? ($available/$total)*100 : 0;
                ?>

                <div class="inventory-item">
                    <strong><?php echo htmlspecialchars($inv['vaccine_name']); ?></strong>
                    <p><?php echo $available; ?> / <?php echo $total; ?></p>

                    <div style="background:#eee;height:10px;border-radius:5px;">
                        <div style="width:<?php echo $percentage; ?>%;
                                    height:10px;
                                    background:
                                    <?php echo ($percentage < 20) ? 'red' : (($percentage < 50) ? 'orange' : 'green'); ?>;
                                    border-radius:5px;">
                        </div>
                    </div>

                    <small>Expires: <?php echo date("M Y", strtotime($inv['expiry_date'])); ?></small>
                </div>

                <?php } ?>

            </section>

        </div>

    </main>

</body>
</html>