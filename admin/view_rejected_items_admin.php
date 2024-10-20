<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user clicked the logout button
if (isset($_POST['logout'])) {
    // Unset all session variables specific to admin
    unset($_SESSION['admin_username']);

    // Redirect the user to the login page
    header("Location: facultylogin.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['admin_username'])) {
    // Redirect to the login page if not logged in
    header("Location: facultylogin.php");
    exit();
}

// Check if the event ID is provided in the URL
if (!isset($_GET['event_id'])) {
    // Redirect back to the referring page with an error message
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Event ID not provided");
    exit();
}

// Get the event ID from the URL
$eventId = $_GET['event_id'];

// Query to fetch rejected images/videos for the current event
$rejectedQuery = "
    SELECT * 
    FROM mediafile 
    WHERE event_id = '$eventId' 
    AND (approval_status = 'rejected' OR approval_status = 'pending')
";

$resultRejected = mysqli_query($conn, $rejectedQuery);

if (!$resultRejected) {
    // Handle error if query fails
    echo "Error fetching rejected images/videos: " . mysqli_error($conn);
    exit();
}

// Query to fetch the event name based on the event ID
$eventNameQuery = "SELECT event_name FROM event WHERE event_id = '$eventId'";
$resultEventName = mysqli_query($conn, $eventNameQuery);

// Assuming $resultEventName has been defined and queried earlier in your code

if ($resultEventName && mysqli_num_rows($resultEventName) > 0) {
    $eventNameRow = mysqli_fetch_assoc($resultEventName);
    $eventName = $eventNameRow['event_name'];
} else {
    $eventName = "Unknown Event";

    // Redirect to admin_index.php if the event is unknown
    header("Location: admin_index.php");
    exit();
}

// Continue with the rest of your code here

// Fetch the notifications (you may need this functionality as per your original code)
$sql = "SELECT * FROM notifications WHERE notification_type = 'Folder Added' OR notification_type = 'Restore Request' ORDER BY timestamp DESC LIMIT 5";
$result = mysqli_query($conn, $sql);

// Initialize an array to store notifications
$notifications = [];

// Check if there are any notifications
if (mysqli_num_rows($result) > 0) {
    // Fetch each notification and store it in the array
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rejected Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    <style>
        .wrapper {
            background: #fff;
            /* Add some padding around the content */
        }

        .pending-message {
            color: #d9534f;
            font-weight: bold;
        }

        .no-media {
            border: 2px dashed grey;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: grey;
            margin: 20px 0;
            /* Add vertical margin for spacing */
        }

        /* Ensure the grid container is responsive and properly aligned */
        #media-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            /* Adjust to your preference */
            gap: 20px;
            /* Space between items */
            margin-bottom: 20px;
            /* Add bottom margin */
        }

        /* Style individual card elements */
        #media-container .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 6px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            /* Make cards full height for consistency */
        }

        /* Add hover effect for the cards */
        #media-container .card:hover {
            transform: translateY(-5px);
            cursor: pointer;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2), 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        /* Style the image within the card */
        #media-container .card .card-img-top {
            width: 100%;
            height: 200px;
            /* Fixed height for images */
            object-fit: cover;
            /* Cover the area without stretching */
            border-bottom: 2px solid #f0f0f0;
        }

        /* Style the video container */
        .video-container {
            width: 100%;
            height: 200px;
            /* Fixed height for videos */
            object-fit: cover;
            /* Maintain aspect ratio */
            border-bottom: 2px solid #007bff;
            /* Bottom border for distinction */
        }

        /* Style the card body content */
        #media-container .card .card-body {
            padding: 15px;
            text-align: center;
        }

        /* Style the file name */
        .file-name {
            color: #007bff;
            /* Blue color for file name */
            font-weight: bold;
            /* Bold font for emphasis */
            margin-bottom: 10px;
            /* Add some space below */
        }

        /* Style the checkbox container */
        #media-container .checkbox-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        /* Style the checkbox */
        #media-container .checkbox-container input[type="checkbox"] {
            cursor: pointer;
            margin-right: 8px;
            /* Space between checkbox and label */
        }

        /* Style the label */
        #media-container .checkbox-container label {
            font-weight: bold;
            font-size: 12px;
            /* Adjust label size */
            color: #007bff;
            /* Color of the label */
            cursor: pointer;
            /* Change cursor to pointer on hover */
            transition: color 0.3s ease;
            /* Smooth transition for color change */
        }

        /* Change color on hover */
        #media-container .checkbox-container label:hover {
            color: #0056b3;
            /* Darker shade on hover */
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        /* Style for the Check All Photos checkbox */
        #checkAllPhotos,
        #checkAllVideos {
            width: 12px;
            /* Width of the checkbox */
            height: 12px;
            /* Height of the checkbox */
            transform: scale(1.5);
            /* Increase overall size */
            margin-bottom: 5px;
            margin-right: 10px;
            /* Space between checkbox and label */
        }

        .form-check-input {
            width: 25px;
            /* Adjust width as needed */
            height: 25px;
            margin-right: 20px;
            /* Adjust height as needed */
        }

        /* Style for the label */
        .form-check-label {
            font-weight: bold;
            /* Make the label text bold */
            font-size: 14px;
            /* Font size for the label */
            cursor: pointer;
            color: green;
            /* Change cursor t
            mao pointer on hover */
            display: flex;
            /* Use flexbox for better alignment */
            align-items: center;
            /* Center vertically */
        }

        /* Additional styling for the label hover effect */
        .form-check-label:hover {
            color: #007bff;
            /* Change color on hover */
        }




        .modal2 {
            background-color: transparent;
            /* Set transparent background */
        }

        .modal-content {
            background-color: transparent;
            /* Optional: slight white background for the content */
        }
        @media (max-width: 992px) { /* Equivalent to fs-lg-2 */
    .title {
        font-size: 2rem;
    }
}
        @media (max-width: 576px) { /* Equivalent to fs-md-3 */
    .title {
        font-size: 22px;
    }
}


    </style>






