<?php
session_start();
include "../connect.php";

if (isset($_POST['add_medication'])) {

    $names = $_POST['med_name'];
    $dosages = $_POST['dosage'];
    $quantities = $_POST['quantity'];
    $max_stocks = $_POST['max_stock_level'];
    $reorders = $_POST['reorder_level'];
    $suppliers = $_POST['supplier'];
    $batches = $_POST['batch_number'];
    $locations = $_POST['storage_location'];
    $expiries = $_POST['expiry_date'];

    $created_by = $_SESSION['user_id'] ?? null;
    $fullname = $_SESSION['fullname'] ?? 'Unknown';

    mysqli_begin_transaction($conn);

    try {

        for ($i = 0; $i < count($names); $i++) {

            $name = $names[$i];
            $dosage = $dosages[$i];
            $qty = $quantities[$i];
            $max = $max_stocks[$i];
            $reorder = $reorders[$i];
            $supplier = $suppliers[$i];
            $batch = $batches[$i];
            $location = $locations[$i];
            $expiry = $expiries[$i];

            // 🔥 Status logic
            $today = date("Y-m-d");
            if ($expiry < $today) {
                $status = "expired";
            } elseif ($qty == 0) {
                $status = "out_of_stock";
            } else {
                $status = "active";
            }

            $stmt = mysqli_prepare($conn, "
                INSERT INTO medications 
                (name, dosage, form, unit, quantity_in_stock, reorder_level, max_stock_level, batch_number, expiry_date, supplier, storage_location, status, created_by)
                VALUES (?, ?, 'tablet', 'pcs', ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "ssiiisssssi",
                $name,
                $dosage,
                $qty,
                $reorder,
                $max,
                $batch,
                $expiry,
                $supplier,
                $location,
                $status,
                $created_by
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // 🔥 Log each medication
            $action = "ADD_MEDICATION";
            $details = "Added $name (Batch: $batch)";
            $ip = $_SERVER['REMOTE_ADDR'];

            $log = mysqli_prepare($conn, "
                INSERT INTO activity_log (user_id, fullname, action, details, ip_address)
                VALUES (?, ?, ?, ?, ?)
            ");

            mysqli_stmt_bind_param($log, "issss", $created_by, $fullname, $action, $details, $ip);
            mysqli_stmt_execute($log);
            mysqli_stmt_close($log);
        }

        mysqli_commit($conn);

        echo "<script>alert('All medications added successfully'); window.location.href='dashboard.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}
?>