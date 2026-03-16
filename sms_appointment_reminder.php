<?php

include "../connect.php";
include "/Mwaka.SHRS.2/sms service/sms_service.php";

date_default_timezone_set("Africa/Nairobi");

$now = date("Y-m-d H:i:s");
$window = date("Y-m-d H:i:s", strtotime("+30 minutes"));

$query = "SELECT a.appointment_id, a.appointment_time, s.phone, s.full_name, a.student_id
          FROM appointments a
          JOIN students s ON a.student_id = s.student_id
          WHERE a.status='confirmed'
          AND a.reminder_sent = 0
         AND a.appointment_date BETWEEN '$now' AND '$window'";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)){
    $message = "Hello {$row['full_name']}, reminder that you have a health appointment at {$row['appointment_date']}.";

    sendSMS($row['phone'], $message);

    mysqli_query($conn, "UPDATE appointments SET reminder_sent = 1 WHERE appointment_id={$row['appointment_id']}");
}

?>