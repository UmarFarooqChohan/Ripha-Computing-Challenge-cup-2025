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
    $user_id = intval($data['user_id']);
    
    // Check if attended
    $check = $conn->query("SELECT id FROM event_registrations 
                          WHERE event_id = $event_id AND user_id = $user_id AND attended = TRUE");
    
    if ($check->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User did not attend event']);
        exit;
    }

    // Generate unique certificate code
    $certificate_code = 'CERT-' . strtoupper(uniqid());
    
    $sql = "INSERT INTO certificates (event_id, user_id, certificate_code) 
            VALUES ($event_id, $user_id, '$certificate_code')";
    
    if ($conn->query($sql)) {
        // Update registration
        $conn->query("UPDATE event_registrations SET certificate_generated = TRUE 
                     WHERE event_id = $event_id AND user_id = $user_id");
        
        echo json_encode(['success' => true, 'certificate_code' => $certificate_code]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to generate certificate']);
    }
}

if ($method === 'GET') {
    $user_id = intval($_GET['user_id'] ?? $_SESSION['user_id']);
    
    $sql = "SELECT c.*, e.title as event_title, e.event_date 
            FROM certificates c 
            JOIN events e ON c.event_id = e.id 
            WHERE c.user_id = $user_id 
            ORDER BY c.issued_at DESC";
    
    $result = $conn->query($sql);
    $certificates = [];
    while ($row = $result->fetch_assoc()) {
        $certificates[] = $row;
    }
    echo json_encode($certificates);
}

$conn->close();
?>
