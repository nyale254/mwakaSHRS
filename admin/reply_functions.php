<?php
function saveReply($conn, $message_id, $admin_id, $reply_text) {
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE contact_messages SET status='Replied' WHERE id=?");
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO contact_replies (message_id, admin_id, reply_text) VALUES (?,?,?)");
        $stmt->bind_param("iis", $message_id, $admin_id, $reply_text);
        $stmt->execute();
        $reply_id = $stmt->insert_id;
        $stmt->close();

        $conn->commit();
        return ['success'=>true, 'reply_id'=>$reply_id];

    } catch(Exception $e) {
        $conn->rollback();
        return ['success'=>false, 'error'=>$e->getMessage()];
    }
}