<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../connect.php';

$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$sql = "SELECT * FROM audit_logs WHERE 1=1"; 

if ($search !== '') {
    $sql .= " AND (user LIKE '%$search%' OR action LIKE '%$search%')";
}

if ($start_date !== '' && $end_date !== '') {
    $sql .= " AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Logs | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/audit_logs.css">
</head>
<body>

<h2>Audit Logs</h2>

<form method="GET" class="filter-form">
    <input type="text" name="search" placeholder="Search user or action"
           value="<?= htmlspecialchars($search); ?>">

    <input type="date" name="start_date" value="<?= $start_date; ?>">
    <input type="date" name="end_date" value="<?= $end_date; ?>">

    <button type="submit">Filter</button>
    <a href="audit_logs.php" class="reset">Reset</a>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Action</th>
        <th>IP Address</th>
        <th>Date & Time</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?= $row['logs_id']; ?></td>
        <td><?= htmlspecialchars($row['user']); ?></td>
        <td><?= htmlspecialchars($row['action']); ?></td>
        <td><?= $row['ip_address']; ?></td>
        <td><?= $row['created_at']; ?></td>
    </tr>
    <?php } ?>

</table>

</body>
</html>
