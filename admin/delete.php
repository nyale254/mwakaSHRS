<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
header("Pragma: no-cache");

include "../connect.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json');

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    echo json_encode([
        'success'=>false,
        'message'=>'Unauthorized'
    ]);
    exit();
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    echo json_encode([
        'success'=>false,
        'message'=>'Invalid user ID'
    ]);
    exit();
}
$user_id = (int) $_GET['id'];

if($user_id === $_SESSION['user_id']){
    echo json_encode([
        'success'=>false,
        'message'=>'You cannot delete your own account'
    ]);
    exit();
}

mysqli_begin_transaction($conn);

try {

    $stmt = mysqli_prepare($conn,"SELECT student_id FROM students WHERE user_id=?");
    mysqli_stmt_bind_param($stmt,"i",$user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    if($student){

        $student_id = $student['student_id'];

        $stmt = mysqli_prepare($conn,"DELETE FROM conditions_allergies WHERE student_id=?");
        mysqli_stmt_bind_param($stmt,"i",$student_id);
        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare($conn,"DELETE FROM medical_records WHERE student_id=?");
        mysqli_stmt_bind_param($stmt,"i",$student_id);
        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare($conn,"DELETE FROM students WHERE user_id=?");
        mysqli_stmt_bind_param($stmt,"i",$user_id);
        mysqli_stmt_execute($stmt);

    }

    $stmt = mysqli_prepare($conn,"DELETE FROM users WHERE user_id=?");
    mysqli_stmt_bind_param($stmt,"i",$user_id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);

    echo json_encode([
        'success'=>true,
        'message'=>'User deleted successfully'
    ]);

} catch(Exception $e){

    mysqli_rollback($conn);

    echo json_encode([
        'success'=>false,
        'message'=>$e->getMessage()
    ]);
}
?>