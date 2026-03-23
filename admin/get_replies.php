<?php
session_start();
header('Content-Type: application/json');
include '../connect.php';
include 'reply_functions.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success'=>false,'error'=>'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message_id = intval($_POST['message_id']);
    $reply_text = trim($_POST['reply_text']);
    $admin_id = $_SESSION['user_id'];

    if(empty($reply_text)) {
        echo json_encode(['success'=>false,'error'=>'Reply text is required']);
        exit();
    }

    $result = saveReply($conn, $message_id, $admin_id, $reply_text);

    if($result['success']){
        echo json_encode(['success'=>true,'message'=>'Reply saved','reply_id'=>$result['reply_id']]);
    } else {
        echo json_encode(['success'=>false,'error'=>'DB error: '.$result['error']]);
    }
} else {
    echo json_encode(['success'=>false,'error'=>'Invalid request method']);
}