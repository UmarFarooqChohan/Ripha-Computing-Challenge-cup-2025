<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

if ($method === 'GET') {
    if (!isLoggedIn() || !hasRole('admin')) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // its to Get overall statistics and there is query for the count
    $stats = [];
    
    // its the User counts by their role
    $result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stats['users_by_role'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['users_by_role'][$row['role']] = $row['count'];
    }
    
    // its is the Event statistics to 
    $result = $conn->query("SELECT 
        COUNT(*) as total_events,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_events,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_events
        FROM events");
    $stats['events'] = $result->fetch_assoc();
    
    // Attendance statistics
    $result = $conn->query("SELECT 
        COUNT(*) as total_registrations,
        SUM(CASE WHEN attended = TRUE THEN 1 ELSE 0 END) as attended_count
        FROM event_registrations");
    $stats['attendance'] = $result->fetch_assoc();
    
    // Engagement statistics
    $result = $conn->query("SELECT 
        (SELECT COUNT(*) FROM posts) as total_posts,
        (SELECT COUNT(*) FROM post_likes) as total_likes,
        (SELECT COUNT(*) FROM post_comments) as total_comments,
        (SELECT COUNT(*) FROM discussions) as total_discussions
    ");
    $stats['engagement'] = $result->fetch_assoc();
    
    // Top rated events
    $result = $conn->query("SELECT e.title, AVG(ef.rating) as avg_rating, COUNT(ef.id) as feedback_count
        FROM events e
        LEFT JOIN event_feedback ef ON e.id = ef.event_id
        GROUP BY e.id
        HAVING feedback_count > 0
        ORDER BY avg_rating DESC
        LIMIT 5");
    $stats['top_events'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['top_events'][] = $row;
    }
    
    echo json_encode($stats);
}

$conn->close();
?>
