<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_username'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $eventId = $data['event_id'];

    // Update all media items to approved for the given event ID where status is 'awaiting' or 'approved'
    $sql = "UPDATE mediafile SET approval_status = 'approved' WHERE event_id = ? AND approval_status IN ('awaiting', 'approved')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
