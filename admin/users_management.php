<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../connect.php';

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/user_management.css">
</head>
<body>

<h2>User Management</h2>

<a href="add_user.php" class="btn">+ Add New User</a>
<a href="javascript:history.back()" class="back-btn">← Back</a>

<table>
    <tr>
        <th>#</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    $i = 1; 
    while ($row = mysqli_fetch_assoc($result)) :
    ?>
    <tr>
        <td><?= $i++; ?></td>
        <td><?= htmlspecialchars($row['username']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><?= ucfirst(htmlspecialchars($row['role'])); ?></td>
        <td><?= htmlspecialchars($row['status']); ?></td>
        <td>
            <a href="edit_user.php?id=<?= $row['user_id']; ?>">Edit</a>
            <a href="delete.php?table=users&id=<?= $row['user_id']; ?>"
              onclick="return confirmDelete('<?= htmlspecialchars($row['username']); ?>')"
              class="delete-btn">
              Delete
            </a>

            <a href="disable_user.php?id=<?= $row['user_id']; ?>">Disable</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
function confirmDelete(username) {
    return confirm("Are you sure you want to delete the user: " + username + "?");
}
</script>

</body>
</html>
