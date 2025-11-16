<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

if ($method === 'GET') {
    $topic_filter = $_GET['topic'] ?? 'all';
    $where = $topic_filter !== 'all' ? "WHERE d.topic = '$topic_filter'" : '';
    
    $sql = "SELECT d.*, u.name as author_name,
            (SELECT COUNT(*) FROM discussion_replies WHERE discussion_id = d.id) as replies_count
            FROM discussions d 
            JOIN users u ON d.user_id = u.id 
            $where
            ORDER BY d.created_at DESC";
    $result = $conn->query($sql);
    $discussions = [];
    while ($row = $result->fetch_assoc()) {
        $discussions[] = $row;
    }
    echo json_encode($discussions);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $title = $conn->real_escape_string($data['title']);
    $content = $conn->real_escape_string($data['content']);
    $topic = $conn->real_escape_string($data['topic'] ?? 'general');
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO discussions (title, content, topic, user_id) VALUES ('$title', '$content', '$topic', $user_id)";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create discussion']);
    }
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    if ($action === 'lock') {
        $discussion_id = intval($data['id']);
        $sql = "UPDATE discussions SET is_locked = TRUE WHERE id = $discussion_id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        }
    } elseif ($action === 'archive') {
        $discussion_id = intval($data['id']);
        $sql = "UPDATE discussions SET is_archived = TRUE WHERE id = $discussion_id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        }
    }
}

$conn->close();
?>
