<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    exit("Unauthorized");
}

$student_id = $_SESSION['user_id'];
?>

<div class="appointment-section">

    <h2 class="section-title">Book Appointment</h2>

    <div class="dashboard-card">
        <form id="appointmentForm" >
            <div class="form-group" >
                <label>Appointment Date & Time</label>
                <input type="datetime-local" name="appointment_date" required>
            </div>

            <div class="form-group">
                <label>Reason for Appointment</label>
                <textarea name="reason" rows="3" required></textarea>
            </div>

            <button type="submit" class="primary-btn">
                Request Appointment
            </button>
        </form>
    </div>

    <h3 class="sub-title">My Appointments</h3>
    <div id="appointmentsList"></div>

    <h3 class="sub-title">Notifications</h3>
    <div id="notifications" class="notifications-box"></div>

</div>