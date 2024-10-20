<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user clicked the logout button
if (isset($_POST['logout'])) {
    unset($_SESSION['admin_username']);
    header("Location: facultylogin.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: facultylogin.php");
    exit();
}

// Check if the event ID is provided in the URL
if (!isset($_GET['event_id'])) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Event ID not provided");
    exit();
}

$eventId = $_GET['event_id'];

// Fetch event name based on event ID
$eventNameQuery = "SELECT event_name FROM event WHERE event_id = ?";
$stmtEventName = mysqli_prepare($conn, $eventNameQuery);
mysqli_stmt_bind_param($stmtEventName, "s", $eventId);
mysqli_stmt_execute($stmtEventName);
$resultEventName = mysqli_stmt_get_result($stmtEventName);

if ($resultEventName && mysqli_num_rows($resultEventName) > 0) {
    $rowEventName = mysqli_fetch_assoc($resultEventName);
    $eventName = $rowEventName['event_name'];
} else {
    echo "Error: Event name not found";
    exit();
}

// Check if the form is submitted and selected images/videos are present
// Check if the form is submitted and selected images/videos are present
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedItems = isset($_POST['selected_items']) ? $_POST['selected_items'] : [];
    $selectedVideos = isset($_POST['selected_videos']) ? $_POST['selected_videos'] : [];

    // Process downloads and status updates
    if (isset($_POST['download_images']) && !empty($selectedItems)) {
        downloadItems($selectedItems, 'photo', $eventName); // Pass the event name
    } elseif (isset($_POST['download_videos']) && !empty($selectedVideos)) {
        downloadItems($selectedVideos, 'video', $eventName); // Pass the event name
    } elseif (isset($_POST['reject_images']) && !empty($selectedItems)) {
        updateStatus($selectedItems, $eventId, 'rejected'); // Reject selected images
    } elseif (isset($_POST['reject_videos']) && !empty($selectedVideos)) {
        updateStatus($selectedVideos, $eventId, 'rejected'); // Reject selected videos
    } elseif (isset($_POST['approve_all'])) {
        // Approve all images/videos that are currently selected
        approveSelectedItems($selectedItems, $eventId);
        approveSelectedItems($selectedVideos, $eventId);
    }

    // Automatically set the remaining items to approved
    autoApproveRemainingItems($eventId, array_merge($selectedItems, $selectedVideos));

    // Redirect back to the page after updating statuses
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}



// Function to handle downloading multiple items as a ZIP file
function downloadItems($selectedItems, $fileType, $eventName) {
    // Set the ZIP filename based on the file type and event name
    if ($fileType === 'photo') {
        $zipFilename = tempnam(sys_get_temp_dir(), 'photos_') . ".zip";
        $zipName = $eventName . "_photos.zip"; // Name of the ZIP file for photos
    } elseif ($fileType === 'video') {
        $zipFilename = tempnam(sys_get_temp_dir(), 'videos_') . ".zip";
        $zipName = $eventName . "_videos.zip"; // Name of the ZIP file for videos
    }

    // Initialize a new ZIP archive
    $zip = new ZipArchive();
    if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
        exit("Cannot open <$zipFilename>\n");
    }

    // Add selected items to the ZIP archive
    foreach ($selectedItems as $item) {
        if ($fileType === 'photo') {
            $filePath = '../uploads/' . $item; // Adjust path for photos
        } elseif ($fileType === 'video') {
            $filePath = '../uploads/videos/' . $item; // Adjust path for videos
        }

        // Add the file to the ZIP if it exists
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($filePath));
        }
    }

    // Close the ZIP archive
    $zip->close();

    // Serve the ZIP file for download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=' . $zipName);
    header('Content-Length: ' . filesize($zipFilename));
    flush(); // Flush system output buffer
    readfile($zipFilename);

    // Delete the temporary ZIP file after download
    unlink($zipFilename);
    exit();
}

