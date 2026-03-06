<?php
session_start();
include "../connect.php";

$id = $_GET['id'];

$query = "SELECT full_name, course FROM students WHERE student_id='$id'";

$result = mysqli_query($conn,$query);

$row = mysqli_fetch_assoc($result);

echo json_encode([
"name"=>$row['full_name'],
"course"=>$row['course']
]);

?>