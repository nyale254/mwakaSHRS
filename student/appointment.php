<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];

$getStudent = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id=?");
mysqli_stmt_bind_param($getStudent, "i", $user_id);
mysqli_stmt_execute($getStudent);
$result = mysqli_stmt_get_result($getStudent);
$row = mysqli_fetch_assoc($result);
$student_id = $row ? $row['student_id'] : 0;
mysqli_stmt_close($getStudent);

$notifQuery = mysqli_query($conn, "
    SELECT * FROM notifications 
    WHERE user_id = '$user_id'
    ORDER BY created_at DESC
    LIMIT 5
");
?>

<div class="appointment-section">

    <h2 class="section-title">Book Appointment</h2>

    <div class="dashboard-card">
        <span class="close">&times;</span>
        <form id="appointmentForm"  >
            <div class="form-group">
                <label for="appointment_date">Appointment Date & Time</label>
                <input 
                    type="datetime-local" 
                    id="appointment_date" 
                    name="appointment_date" 
                    required
                    min="<?= date('Y-m-d\TH:i') ?>"  
                    class="form-control"
                >
                <small class="form-text">Select a date and time for your appointment (cannot be in the past).</small>
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