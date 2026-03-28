<?php
include "../connect.php";

$result = mysqli_query($conn, "SELECT id, name FROM medications WHERE status='active'");

$medications = [];

while($row = mysqli_fetch_assoc($result)){
    $medications[] = $row;
}

echo json_encode($medications);