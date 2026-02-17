<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../connect.php';

$system_name        = trim($_POST['system_name']);
$institution        = trim($_POST['institution']);
$contact_email      = trim($_POST['contact_email']);
$session_timeout    = (int) $_POST['session_timeout'];
$allow_registration = isset($_POST['allow_registration']) ? 'yes' : 'no';
$maintenance_mode   = isset($_POST['maintenance_mode']) ? 'on' : 'off';

function saveSetting($conn, $name, $value) {
    $sql = "INSERT INTO system_settings (setting_name, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $name, $value);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

saveSetting($conn, 'system_name', $system_name);
saveSetting($conn, 'institution', $institution);
saveSetting($conn, 'contact_email', $contact_email);
saveSetting($conn, 'session_timeout', $session_timeout);
saveSetting($conn, 'allow_registration', $allow_registration);
saveSetting($conn, 'maintenance_mode', $maintenance_mode);

mysqli_query($conn, "
    INSERT INTO audit_logs (user, action)
    VALUES ('{$_SESSION['username']}', 'Updated system settings')
");

header("Location: dashboard.php?success=1");
exit();
