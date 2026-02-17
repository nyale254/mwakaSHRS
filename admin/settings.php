<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/settings.css">
</head>
<body>

<h2>System Settings</h2>
<?php if (isset($_GET['success'])) { ?>
    <p style="color: green;">Settings saved successfully.</p>
<?php } ?>


<form method="POST" action="save_settings.php" class="settings-form">

    <fieldset>
        <legend>General Settings</legend>

        <label>System Name</label>
        <input type="text" name="system_name" value="EduHealth System">

        <label>Institution Name</label>
        <input type="text" name="institution" value="SHRS">

        <label>Contact Email</label>
        <input type="email" name="contact_email" value="admin@shrs.com">
    </fieldset>

    <fieldset>
        <legend>Security Settings</legend>

        <label>Session Timeout (minutes)</label>
        <input type="number" name="session_timeout" value="30">

        <label>
            <input type="checkbox" name="allow_registration" checked>
            Allow User Registration
        </label>
    </fieldset>

    <fieldset>
        <legend>System Controls</legend>

        <label>
            <input type="checkbox" name="maintenance_mode">
            Enable Maintenance Mode
        </label>
    </fieldset>

    <button type="submit" class="btn-save">Save Settings</button>
    <a href="dashboard.php" class="cancel">Cancel</a>

</form>

</body>
</html>
