<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/PHPMailer-PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/PHPMailer-PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/PHPMailer-PHPMailer/src/SMTP.php';

if (!isset($studentEmail, $studentName, $appointmentTime, $status_for_mail)) {
    return; 
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'titusnyale1@gmail.com';
    $mail->Password   = 'gszs lpsp ovpk dzxh'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('noreply@shrs.com', 'SHRS');
    $mail->addAddress($studentEmail, $studentName);
    $mail->isHTML(true);

    switch($status_for_mail){
        case 'confirmed':
            $subject = "Appointment Confirmed";
            $body = "
                Hello $studentName,<br><br>
                Your appointment has been <b>confirmed</b>.<br>
                Time: $appointmentTime<br><br>
                Thank you.
            ";
            break;

        case 'rejected':
            $subject = "Appointment Rejected";
            $body = "
                Hello $studentName,<br><br>
                Your appointment on <b>$appointmentTime</b> has been <b>rejected</b>.<br>
                Please contact the clinic for more details.<br><br>
                Thank you.
            ";
            break;

        case 'reschedule':
            $subject = "Appointment Rescheduled";
            $body = "
                Hello $studentName,<br><br>
                Your appointment has been <b>rescheduled</b>.<br>
                New Time: $appointmentTime<br><br>
                Thank you.
            ";
            break;

        default:
            $subject = "Appointment Update";
            $body = "Hello $studentName,<br><br>Your appointment has been updated.<br><br>Thank you.";
            break;
    }

    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->send();

} catch (Exception $e) {
    error_log("Mail Error: " . $mail->ErrorInfo);
}
?>

