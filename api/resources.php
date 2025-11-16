<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

if ($method === 'GET') {
    $category_filter = $_GET['category'] ?? 'all';
    $type_filter = $_GET['type'] ?? 'all';
    
    $where = [];
    if ($category_filter !== 'all') {
        $where[] = "r.category = '$category_filter'";
    }
    if ($type_filter !== 'all') {
        $where[] = "r.file_type = '$type_filter'";
    }
    
    $where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $sql = "SELECT r.*, u.name as uploader_name FROM resources r 
            JOIN users u ON r.uploaded_by = u.id 
            $where_clause
            ORDER BY r.created_at DESC";
    $result = $conn->query($sql);
    $resources = [];
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }
    echo json_encode($resources);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $title = $conn->real_escape_string($data['title']);
    $description = $conn->real_escape_string($data['description'] ?? '');
    $file_path = $conn->real_escape_string($data['file_path']);
    $file_type = $conn->real_escape_string($data['file_type'] ?? 'other');
    $category = $conn->real_escape_string($data['category'] ?? 'general');
    $allowed_roles = $conn->real_escape_string($data['allowed_roles'] ?? 'all');
    $uploaded_by = $_SESSION['user_id'];

    $sql = "INSERT INTO resources (title, description, file_path, file_type, category, uploaded_by, allowed_roles) 
            VALUES ('$title', '$description', '$file_path', '$file_type', '$category', $uploaded_by, '$allowed_roles')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload resource']);
    }
}

$conn->close();
?>
