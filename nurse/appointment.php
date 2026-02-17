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
<link rel="stylesheet" href="/Mwaka.SHRS.2/styles/appointment.css">
</head>
<body>
<div class="table_container">
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

<script>
function fetchAppointments() {
    fetch('fetch_appointment.php')
    .then(res => res.json())
    .then(data => {
        const table = document.getElementById('appointmentsTable');
        table.innerHTML = '';
        data.forEach(a => {
            table.innerHTML += `
            <tr id="appointment-${a.appointment_id}">
                <td>${a.student_name}</td>
                <td>${a.appointment_date}</td>
                <td>${a.reason}</td>
                <td><span class="status ${a.status}">${a.status.charAt(0).toUpperCase() + a.status.slice(1)}</span></td>
                <td>
                    ${a.status === 'pending' ? `
                        <button class="btn confirm" onclick="updateStatus(${a.appointment_id}, 'confirmed', ${a.student_id})">Confirm</button>
                        <button class="btn reject" onclick="updateStatus(${a.appointment_id}, 'rejected', ${a.student_id})">Reject</button>
                    ` : '-'}
                </td>
            </tr>`;
        });
    });
}

function updateStatus(appointmentId, status, studentId) {
    if(!confirm(`Are you sure you want to ${status} this appointment?`)) return;

    fetch('update_appointment.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({appointment_id:appointmentId, status:status, student_id:studentId})
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.success) fetchAppointments();
        else alert('Error: '+data.message);
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
fetchAppointments();
</script>
</body>
</html>
