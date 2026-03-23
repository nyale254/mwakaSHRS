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
<div id="rescheduleModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Reschedule Appointment</h3>
    <form id="rescheduleForm">
      <label for="newDate">New Date & Time:</label>
      <input type="datetime-local" id="newDate" name="newDate" required>
      <input type="hidden" id="appointmentId">
      <input type="hidden" id="studentId">
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</div>
</body>
</html>
