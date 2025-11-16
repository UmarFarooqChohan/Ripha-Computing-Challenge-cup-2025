<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $event_id = intval($data['event_id']);
    $user_id = $_SESSION['user_id'];
    $rating = intval($data['rating']);
    $comment = $conn->real_escape_string($data['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
        exit;
    }

    $sql = "INSERT INTO event_feedback (event_id, user_id, rating, comment) 
            VALUES ($event_id, $user_id, $rating, '$comment')
            ON DUPLICATE KEY UPDATE rating = $rating, comment = '$comment'";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit feedback']);
    }
}

if ($method === 'GET') {
    $event_id = intval($_GET['event_id'] ?? 0);
    
    if ($event_id > 0) {
        // Get feedback for specific event
        $sql = "SELECT ef.*, u.name as user_name FROM event_feedback ef 
                JOIN users u ON ef.user_id = u.id 
                WHERE ef.event_id = $event_id 
                ORDER BY ef.created_at DESC";
        $result = $conn->query($sql);
        $feedback = [];
        while ($row = $result->fetch_assoc()) {
            $feedback[] = $row;
        }
        
        // Get average rating
        $avg_result = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total_feedback 
                                    FROM event_feedback WHERE event_id = $event_id");
        $avg_data = $avg_result->fetch_assoc();
        
        echo json_encode([
            'feedback' => $feedback,
            'average_rating' => round($avg_data['avg_rating'], 2),
            'total_feedback' => $avg_data['total_feedback']
        ]);
    } else {
        // Get all feedback analytics
        $sql = "SELECT e.id, e.title, AVG(ef.rating) as avg_rating, COUNT(ef.id) as feedback_count 
                FROM events e 
                LEFT JOIN event_feedback ef ON e.id = ef.event_id 
                GROUP BY e.id 
                ORDER BY avg_rating DESC";
        $result = $conn->query($sql);
        $analytics = [];
        while ($row = $result->fetch_assoc()) {
            $analytics[] = $row;
        }
        echo json_encode($analytics);
    }
}

$conn->close();
?>
