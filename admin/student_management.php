<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$search = $_GET['search'] ?? '';

$query = "
    SELECT student_id, full_name, reg_no, gender, course, created_at
    FROM students
    WHERE full_name LIKE ? OR reg_no LIKE ?
    ORDER BY created_at DESC
";

$stmt = mysqli_prepare($conn, $query);
$searchTerm = "%$search%";
mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/student_management.css">
</head>
<body>

<div class="topbar-admin">
    <div class="topbar-left">
        <a href="dashboard.php">Dashboard</a>
    </div>

    <div class="topbar-center">
        <a href="add_student.php" class="btn-add">+ Add Student</a>
    </div>

    <div class="topbar-right">
        <span class="admin-name">
            <?= htmlspecialchars($_SESSION['fullname']) ?>
        </span>
        <a href="/Mwaka.SHRS.2/logout.php" class="btn-logout">Logout</a>
    </div>
</div>

<div class="page-container">

    <h2>Student Management</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by name or reg no"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <table class="data-table">
        <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Reg No</th>
            <th>Gender</th>
            <th>Course</th>
            <th>Registered On</th>
            <th>Actions</th>
        </tr>

        <?php
        $count = 1;
        if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
        ?>
        <tr>
            <td><?= $count++ ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['reg_no']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['course']) ?></td>
            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
            <td class="actions">
                <a href="view_student.php?id=<?= $row['student_id'] ?>">View</a>
                <a href="edit_student.php?id=<?= $row['student_id'] ?>">Edit</a>
                <a href="delete.php?id=<?= $row['student_id'] ?>"
                   onclick="return confirm('Delete this student?')"
                   class="danger">Delete</a>
            </td>
        </tr>
        <?php
            endwhile;
        else:
        ?>
        <tr>
            <td colspan="7">No students found</td>
        </tr>
        <?php endif; ?>
    </table>

</div>
<script>
    function confirmDelete(name) {
    return confirm("Are you sure you want to delete " + name + "?");
}

</script>
</body>
</html>
