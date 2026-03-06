<?php
session_start();
include "../connect.php";
$id = $_GET['id'];
$query = "SELECT created_at,diagnosis FROM treatments WHERE student_id='$id'";
$result = mysqli_query($conn,$query);

$data=[];

while($row=mysqli_fetch_assoc($result)){

$data[]=[
"created_at"=>$row['created_at'],
"diagnosis"=>$row['diagnosis'],
"treatment"=>"Medication Given"
];

}

echo json_encode($data);

?>