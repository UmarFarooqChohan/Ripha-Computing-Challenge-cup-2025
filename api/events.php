<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

if ($method === 'GET') {
    $category_filter = $_GET['category'] ?? 'all';
    $where = $category_filter !== 'all' ? "WHERE e.category = '$category_filter'" : '';
    
    $sql = "SELECT e.*, u.name as creator_name,
            (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registered_count,
            (SELECT AVG(rating) FROM event_feedback WHERE event_id = e.id) as avg_rating
            FROM events e 
            JOIN users u ON e.created_by = u.id 
            $where
            ORDER BY e.event_date DESC";
    $result = $conn->query($sql);
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    echo json_encode($events);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $title = $conn->real_escape_string($data['title']);
    $description = $conn->real_escape_string($data['description']);
    $event_date = $conn->real_escape_string($data['event_date']);
    $location = $conn->real_escape_string($data['location']);
    $category = $conn->real_escape_string($data['category'] ?? 'academic');
    $max_participants = intval($data['max_participants'] ?? 0);
    $requires_approval = $data['requires_approval'] ? 1 : 0;
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO events (title, description, event_date, location, category, max_participants, requires_approval, created_by) 
            VALUES ('$title', '$description', '$event_date', '$location', '$category', $max_participants, $requires_approval, $created_by)";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create event']);
    }
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $event_id = intval($data['id']);
    
    $title = $conn->real_escape_string($data['title']);
    $description = $conn->real_escape_string($data['description']);
    $event_date = $conn->real_escape_string($data['event_date']);
    $location = $conn->real_escape_string($data['location']);
    $category = $conn->real_escape_string($data['category']);
    
    $sql = "UPDATE events SET title='$title', description='$description', 
            event_date='$event_date', location='$location', category='$category' 
            WHERE id=$event_id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

if ($method === 'DELETE') {
    $event_id = intval($_GET['id'] ?? 0);
    $sql = "DELETE FROM events WHERE id = $event_id";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

$conn->close();
?>
