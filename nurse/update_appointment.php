<?php
session_start();
include "../connect.php";

header('Content-Type: application/json');

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'nurse') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$input = json_decode(file_get_contents('php://input'), true);
$appointment_id = $input['appointment_id'] ?? 0;
$status = $input['status'] ?? ''; 
$new_date = $input['new_date'] ?? null; 

if(!$appointment_id || !in_array($status, ['confirmed', 'rejected', 'reschedule'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$getStudent = mysqli_prepare($conn, "SELECT student_id FROM appointments WHERE appointment_id=?");
mysqli_stmt_bind_param($getStudent, "i", $appointment_id);
mysqli_stmt_execute($getStudent);
$result = mysqli_stmt_get_result($getStudent);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($getStudent);

if(!$row){
    echo json_encode(['success'=>false,'message'=>'Student not found']);
    exit();
}

$student_id = $row['student_id'];
mysqli_begin_transaction($conn);

try {
    $res = mysqli_query($conn, "SELECT status, nurse_id FROM appointments WHERE appointment_id = $appointment_id FOR UPDATE");
    $appointment = mysqli_fetch_assoc($res);

    if(!$appointment){
        mysqli_rollback($conn);
        echo json_encode(['success'=>false,'message'=>'Appointment not found']);
        exit();
    }

    if(in_array($status, ['confirmed','rejected'])){
        if($appointment['status'] != 'pending'){
            mysqli_rollback($conn);
            echo json_encode(['success'=>false,'message'=>'Appointment has already been handled']);
            exit();
        }

        $new_status = $status === 'confirmed' ? 'confirmed' : 'rejected';
        $stmt = mysqli_prepare($conn, "UPDATE appointments SET status = ?, nurse_id = ? WHERE appointment_id = ?");
        mysqli_stmt_bind_param($stmt, "sii", $new_status, $user_id, $appointment_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $msg = $status === 'confirmed' ? "Your appointment has been confirmed." : "Your appointment has been rejected.";
        $notify = mysqli_prepare($conn, "INSERT INTO notifications (user_id, type, message) VALUES (?, 'appointment', ?)");
        mysqli_stmt_bind_param($notify, "is", $student_id, $msg);
        mysqli_stmt_execute($notify);
        mysqli_stmt_close($notify);

    }

    if($status === 'reschedule'){
        if($appointment['nurse_id'] != $user_id){
            mysqli_rollback($conn);
            echo json_encode(['success'=>false,'message'=>'You are not allowed to reschedule this appointment']);
            exit();
        }
        if(!$new_date){
            mysqli_rollback($conn);
            echo json_encode(['success'=>false,'message'=>'New date is required for rescheduling']);
            exit();
        }

        $stmt = mysqli_prepare($conn, "UPDATE appointments SET appointment_date = ?, reminder_sent = 0 WHERE appointment_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_date, $appointment_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $msg = "Your appointment has been rescheduled to $new_date.";
        $notify = mysqli_prepare($conn, "INSERT INTO notifications (user_id, type, message) VALUES (?, 'appointment', ?)");
        mysqli_stmt_bind_param($notify, "is", $student_id, $msg);
        mysqli_stmt_execute($notify);
        mysqli_stmt_close($notify);
    }
    if (in_array($status, ['confirmed', 'rejected', 'reschedule'])) {

        $stmt2 = mysqli_prepare($conn, "UPDATE appointments SET reminder_sent=0 WHERE appointment_id=?");
        mysqli_stmt_bind_param($stmt2, "i", $appointment_id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        $stmt3 = mysqli_prepare($conn, "
            SELECT s.full_name AS student_name, s.email AS student_email, a.appointment_date
            FROM appointments a
            JOIN students s ON a.student_id = s.student_id
            WHERE a.appointment_id = ?
        ");
        mysqli_stmt_bind_param($stmt3, "i", $appointment_id);
        mysqli_stmt_execute($stmt3);
        $result3 = mysqli_stmt_get_result($stmt3);
        $student = mysqli_fetch_assoc($result3);
        mysqli_stmt_close($stmt3);

        if ($student) {
            $studentEmail = $student['student_email'];
            $studentName = $student['student_name'];
            $appointmentTime = $student['appointment_date'];

            $status_for_mail = $status;
            require __DIR__ . '/send_mail.php';
        }
    }

    mysqli_commit($conn);
    echo json_encode(['success'=>true,'message'=>"Appointment successfully $status"]);

} catch (Exception $e){
    mysqli_rollback($conn);
    echo json_encode(['success'=>false,'message'=>'Error handling appointment: '.$e->getMessage()]);
}
?>