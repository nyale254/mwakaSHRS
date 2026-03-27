<?php
session_start();
include "../connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

$student_id = $_POST['student_id'] ?? '';
$symptoms   = mysqli_real_escape_string($conn, $_POST['symptoms'] ?? '');
$referral   = mysqli_real_escape_string($conn, $_POST['referral'] ?? '');
$medications = $_POST['medication'] ?? [];
$dosage      = $_POST['prescribed_dosage'] ?? [];
$frequency   = $_POST['frequency'] ?? [];
$quantity    = $_POST['quantity'] ?? [];

if (empty($student_id)) {
    die("Student ID is required");
}

mysqli_begin_transaction($conn);

try {

    $query = "INSERT INTO treatments(student_id, symptoms, referral, treatment_date)
              VALUES('$student_id', '$symptoms', '$referral', NOW())";

    mysqli_query($conn, $query);
    $treatment_id = mysqli_insert_id($conn);

    for ($i = 0; $i < count($medications); $i++) {

        $med_id = (int)$medications[$i];
        $dose   = mysqli_real_escape_string($conn, $dosage[$i]);
        $freq   = mysqli_real_escape_string($conn, $frequency[$i]);
        $qty    = (int)$quantity[$i];

        if ($med_id <= 0 || $qty <= 0) {
            throw new Exception("Invalid medication or quantity.");
        }

        $check = mysqli_query($conn, "SELECT quantity_in_stock, status 
                                     FROM medication 
                                     WHERE id = '$med_id' FOR UPDATE");

        if (!$check || mysqli_num_rows($check) == 0) {
            throw new Exception("Medication not found (ID: $med_id)");
        }

        $row = mysqli_fetch_assoc($check);
        $current_stock = (int)$row['quantity_in_stock'];
        $status = $row['status'];

        if ($status == 'expired' || $status == 'discontinued') {
            throw new Exception("Medication ID $med_id is not usable ($status).");
        }

        if ($current_stock < $qty) {
            throw new Exception("Not enough stock for medication ID: $med_id");
        }

        $new_stock = $current_stock - $qty;

        mysqli_query($conn, "UPDATE medication 
                             SET quantity_in_stock = '$new_stock'
                             WHERE id = '$med_id'");

        if ($new_stock == 0) {
            mysqli_query($conn, "UPDATE medication 
                                 SET status = 'out_of_stock'
                                 WHERE id = '$med_id'");
        }

        mysqli_query($conn, "INSERT INTO medication_schedule(
            medication_id,
            prescribed_dosage,
            frequency,
            quantity
        ) VALUES(
            '$med_id',
            '$dose',
            '$freq',
            '$qty'
        )");
    }

    $visit_date = date('Y-m-d');
    $visit_time = date('H:i:s');

    $health_query = "INSERT INTO health_records(
        student_id,
        attended_by,
        visit_date,
        visit_time,
        complain,
        severity,
        diagnosis,
        notes,
        status,
        created_at
    ) VALUES(
        '$student_id',
        '$user_id',
        '$visit_date',
        '$visit_time',
        '$symptoms',
        '',
        '',
        '',
        'pending',
        NOW()
    )";

    mysqli_query($conn, $health_query);

    $action = "Save Treatment";
    $detail = "Saved treatment for student ID: $student_id with treatment ID: $treatment_id";
    $ip_address = $_SERVER['REMOTE_ADDR'];

    mysqli_query($conn, "INSERT INTO activity_log(
        user_id, action, detail, ip_address, created_at
    ) VALUES(
        '$user_id',
        '".mysqli_real_escape_string($conn,$action)."',
        '".mysqli_real_escape_string($conn,$detail)."',
        '$ip_address',
        NOW()
    )");

    mysqli_commit($conn);

    echo "Saved Successfully";

} catch (Exception $e) {

    mysqli_rollback($conn);

    echo "Error: " . $e->getMessage();
}
?>