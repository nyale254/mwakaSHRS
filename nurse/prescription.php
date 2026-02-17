<?php
$conn = mysqli_connect("localhost", "root", "", "SHRS_db");
if (!$conn) {
    die("Database connection failed");
}

// Search by student
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT p.*, s.full_name, s.reg_no
          FROM prescriptions p
          JOIN students s ON p.student_id = s.id
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (s.full_name LIKE '%$search%' OR s.reg_no LIKE '%$search%')";
}

$query .= " ORDER BY p.date_given DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription & Treatment | SHRS</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<div class="container">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <h2>SHRS</h2>
        <ul>
            <li><a href="nurse_dashboard.php">Dashboard</a></li>
            <li><a href="student_list.php">Students</a></li>
            <li><a href="appointments.php">Appointments</a></li>
            <li><a href="prescriptions.php" class="active">Prescriptions</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- Main -->
    <main class="main">
        <header class="topbar">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <h1>Prescriptions & Treatments</h1>
        </header>

        <!-- Search -->
        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search student name or reg no" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn">Search</button>
            <a href="prescriptions.php" class="btn reset">Reset</a>
        </form>

        <!-- Prescription Table -->
        <section class="table-section">
            <table>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Reg No</th>
                    <th>Date</th>
                    <th>Medication / Treatment</th>
                    <th>Dosage / Instructions</th>
                    <th>Action</th>
                </tr>

                <?php
                $count = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>".$count++."</td>
                                <td>".$row['full_name']."</td>
                                <td>".$row['reg_no']."</td>
                                <td>".$row['date_given']."</td>
                                <td>".$row['medication']."</td>
                                <td>".$row['instructions']."</td>
                                <td>
                                    <a href='view_prescription.php?id=".$row['id']."' class='btn'>View</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No records found</td></tr>";
                }
                ?>
            </table>
        </section>

        <!-- Add New Prescription -->
        <div style="margin-top:15px;">
            <a href="add_prescription.php" class="btn">Add New Prescription</a>
        </div>

    </main>
</div>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}
</script>

</body>
</html>
