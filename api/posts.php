<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

if ($method === 'GET') {
    $type_filter = $_GET['type'] ?? 'all';
    $category_filter = $_GET['category'] ?? 'all';
    
    $where = [];
    if ($type_filter !== 'all') {
        $where[] = "p.post_type = '$type_filter'";
    }
    if ($category_filter !== 'all') {
        $where[] = "p.category = '$category_filter'";
    }
    
    $where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $sql = "SELECT p.*, u.name as author_name, u.role as author_role, u.profile_picture,
            (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as likes_count,
            (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comments_count
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            $where_clause
            ORDER BY p.created_at DESC";
    $result = $conn->query($sql);
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    echo json_encode($posts);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $content = $conn->real_escape_string($data['content']);
    $post_type = $conn->real_escape_string($data['post_type'] ?? 'post');
    $category = $conn->real_escape_string($data['category'] ?? 'general');
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (user_id, content, post_type, category) 
            VALUES ($user_id, '$content', '$post_type', '$category')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create post']);
    }
}

$conn->close();
?>
