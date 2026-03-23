<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../connect.php';

$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM contact_messages WHERE 1=1";
$params = [];
$types = "";

if($status_filter != 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if(!empty($search)) {
    $query .= " AND (fullname LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ssss";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if(!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$messages = $stmt->get_result();
$stmt->close();

$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Unread' THEN 1 ELSE 0 END) as unread,
    SUM(CASE WHEN status = 'Read' THEN 1 ELSE 0 END) as read_count,
    SUM(CASE WHEN status = 'Replied' THEN 1 ELSE 0 END) as replied
    FROM contact_messages";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contact Messages | SHRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="\Mwaka.SHRS.2\styles\admin-contact.css">
</head>
<body>
<div class="main-content">
    <div class="header">
        <h1><i class="fas fa-envelope"></i> Contact Messages</h1>
        <div class="admin-info">
            <span class="admin-name"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
        </div>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Messages</h3>
                <div class="number"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Unread</h3>
                <div class="number"><?php echo $stats['unread']; ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-envelope-open"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Read</h3>
                <div class="number"><?php echo $stats['read_count']; ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Replied</h3>
                <div class="number"><?php echo $stats['replied']; ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-reply-all"></i>
            </div>
        </div>
    </div>

    <div class="filters">
        <div class="filter-group">
            <label>Status Filter:</label>
            <select id="statusFilter" onchange="filterMessages(this.value)">
                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Messages</option>
                <option value="Unread" <?php echo $status_filter == 'Unread' ? 'selected' : ''; ?>>Unread</option>
                <option value="Read" <?php echo $status_filter == 'Read' ? 'selected' : ''; ?>>Read</option>
                <option value="Replied" <?php echo $status_filter == 'Replied' ? 'selected' : ''; ?>>Replied</option>
            </select>
        </div>
        <div class="filter-group">
           <form id="searchForm">
                <input type="text" name="search" id="searchInput" placeholder="Search...">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="messages-container">
        <table class="messages-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Sent Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($messages->num_rows > 0): ?>
                    <?php while($message = $messages->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $message['id']; ?></td>
                            <td><?php echo htmlspecialchars($message['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                            <td class="message-preview"><?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>...</td>
                            <td>
                                <span class="status-badge status-<?php echo $message['status']; ?>">
                                    <?php echo $message['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></td>
                            <td class="action-buttons">
                                <button class="btn-view" data-id="<?php echo $message['id']; ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>
                               <button class="btn-reply" data-id="<?php echo $message['id']; ?>">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                                <?php if($message['status'] == 'Unread'): ?>
                                   <button class="btn-mark" data-id="<?php echo $message['id']; ?>">
                                        <i class="fas fa-check"></i> Mark Read
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
                            <p style="margin-top: 10px;">No messages found.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-envelope"></i> Message Details</h3>
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewModalBody">
        </div>
    </div>
</div>
<div id="replyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-reply"></i> Reply to Message</h3>
            <span class="close" onclick="closeModal('replyModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div id="replyMessageInfo"></div>
            
            <div id="emailStatus" style="margin-bottom:10px; font-weight:bold;"></div>

           <form method="POST" id="replyForm">
                <input type="hidden" name="message_id" id="replyMessageId">
                <div class="reply-section">
                    <label><strong>Your Reply:</strong></label>
                    <textarea name="reply_text" rows="6" required placeholder="Type your reply here..."></textarea>
                </div>
                <div style="margin-top: 15px;">
                    <button type="submit" name="reply_message" class="btn-reply" style="padding: 10px 20px;">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </div>
            </form>
            <div id="replyHistory" style="margin-top: 20px;"></div>
        </div>

    </div>
</div>
</body>
</html>