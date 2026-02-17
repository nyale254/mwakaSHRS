<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Action Cancelled | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/cancel.css">
</head>
<body>

<div class="cancel-box">
    <h2>Action Cancelled</h2>
    <p>No changes were saved.</p>

    <a href="dashboard.php">Go to Dashboard</a>
    <a href="user_management.php">Back to User Management</a>
</div>

<script>
function confirmCancel() {
    const confirmAction = confirm(
        "Are you sure you want to cancel?\nAny unsaved changes will be lost."
    );

    if (confirmAction) {
        window.location.href = "cancel.php";
    }
}
</script>

</body>
</html>
