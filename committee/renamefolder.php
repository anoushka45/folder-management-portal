<?php
include '../_dbconnect.php';

session_start();

if (!isset($_SESSION['committee_username'])) {
    header("Location: login.html");
    exit();
}

if (isset($_POST['event_id']) && isset($_POST['new_name'])) {
    $eventId = intval($_POST['event_id']);
    $newName = mysqli_real_escape_string($conn, $_POST['new_name']);

    $query = "UPDATE event SET event_name = '$newName' WHERE event_id = $eventId";
    if (mysqli_query($conn, $query)) {
        echo "Folder renamed successfully";
    } else {
        http_response_code(500);
        echo "Error renaming folder: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
