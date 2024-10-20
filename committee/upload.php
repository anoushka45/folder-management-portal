<?php
include '../_dbconnect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = $_POST['eventId'];
    
    // Fetch committee ID based on login session
    session_start();
    if (isset($_SESSION['committee_username'])) {
        $username = $_SESSION['committee_username'];
        $committeeQuery = "SELECT committee_id FROM committee WHERE login_username = '$username'";
        
        // Execute the query
        $resultCommittee = mysqli_query($conn, $committeeQuery);
        
        if ($resultCommittee && mysqli_num_rows($resultCommittee) > 0) {
            $row = mysqli_fetch_assoc($resultCommittee);
            $committeeId = $row['committee_id'];
            
            // Process image uploads
            if (!empty($_FILES['images']['name'][0])) {
                // Loop through each image file
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $imageName = $_FILES['images']['name'][$key];
                    $imageTmpName = $_FILES['images']['tmp_name'][$key];
                    $imageError = $_FILES['images']['error'][$key];
                    
                    // Check if uploaded file is a valid image
                    if ($imageError === 0) {
                        $filePath = '../uploads/' . $imageName;

                        // Check if file already exists and append a unique suffix
                        $fileCounter = 1;
                        $fileNameWithoutExt = pathinfo($imageName, PATHINFO_FILENAME);
                        $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);

                        while (file_exists($filePath)) {
                            $filePath = '../uploads/' . $fileNameWithoutExt . '_' . $fileCounter . '.' . $fileExtension;
                            $imageName = $fileNameWithoutExt . '_' . $fileCounter . '.' . $fileExtension;
                            $fileCounter++;
                        }

                        // Move uploaded file to desired location
                        if (move_uploaded_file($imageTmpName, $filePath)) {
                            // Insert file information into database
                            $insertQuery = "INSERT INTO mediafile (event_id, committee_id, file_name, file_type, approval_status) VALUES ('$eventId', '$committeeId', '$imageName', 'photo', 'awaiting')";
                            $resultInsert = mysqli_query($conn, $insertQuery);
                            if (!$resultInsert) {
                                echo "Error inserting data: " . mysqli_error($conn);
                            }
                        } else {
                            echo "Failed to upload image.";
                        }
                    } else {
                        echo "Error uploading image.";
                    }
                }
            }
            
            // Process video uploads
            if (!empty($_FILES['videos']['name'][0])) {
                // Loop through each video file
                foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
                    $videoName = $_FILES['videos']['name'][$key];
                    $videoTmpName = $_FILES['videos']['tmp_name'][$key];
                    $videoError = $_FILES['videos']['error'][$key];
                    
                    // Check if uploaded file is a valid video
                    if ($videoError === 0) {
                        $filePath = '../uploads/videos/' . $videoName;

                        // Check if file already exists and append a unique suffix
                        $fileCounter = 1;
                        $fileNameWithoutExt = pathinfo($videoName, PATHINFO_FILENAME);
                        $fileExtension = pathinfo($videoName, PATHINFO_EXTENSION);

                        while (file_exists($filePath)) {
                            $filePath = '../uploads/videos/' . $fileNameWithoutExt . '_' . $fileCounter . '.' . $fileExtension;
                            $videoName = $fileNameWithoutExt . '_' . $fileCounter . '.' . $fileExtension;
                            $fileCounter++;
                        }

                        // Move uploaded file to desired location
                        if (move_uploaded_file($videoTmpName, $filePath)) {
                            // Insert file information into database
                            $insertQuery = "INSERT INTO mediafile (event_id, committee_id, file_name, file_type, approval_status) VALUES ('$eventId', '$committeeId', '$videoName', 'video', 'awaiting')";
                            $resultInsert = mysqli_query($conn, $insertQuery);
                            if (!$resultInsert) {
                                echo "Error inserting data: " . mysqli_error($conn);
                            }
                        } else {
                            echo "Failed to upload video.";
                        }
                    } else {
                        echo "Error uploading video.";
                    }
                }
            }
            
            // Redirect back to the same page after upload
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            echo "Error fetching committee ID.";
        }
    } else {
        echo "User session not found.";
    }
} else {
    echo "Invalid request method.";
}
?>
