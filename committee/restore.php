<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['committee_username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.html");
    exit();
}

// Get the username of the logged-in user from the session
$username = $_SESSION['committee_username'];

// Query the database to get the committee_id based on the username
$getCommitteeIdQuery = "SELECT committee_id FROM committee WHERE login_username = ?";
$stmt = mysqli_prepare($conn, $getCommitteeIdQuery);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the committee_id
    $row = mysqli_fetch_assoc($result);
    $committeeId = $row['committee_id'];

    // Check if the event ID is provided in the URL
    if (!isset($_GET['event_id'])) {
        // Redirect back to the referring page with an error message
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Event ID or Event Name not provided");
        exit();
    }

    // Get the event ID and event name from the URL
    $eventId = $_GET['event_id'];
    $getEventNameQuery = "SELECT event_name FROM event WHERE event_id = ?";
    $stmtEventName = mysqli_prepare($conn, $getEventNameQuery);
    mysqli_stmt_bind_param($stmtEventName, "i", $eventId);
    mysqli_stmt_execute($stmtEventName);
    $resultEventName = mysqli_stmt_get_result($stmtEventName);
    
    if ($resultEventName && mysqli_num_rows($resultEventName) > 0) {
        $row = mysqli_fetch_assoc($resultEventName);
        $eventName = $row['event_name'];
    } else {
        // Handle error if event name not found
        echo "Error: Event name not found for the provided event ID";
        exit();
    }

    // Function to create a notification
    function createNotification($conn, $committeeId, $eventId, $notificationType, $message)
    {
        // Fetch the committee name based on the committee_id
        $committeeNameQuery = "SELECT committee_name FROM committee WHERE committee_id = ?";
        $stmtCommitteeName = mysqli_prepare($conn, $committeeNameQuery);
        mysqli_stmt_bind_param($stmtCommitteeName, "i", $committeeId);
        mysqli_stmt_execute($stmtCommitteeName);
        $resultCommitteeName = mysqli_stmt_get_result($stmtCommitteeName);

        if ($resultCommitteeName && mysqli_num_rows($resultCommitteeName) > 0) {
            $row = mysqli_fetch_assoc($resultCommitteeName);
            $committeeName = $row['committee_name'];

            // Insert the notification with committee_name
            $notificationQuery = "INSERT INTO notifications (committee_id, committee_name, event_id, event_name, notification_type, message) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtNotification = mysqli_prepare($conn, $notificationQuery);
            mysqli_stmt_bind_param($stmtNotification, "isssss", $committeeId, $committeeName, $eventId, $eventName, $notificationType, $message);
            $notificationResult = mysqli_stmt_execute($stmtNotification);

            if ($notificationResult) {
                return true;
            } else {
                echo "Error creating notification: " . mysqli_error($conn);
                return false;
            }
        } else {
            echo "Error fetching committee name: " . mysqli_error($conn);
            return false;
        }
    }

    // Check if the form is submitted and selected images/videos are present
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_images'])) {
        // Fetch selected images/videos to restore or delete
        $selectedImages = $_POST['selected_images'];

        // Fetch the committee name to include in the email
        $committeeNameQuery = "SELECT committee_name FROM committee WHERE committee_id = ?";
        $stmtCommitteeName = mysqli_prepare($conn, $committeeNameQuery);
        mysqli_stmt_bind_param($stmtCommitteeName, "i", $committeeId);
        mysqli_stmt_execute($stmtCommitteeName);
        $resultCommitteeName = mysqli_stmt_get_result($stmtCommitteeName);
        $committeeName = mysqli_fetch_assoc($resultCommitteeName)['committee_name'];

        // Check if the user clicked the restore button
        if (isset($_POST['restore'])) {
            // Iterate over selected images/videos and update their approval status to 'pending'
            foreach ($selectedImages as $image) {
                // Perform update operation for each selected image/video
                $updateQuery = "UPDATE mediafile SET approval_status = 'pending' WHERE file_name = ? AND event_id = ?";
                $stmtUpdate = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($stmtUpdate, "si", $image, $eventId);
                $result = mysqli_stmt_execute($stmtUpdate);
                if (!$result) {
                    // Handle error if update fails
                    echo "Error updating approval status: " . mysqli_error($conn);
                }
            }

            // Create a notification for the restore operation
            $notificationType = "Restore Request";
            $message = "Restore request: '$eventName' ";
            createNotification($conn, $committeeId, $eventId, $notificationType, $message);
            
            // Send email notification
            $emailTo = "vyasanoushka@gmail.com"; // Replace with the recipient's email address
            $subject = "Media Restoration Request from Committee";
            $emailMessage = "Dear User,\n\nThe committee '$committeeName' has requested to restore media items for the folder: '$eventName' (ID: '$eventId').\n\nPlease review the request and take necessary action.\n\nBest regards,\nKJSIT MEDIA PORTAL"; // Customize your message as needed
            $headers = "From: no-reply@gallery.kjsieit.in\r\n"; // Use your domain for the sender email

            if (mail($emailTo, $subject, $emailMessage, $headers)) {
                $_SESSION['message'] = "Selected items have been restored successfully. Notification email sent.";
            } else {
                $_SESSION['message'] = "Selected items have been restored successfully, but email notification failed to send.";
            }
            
            // Redirect after processing restore
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Check if the user clicked the delete button
        if (isset($_POST['delete'])) {
            // Iterate over selected images/videos and delete them
            foreach ($selectedImages as $image) {
                // Query to get the file details before deleting
                $fileQuery = "SELECT file_name, file_type FROM mediafile WHERE file_name = ? AND event_id = ?";
                $stmtFile = mysqli_prepare($conn, $fileQuery);
                mysqli_stmt_bind_param($stmtFile, "si", $image, $eventId);
                mysqli_stmt_execute($stmtFile);
                $resultFile = mysqli_stmt_get_result($stmtFile);

                if ($resultFile && mysqli_num_rows($resultFile) > 0) {
                    $row = mysqli_fetch_assoc($resultFile);
                    $fileName = $row['file_name'];
                    $fileType = $row['file_type'];

                    // Determine the path based on file type
                    if ($fileType === 'photo') {
                        $filePath = "../uploads/" . $fileName;
                    } else if ($fileType === 'video') {
                        $filePath = "../uploads/videos/" . $fileName;
                    } else {
                        $filePath = null;
                    }

                    // Delete the media file from the database
                    $deleteQuery = "DELETE FROM mediafile WHERE file_name = ? AND event_id = ?";
                    $stmtDelete = mysqli_prepare($conn, $deleteQuery);
                    mysqli_stmt_bind_param($stmtDelete, "si", $fileName, $eventId);
                    $deleteResult = mysqli_stmt_execute($stmtDelete);

                    if ($deleteResult) {
                        // Delete the file from the filesystem if it exists
                        if ($filePath && file_exists($filePath)) {
                            unlink($filePath);
                        }
                    } else {
                        // Error occurred while deleting the media file
                        echo "Error deleting media file: " . mysqli_error($conn);
                        exit();
                    }
                } else {
                    // File details not found
                    echo "Error: File details not found for the provided media file";
                    exit();
                }
            }

            // Redirect after processing delete
            $_SESSION['message'] = "Selected items have been deleted successfully.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        // No images/videos selected for restoring or deleting
        $_SESSION['error'] = "No images/videos selected for restoring or deleting.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // Handle error if committee_id not found for the logged-in user
    echo "Error: Committee ID not found for the logged-in user";
}

// Close the database connection
mysqli_close($conn);
?>