// Modify the part where you call downloadItems
if (isset($_POST['download_images']) && !empty($selectedItems)) {
    downloadItems($selectedItems, 'photo', $eventName);
} elseif (isset($_POST['download_videos']) && !empty($selectedVideos)) {
    downloadItems($selectedVideos, 'video', $eventName);
}

// Function to update status of items
function updateStatus($selectedItems, $eventId, $status) {
    global $conn; // Access the global connection variable

    // Initialize a counter for rejected items
    $rejectedCount = 0;

    foreach ($selectedItems as $item) {
        // Update the selected items to the specified status
        $updateQuery = "UPDATE mediafile SET approval_status = ? WHERE file_name = ? AND event_id = ?";
        $stmtUpdate = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, "sss", $status, $item, $eventId);
        mysqli_stmt_execute($stmtUpdate);
        mysqli_stmt_close($stmtUpdate);

        if ($status === 'rejected') {
            $rejectedCount++; // Increment the count for each rejected item
        }
    }

    // If there are any rejected items, create a single notification
    if ($rejectedCount > 0) {
        createRejectionNotification($eventId, $rejectedCount); // Pass the count to the notification function
    }
}

function createRejectionNotification($eventId, $rejectedCount) {
    global $conn; // Access the global connection variable

    // Fetch event name and committee name using a join on event_committee_mapping
    $query = "SELECT e.event_name, c.committee_name 
              FROM event e
              JOIN event_committee_mapping ec ON e.event_id = ec.event_id
              JOIN committee c ON ec.committee_id = c.committee_id
              WHERE e.event_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $eventId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $eventData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($eventData) {
        $eventName = $eventData['event_name'];
        $committeeName = $eventData['committee_name'];

        // Create the notification message with the count of rejected items
        $message = "$rejectedCount item(s) for '$eventName' rejected";

        // Insert the notification into the 'notification' table
        $notificationQuery = "INSERT INTO notifications (committee_id, committee_name, event_id, notification_type, message, event_name) 
                              VALUES ((SELECT committee_id FROM event_committee_mapping WHERE event_id = ? LIMIT 1), ?, ?, 'rejected', ?, ?)";
        $stmtNotification = mysqli_prepare($conn, $notificationQuery);
        mysqli_stmt_bind_param($stmtNotification, "isiss", $eventId, $committeeName, $eventId, $message, $eventName);
        mysqli_stmt_execute($stmtNotification);
        mysqli_stmt_close($stmtNotification);
    }
}



// Function to approve selected items
function approveSelectedItems($selectedItems, $eventId) {
    global $conn; // Access the global connection variable

    foreach ($selectedItems as $item) {
        // Update the selected items to approved status
        $approveQuery = "UPDATE mediafile SET approval_status = 'approved' WHERE file_name = ? AND event_id = ?";
        $stmtApprove = mysqli_prepare($conn, $approveQuery);
        mysqli_stmt_bind_param($stmtApprove, "ss", $item, $eventId);
        mysqli_stmt_execute($stmtApprove);
        mysqli_stmt_close($stmtApprove);
    }
}

// New function to auto-approve remaining items
function autoApproveRemainingItems($eventId, $selectedItems) {
    global $conn; // Access the global connection variable

    // Fetch all media files for the event that are not rejected
    $query = "SELECT file_name FROM mediafile WHERE event_id = ? AND approval_status != 'rejected'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $eventId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Create an array of all files for the event
    $allFiles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $allFiles[] = $row['file_name'];
    }
    mysqli_stmt_close($stmt);

    // Loop through all files and approve those not in selected items
    foreach ($allFiles as $file) {
        if (!in_array($file, $selectedItems)) {
            $approveQuery = "UPDATE mediafile SET approval_status = 'approved' WHERE file_name = ? AND event_id = ?";
            $stmtApprove = mysqli_prepare($conn, $approveQuery);
            mysqli_stmt_bind_param($stmtApprove, "ss", $file, $eventId);
            mysqli_stmt_execute($stmtApprove);
            mysqli_stmt_close($stmtApprove);
        }
    }
}
?>
