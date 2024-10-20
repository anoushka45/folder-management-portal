<?php
include '../_dbconnect.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    echo "You must be logged in to perform this action.";
    exit();
}

// Check if the event ID and selected items are provided
if (isset($_POST['event_id']) && (isset($_POST['selected_images']) || isset($_POST['selected_videos']))) {
    $eventId = $_POST['event_id'];
    
    // Restore selected images
    if (isset($_POST['selected_images'])) {
        foreach ($_POST['selected_images'] as $image) {
            // Your logic to restore the image
            $updateQuery = "UPDATE mediafile SET approval_status = 'approved' WHERE file_name = '$image' AND event_id = '$eventId'";
            mysqli_query($conn, $updateQuery);
        }
    }

    // Restore selected videos
    if (isset($_POST['selected_videos'])) {
        foreach ($_POST['selected_videos'] as $video) {
            // Your logic to restore the video
            $updateQuery = "UPDATE mediafile SET approval_status = 'approved' WHERE file_name = '$video' AND event_id = '$eventId'";
            mysqli_query($conn, $updateQuery);
        }
    }

    echo "Selected items will be restored successfully.";
} else {
    echo "No items selected or event ID is missing.";
}
?>
