<?php
include '../_dbconnect.php';

session_start();

if (!isset($_SESSION['committee_username'])) {
    header("Location: login.html");
    exit();
}

if (isset($_POST['event_id'])) {
    $eventId = intval($_POST['event_id']);

    // Function to recursively delete an event and its sub-events
    function deleteEventAndSubEvents($conn, $eventId) {
        // Begin a transaction
        mysqli_begin_transaction($conn);

        try {
            // Recursively find all sub-events
            $childEvents = getChildEvents($conn, $eventId);

            // Delete media files and mappings for the current event
            deleteMediaAndMappings($conn, $eventId);

            // Delete the event itself
            $deleteEvent = "DELETE FROM event WHERE event_id = ?";
            $stmt = mysqli_prepare($conn, $deleteEvent);
            mysqli_stmt_bind_param($stmt, "i", $eventId);
            mysqli_stmt_execute($stmt);

            // Delete all child events recursively
            foreach ($childEvents as $childEventId) {
                deleteEventAndSubEvents($conn, $childEventId);  // Recursion
            }

            // Commit transaction
            mysqli_commit($conn);
        } catch (Exception $e) {
            // Rollback transaction in case of an error
            mysqli_rollback($conn);
            throw $e;  // Rethrow the exception
        }
    }

    // Function to get all child events recursively
    function getChildEvents($conn, $eventId) {
        $childEvents = [];
        $query = "SELECT event_id FROM event WHERE parent_event_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $eventId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $childEvents[] = $row['event_id'];
        }

        return $childEvents;
    }

    // Function to delete media files and mappings for an event
    function deleteMediaAndMappings($conn, $eventId) {
        // Get media file paths
        $getMediaFiles = "SELECT file_name, file_type FROM mediafile WHERE event_id = ?";
        $stmt = mysqli_prepare($conn, $getMediaFiles);
        mysqli_stmt_bind_param($stmt, "i", $eventId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $fileName = $row['file_name'];
            $fileType = $row['file_type'];
            $filePath = ($fileType === 'photo') ? "../uploads/$fileName" : "../uploads/videos/$fileName";
            
            // Delete file from filesystem
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete from event_committee_mapping
        $deleteMappings = "DELETE FROM event_committee_mapping WHERE event_id = ?";
        $stmt = mysqli_prepare($conn, $deleteMappings);
        mysqli_stmt_bind_param($stmt, "i", $eventId);
        mysqli_stmt_execute($stmt);

        // Delete media files from the database
        $deleteMediaFiles = "DELETE FROM mediafile WHERE event_id = ?";
        $stmt = mysqli_prepare($conn, $deleteMediaFiles);
        mysqli_stmt_bind_param($stmt, "i", $eventId);
        mysqli_stmt_execute($stmt);
    }

    try {
        // Start by deleting the parent event and its sub-events recursively
        deleteEventAndSubEvents($conn, $eventId);
        echo "Folder and its subfolders deleted successfully";
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error deleting folder and subfolders: " . $e->getMessage();
    }
}

mysqli_close($conn);
?>
