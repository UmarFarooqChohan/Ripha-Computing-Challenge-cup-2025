<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    if ($action === 'register') {
        $event_id = intval($data['event_id']);
        $user_id = $_SESSION['user_id'];

        // Check if already registered
        $check = $conn->query("SELECT id FROM event_registrations WHERE event_id = $event_id AND user_id = $user_id");
        if ($check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Already registered']);
            exit;
        }

        $sql = "INSERT INTO event_registrations (event_id, user_id) VALUES ($event_id, $user_id)";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    }

    if ($action === 'mark_attendance') {
        $registration_id = intval($data['registration_id']);
        $sql = "UPDATE event_registrations SET attended = TRUE WHERE id = $registration_id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    if ($action === 'approve') {
        $registration_id = intval($data['registration_id']);
        $sql = "UPDATE event_registrations SET status = 'approved' WHERE id = $registration_id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}

if ($method === 'GET') {
    $event_id = intval($_GET['event_id'] ?? 0);
    if ($event_id > 0) {
        $sql = "SELECT er.*, u.name, u.email FROM event_registrations er 
                JOIN users u ON er.user_id = u.id 
                WHERE er.event_id = $event_id";
        $result = $conn->query($sql);
        $registrations = [];
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
        echo json_encode($registrations);
    }
}

$conn->close();
?>