</head>

<body>
    <?php include 'navbar.php' ?>

    <div class="wrapper">
        <div class="main-panel fade-in">
        <h2 class="text-center title my-3">
    Rejected Items for <?php echo htmlspecialchars($eventName); ?>
</h2>




            <?php if (mysqli_num_rows($resultRejected) > 0): ?>
                <form id="restoreForm" action="restore_admin.php?event_id=<?php echo $eventId; ?>" method="POST">
                    <h6>Photos</h6>
                    <div class=" mx-4 mb-2">
                        <label for="checkAllPhotos" class="form-check-label mb-2">
                            <input type="checkbox" id="checkAllPhotos" class="form-check-input">
                            Select All Photos
                        </label>
                    </div>
                    <div id="media-container">
                        <?php
                        $hasPhotos = false;
                        mysqli_data_seek($resultRejected, 0);
                        while ($media = mysqli_fetch_assoc($resultRejected)):
                            if ($media['file_type'] == 'photo'):
                                $hasPhotos = true;
                                ?>
                                <div class="card mb-3">
                                    <img src="../uploads/<?php echo htmlspecialchars($media['file_name']); ?>"
                                        alt="<?php echo htmlspecialchars($media['file_name']); ?>" class="card-img-top">
                                    <div class="card-body">
                                    <?php if ($media['approval_status'] == 'pending'): ?>
                                            <div class="pending-message mt-1">Requested to Restore</div>
                                        <?php endif; ?>
                                        <p class="mb-1 file-name"><?php echo htmlspecialchars($media['file_name']); ?></p>
                                        <div class="checkbox-container">
                                            <label for="checkbox-<?php echo htmlspecialchars($media['file_name']); ?>"
                                                class="form-check-label">
                                                <input type="checkbox" name="selected_images[]"
                                                    value="<?php echo htmlspecialchars($media['file_name']); ?>"
                                                    id="checkbox-<?php echo htmlspecialchars($media['file_name']); ?>"
                                                    class="form-check-input media-checkbox">

                                            </label>
                                        </div>
                                      
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                        <?php if (!$hasPhotos): ?>
                            <div class="col-12 no-media">No photos to restore</div>
                        <?php endif; ?>
                    </div>

                    <h6>Videos</h6>
                    <div class="checkbox-container mx-4 mb-2">
                        <label for="checkAllVideos" class="form-check-label">
                            <input type="checkbox" id="checkAllVideos" class="form-check-input">
                            Select All Videos
                        </label>
                    </div>
                    <div id="media-container">
                        <?php
                        $hasVideos = false;
                        mysqli_data_seek($resultRejected, 0);
                        while ($media = mysqli_fetch_assoc($resultRejected)):
                            if ($media['file_type'] == 'video'):
                                $hasVideos = true;
                                ?>
                                <div class="card mb-3">
                                    <video controls class="video-container" id="myVideo">
                                        <source src="../uploads/videos/<?php echo htmlspecialchars($media['file_name']); ?>"
                                            type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="card-body">
                                    <?php if ($media['approval_status'] == 'pending'): ?>
                                            <br>
                                            <div class="pending-message">Requested to Restore</div>
                                        <?php endif; ?>
                                        <p class="mb-1 file-name"><?php echo htmlspecialchars($media['file_name']); ?></p>
                                        <div class="checkbox-container mb-2">
                                            <label for="checkbox-video-<?php echo htmlspecialchars($media['file_name']); ?>"
                                                class="form-check-label">
                                                <input type="checkbox" name="selected_videos[]"
                                                    value="<?php echo htmlspecialchars($media['file_name']); ?>"
                                                    id="checkbox-video-<?php echo htmlspecialchars($media['file_name']); ?>"
                                                    class="form-check-input media-checkbox">

                                            </label>
                                        </div>
                                       
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                        <?php if (!$hasVideos): ?>
                            <div class="col-12 no-media">No videos to restore</div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn mb-3 text-light" style=" background-color:#b7202e;" >Restore Selected Items</button>
                    <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
                </form>

            <?php else: ?>
                <div class="alert alert-info " role="alert">
                    No rejected items found for this event.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal to display the media file -->


    <!-- Image Preview Modal -->
    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal2">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Image Preview" class="img-fluid">
                </div>
            </div>
        </div>
    </div>



    <?php include '../footer.php' ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('restoreForm').addEventListener('submit', function (event) {
            // Get all checkboxes
            const checkboxes = document.querySelectorAll('.media-checkbox');
            let checked = false;

            // Check if at least one checkbox is selected
            checkboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    checked = true;
                }
            });

            if (!checked) {
                alert('Please select at least one media file to restore.');
                event.preventDefault(); // Prevent form submission
            }
        });

        const video = document.getElementById('myVideo');

        video.addEventListener('playing', () => {
            video.style.objectFit = 'contain';
        });

        video.addEventListener('pause', () => {
            video.style.objectFit = 'contain';
        });

        video.addEventListener('ended', () => {
            video.style.objectFit = 'cover';
        });

        video.addEventListener('loadstart', () => {
            video.style.objectFit = 'cover';
        });
    </script>
    <script>
        // Function to open modal on image click
        document.querySelectorAll('#media-container .card-img-top').forEach(image => {
            image.addEventListener('click', function () {
                // Get the source of the clicked image
                const imgSrc = this.src;
                // Set the modal image source
                document.getElementById('modalImage').src = imgSrc;
                // Show the modal
                $('#imageModal').modal('show');
            });
        });
    </script>


    <script>
        document.getElementById('checkAllPhotos').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('input[name="selected_images[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('checkAllVideos').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('input[name="selected_videos[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>

    <script>
        document.getElementById('restoreForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Create a FormData object to hold the form data
            const formData = new FormData(this);

            // Use AJAX to submit the form data
            fetch('restore_admin.php?event_id=<?php echo $eventId; ?>', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    // Handle the response data (you can customize this)
                    alert(data); // Display the response from the server (optional)
                    // Optionally refresh the media container to reflect changes
                    location.reload(); // Reloads the page to see the updates
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>

</body>

</html>


<?php
// Close the database connection
mysqli_close($conn);
?>