<?php
session_start();
include "../connect.php";

if (!$conn) {
    die("Database connection failed");
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$course = isset($_GET['course']) ? $_GET['course'] : '';

$query = "SELECT * FROM students WHERE 1=1";

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $query .= " AND (full_name LIKE '%$search_safe%' OR reg_no LIKE '%$search_safe%')";
}

if (!empty($course)) {
    $course_safe = mysqli_real_escape_string($conn, $course);
    $query .= " AND course = '$course_safe'";
}

$query .= " ORDER BY full_name ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student List | SHRS</title>
    <link rel="stylesheet" href="../styles/student_list.css">
    
</head>
<body>
    <div class="topbar">
        <h1>Student List | SHRS</h1>
        <div>
            <a href="dashboard.php" class="btn1">Dashboard</a>
            <a href="../logout.php" class= "btn2">Logout</a>
        </div>
        <div>
            <form method="GET" class="filter-form">
            <input 
                type="text" 
                name="search" 
                placeholder="Search name or reg no"
                value="<?php echo htmlspecialchars($search); ?>"
            >
            <button type="submit" class="btn">Search</button>
            <a href="student_list.php" class="btn reset">Reset</a>
        </form>

        </div>
        
    </div>

    <div class="container">
        <p>Registered Student</p>
        <table>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Reg No</th>
                <th>Gender</th>
                <th>Course</th>
                <th>Action</th>
            </tr>

            <?php
            $count = 1;
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                     echo "<tr class='clickable-row' data-id='".$row['student_id']."'>
                        <td>".$count++."</td>
                        <td>".$row['full_name']."</td>
                        <td>".$row['reg_no']."</td>
                        <td>".$row['gender']."</td>
                        <td>".$row['course']."</td>
                        <td>
                            <a href='view_student.php?id=".$row['student_id']."' class='btn'>View</a>
                            <a href='treatment.php?id=".$row['student_id']."' class='btn primary'>Treat</a>
                        </td>
                    </tr>";

                }
            }else {
                echo "<tr><td colspan='6'>No records found</td></tr>";
            }
            ?>
        </table>

    </div>

<script>
    document.querySelectorAll(".clickable-row").forEach(row => {
        row.addEventListener("click", () => {
            const studentId = row.getAttribute("data-id");
            window.location.href = "view_student.php?id=" + studentId;
        });
    });
</script>

</body>
</html>
