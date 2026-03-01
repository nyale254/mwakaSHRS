<?php
session_start();
include "../connect.php"; 

$today = date("Y-m-d");

$dueTodayQuery = "SELECT COUNT(*) as total FROM medication_schedule WHERE start_date='$today' AND status='scheduled'";
$dueTodayResult = mysqli_query($conn, $dueTodayQuery);
$dueToday = mysqli_fetch_assoc($dueTodayResult)['total'] ?? 0;

$lowStockQuery = "SELECT COUNT(*) as total FROM medications WHERE quantity_in_stock <= reorder_level";
$lowStockResult = mysqli_query($conn, $lowStockQuery);
$lowStock = mysqli_fetch_assoc($lowStockResult)['total'] ?? 0;

$givenTodayQuery = "SELECT COUNT(*) as total FROM medication_schedule WHERE start_date='$today' AND status='given'";
$givenTodayResult = mysqli_query($conn, $givenTodayQuery);
$givenToday = mysqli_fetch_assoc($givenTodayResult)['total'] ?? 0;

$medsQuery = "
SELECT ms.id, u.fullname, m.name AS med_name, m.dosage, ms.administration_time, ms.status
FROM medication_schedule ms
JOIN users u ON ms.student_id=u.user_id
JOIN medications m ON ms.medication_id=m.id
WHERE ms.start_date='$today'
ORDER BY ms.administration_time ASC
";
$medsResult = mysqli_query($conn, $medsQuery);

$lowStockItemsQuery = "SELECT name, quantity_in_stock, expiry_date FROM medications WHERE quantity_in_stock <= reorder_level";
$lowStockItems = mysqli_query($conn, $lowStockItemsQuery);

$authQuery = "SELECT m.name AS med_name, a.dosage, u.fullname AS doctor, a.status 
FROM authorizations a 
JOIN medications m ON a.med_id=m.med_id
JOIN users u ON a.doctor_id=u.user_id
WHERE a.status='active'";
$authResult = mysqli_query($conn, $authQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHRS · Medication Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/admin_medi.css">
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div class="logo-area">
                <div class="logo-icon">SHRS</div>
                <span class="logo-text">SHRS <span style="font-weight:400; color:#7999b3;">| Medication</span></span>
                <div class="badge"><i class="fas fa-shield-alt"></i> school nurse · live</div>
            </div>
            <div class="date-badge"><i class="far fa-calendar-alt"></i> <?= date("D, M d · h:i A"); ?></div>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-paste"></i></div>
                <div class="stat-content">
                    <h4>due today</h4>
                    <span class="number"><?= $dueToday ?></span><span class="unit">tasks</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-prescription-bottle"></i></div>
                <div class="stat-content">
                    <h4>low stock</h4>
                    <span class="number"><?= $lowStock ?></span><span class="unit">items</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <h4>given today</h4>
                    <span class="number"><?= $givenToday ?></span><span class="unit">doses</span>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="panel-left">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list-check" style="color:#1e81c4;"></i> Today's medication schedule</h2>
                    </div>
                    <table class="med-table">
                        <thead>
                            <tr><th>Student</th><th>Medication</th><th>Time</th><th>Status</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php while($med = mysqli_fetch_assoc($medsResult)): ?>
                                <tr>
                                    <td><div class="student"><span class="avatar"><?= strtoupper(substr($med['fullname'],0,2)) ?></span> <?= $med['fullname'] ?></div></td>
                                    <td><span class="med-name"><?= $med['med_name'] ?></span> <span class="dosage"><?= $med['dosage'] ?></span></td>
                                    <td><?= date("h:i A", strtotime($med['schedule_time'])) ?></td>
                                    <td>
                                        <?php
                                            $statusClass = $med['status'] == 'given' ? 'status-badge' : 'status-badge warning';
                                            $icon = $med['status']=='given'?'<i class="fas fa-check"></i>':'<i class="fas fa-hourglass-half"></i>';
                                        ?>
                                        <span class="<?= $statusClass ?>"><?= $icon ?> <?= $med['status'] ?></span>
                                    </td>
                                    <td><a href="#" class="action-link">details</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div class="inventory-alert">
                        <div class="alert-left">
                            <i class="fas fa-boxes"></i>
                            <div class="alert-text">
                                <strong>Low stock alert · <?= mysqli_num_rows($lowStockItems) ?> items need reorder</strong>
                                <p>
                                    <?php
                                    $items = [];
                                    while($item=mysqli_fetch_assoc($lowStockItems)){
                                        $items[] = $item['name'].' ('.$item['quantity'].' left, exp. '.date("m/Y", strtotime($item['expiry_date'])).')';
                                    }
                                    echo implode(', ', $items);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="panel-right">
                <div class="auth-card">
                    <div class="auth-title"><i class="fas fa-notes-medical"></i> Emergency / Quick Access</div>
                    <div class="auth-meta">
                        <span>⚠️ severe allergies (2)</span>
                        <span>⬆️ view all</span>
                    </div>
                    <div class="doc-note">
                        <i class="fas fa-id-card"></i> <strong>A. Chen (Gr.3)</strong> · EpiPen in health office · nut/peanut
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        setInterval(()=> location.reload(), 120000);
    </script>
</body>
</html>