<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'nurse') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Nurse Dashboard | SHRS</title>
<link rel="stylesheet" href="/Mwaka.SHRS.2/styles/nurse_appointment.css">
</head>
<body>
<div class="table_container" >
    <h2>Nurse Appointment Dashboard</h2>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Date & Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="appointmentsTable"></tbody>
    </table>
</div>

</body>
</html>
