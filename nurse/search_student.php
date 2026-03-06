<?php
include "../connect.php";
$name = $_GET['name'];
$name = mysqli_real_escape_string($conn,$name);
$query = "SELECT student_id, full_name, course
          FROM students 
          WHERE full_name LIKE '%$name%' 
          LIMIT 10";

$result = mysqli_query($conn,$query);

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}
echo json_encode($data);
?>