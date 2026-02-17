<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Appointments | SHRS</title>
<link rel="stylesheet" href="/Mwaka.SHRS.2/styles/appointment.css">
</head>
<body>
<div class="container">
    <h2>Book Appointment</h2>
    <div class="card">
        <form id="appointmentForm">
            <label>Appointment Date & Time</label>
            <input type="datetime-local" name="appointment_date" required>
            <label>Reason for Appointment</label>
            <textarea name="reason" rows="3" required></textarea>
            <button type="submit" class="btn">Request Appointment</button>
        </form>
    </div>

    <h3>My Appointments</h3>
    <div id="appointmentsList"></div>

    <h3>Notifications</h3>
    <div id="notifications" class="notifications"></div>
</div>

<script>
function fetchAppointments() {
    fetch('fetch_appointment.php')
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById('appointmentsList');
        list.innerHTML = '';
        data.forEach(a => {
            list.innerHTML += `
                <div class="card">
                    <p><strong>Date:</strong> ${a.appointment_date}</p>
                    <p><strong>Reason:</strong> ${a.reason}</p>
                    <p><strong>Status:</strong> 
                        <span class="status ${a.status}">${a.status.charAt(0).toUpperCase() + a.status.slice(1)}</span>
                    </p>
                </div>
            `;
        });
    });
}

function fetchNotifications() {
    fetch('fetch_notification.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('notifications');
        container.innerHTML = '';
        data.forEach(note => {
            container.innerHTML += `<p>${note.message} <small>(${note.created_at})</small></p>`;
        });
    });
}

setInterval(fetchAppointments, 5000);
setInterval(fetchNotifications, 5000);
fetchAppointments();
fetchNotifications();

document.getElementById('appointmentForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('submit_appointment.php', {method:'POST', body: formData})
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if(data.success) fetchAppointments();
    });
});
</script>
</body>
</html>
