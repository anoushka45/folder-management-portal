<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user clicked the logout button
if (isset($_POST['logout'])) {
    // Unset all session variables

    unset($_SESSION['admin_username']);

    // Redirect the user to the login page
    header("Location:facultylogin.php");
    exit();
}
if (!isset($_SESSION['admin_username'])) {
    // Redirect to the login page if not logged in
    header("Location:facultylogin.php");
    exit();
}

// Fetch unique committees with their events using a JOIN
$committeesQuery = "SELECT DISTINCT committee.committee_id, committee.committee_name
                    FROM event_committee_mapping 
                    INNER JOIN committee ON event_committee_mapping.committee_id = committee.committee_id";

$resultCommittees = mysqli_query($conn, $committeesQuery);

//

// Check if the user is logged in
// Check if the user is logged in

// Ch
if (isset($_GET['event_id'])) {
    $eventId = $_GET['event_id'];


    // Determine view type
    $viewType = isset($_GET['view_type']) ? $_GET['view_type'] : 'grid';


    // Query to fetch details of the current subfolder
    $subfolderQuery = "SELECT * FROM event WHERE event_id = '$eventId'";
    $resultSubfolder = mysqli_query($conn, $subfolderQuery);

    if ($resultSubfolder && mysqli_num_rows($resultSubfolder) > 0) {
        $subfolder = mysqli_fetch_assoc($resultSubfolder);
        $subfolderName = $subfolder['event_name'];

        // Query to fetch subfolders of the current subfolder
        $subfolderQuery = "SELECT * FROM event WHERE parent_event_id = '$eventId'";
        $resultSubfolders = mysqli_query($conn, $subfolderQuery);
        ?>

        <?php
        // Assuming $conn is your database connection and $eventId is already set
        $rejectedQuery = "SELECT COUNT(*) AS total_rejected FROM mediafile WHERE event_id = '$eventId' AND (approval_status = 'rejected' OR approval_status = 'pending')";
        $rejectedResult = mysqli_query($conn, $rejectedQuery);
        $rejectedItemsCount = 0;

        if ($rejectedResult) {
            $rejectedRow = mysqli_fetch_assoc($rejectedResult);
            $rejectedItemsCount = $rejectedRow['total_rejected'];  // Get the count of rejected items
        }
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $subfolderName; ?></title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="../style.css">
            <link rel="stylesheet" href="styles/style.css">
            <style>
                .icon-btn {
                    background-color: #ffffff;
                    /* White background for a clean look */
                    border: 2px solid #dcdcdc;
                    /* Light border for subtle definition */
                    border-radius: 50%;
                    /* Make the button circular */
                    padding: 10px;
                    /* Ensure padding for clickability */
                    width: 50px;
                    /* Define width to make it uniform */
                    height: 50px;
                    /* Define height to match width */
                    ;
                    /* Center horizontally */
                    transition: all 0.3s ease;
                    /* Smooth transition on hover */
                    cursor: pointer;
                    /* Pointer on hover */
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    /* Soft shadow for depth */
                }

                .icon-btn:hover {
                    background-color: #f5f5f5;
                    /* Light gray background on hover */
                    border-color: #bbbbbb;
                    /* Darker border on hover */
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                    /* Increase shadow for hover effect */
                    transform: translateY(-2px);
                    /* Slight lift on hover */
                }

                .icon-btn:active {
                    transform: translateY(1px);
                    /* Button depress effect on click */
                }

                .icon-image {
                    width: 24px;
                    /* Control icon size */
                    height: 24px;
                    /* Keep icons proportional */
                }

                .approve-button {
                    background-color: #b7202e !important;
                    /* Blue gradient */
                    border: none;
                    /* No border */
                    color: white;
                    /* White text */
                    padding: 7px 20px;
                    /* Adequate padding */
                    border-radius: 8px;
                    /* Rounded corners */
                    font-weight: 600;
                    /* Bold text */
                    font-size: 1rem;
                    /* Larger font size */
                    cursor: pointer;
                    /* Pointer cursor */
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    /* Shadow for depth */
                    transition: all 0.3s ease;
                    /* Smooth transitions */
                    display: flex;
                    /* Flexbox for centering */
                    align-items: center;
                    /* Centering items vertically */
                    justify-content: center;
                    /* Centering items horizontally */
                    width: auto;
                    /* Default width for larger screens */
                }

                /* Media query for smaller screens */
                @media (max-width: 769px) {
                    .approve-button {
                        width: 100%;
                        /* Full width on small screens */
                    }

                    h2 {
                        font-size: 22px;
                        /* Medium screens (tablet) */
                    }

                    p,
                    rejected-items-link {
                        font-size: 16px;
                    }
                }

                .approve-button:hover {
                    background: linear-gradient(135deg, #66B2FF, #007BFF);
                    /* Reverse gradient on hover */
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
                    /* Deeper shadow on hover */
                    transform: translateY(-2px);
                    /* Lift effect */
                }

                .approve-button:active {
                    transform: translateY(0);
                    /* Reset lift effect on click */
                }




                .modal2 {
                    max-width: 80%;
                    /* Adjust width as needed */
                    height: auto;
                    margin: auto;
                    /* Center horizontally */
                    /* Center vertically */
                }

                .modal2 .modal-content {
                    background-color: transparent;
                    /* Transparent background */
                    border: none;
                    /* Remove border */
                    box-shadow: none;
                    /* Remove shadow */
                }

                .modal2 .modal-body {
                    padding: 0;
                    /* Remove padding if necessary */
                }

                .carousel-item img {
                    object-fit: contain;
                    /* Ensure images maintain aspect ratio */
                    max-height: 90vh;
                    /* Control the maximum height */
                }

                .modal-content,
                .modal-header,
                .modal-body {
                    border: none;
                    /* Remove any borders */
                    outline: none;
                    /* Remove any outlines */
                }


                .modal1-close {
                    font-size: 1.5rem;
                    /* Increase font size */
                    color: #ffffff;
                    /* Set text color */
                    opacity: 0.8;
                    /* Set opacity */
                    transition: opacity 0.3s ease;
                    /* Smooth transition effect */
                }

                .modal1-close:hover {
                    color: #ff0000;
                    /* Change color on hover */
                    opacity: 1;
                    /* Fully opaque on hover */
                }

                .modal1-close span {
                    font-weight: bold;
                    /* Make the close icon bold */
                }




                #media-container {
                    display: grid;
                    grid-template-columns: repeat(5, 1fr);
                    gap: 20px;
                    cursor: pointer;

                    /* 5 pictures per row on large screens */
                }

                /* Adjust the grid for smaller screens */
                @media (max-width: 1200px) {
                    #media-container {
                        grid-template-columns: repeat(3, 1fr);
                        /* 3 pictures per row on medium screens */
                    }
                }

                @media (max-width: 768px) {
                    #media-container {
                        grid-template-columns: repeat(2, 1fr);
                        /* 2 pictures per row on small screens */
                    }

                    #videoSection {
                        justify-content: center;
                        /* Center the videos on smaller screens */
                    }

                    .video-container {
                        width: 100%;
                        margin-right: 0;
                        /* Ensures full width for the video container */
                    }
                }

                @media (max-width: 576px) {
                    #media-container {
                        grid-template-columns: 1fr;
                        /* 1 picture per row on very small screens */
                    }
                }

                /* Style individual card elements */
                #media-container .card {
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 6px 20px rgba(0, 0, 0, 0.1);
                    border-radius: 12px;
                    overflow: hidden;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                /* Add hover effect for the cards */
                #media-container .card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2), 0 12px 24px rgba(0, 0, 0, 0.2);
                }

                /* Style the image within the card */
                #media-container .card .card-img-top {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                    border-bottom: 2px solid #f0f0f0;
                }

                /* Style the card body content */
                #media-container .card .card-body {
                    padding: 15px;
                    text-align: center;
                }

                /* Style the checkbox container and label */
                #media-container .checkbox-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-top: 10px;
                }

                #media-container .checkbox-container input[type="checkbox"] {
                    margin-right: 8px;
                    cursor: pointer;
                    transform: scale(1.3);
                    /* Adjust the scale factor as needed (1.3 is 30% larger) */
                    /* Remove default styling (for webkit browsers) */
                    width: 15px;
                    /* Set a specific width */
                    height: 15px;
                    /* Set a specific height */
                    background-color: white;
                    /* Set background color */
                    border: 2px solid #007bff;
                    /* Set border color */
                    border-radius: 4px;
                    /* Optional: round corners */
                    outline: none;
                    /* Remove default outline */
                }


                #media-container .checkbox-container input[type="checkbox"]:checked {
                    background-color: #007bff;
                    /* Background color when checked */
                    border-color: #007bff;
                    /* Border color when checked */
                }


                /* Optional: Add some margin at the bottom for better spacing */
                #media-container .card-body label {
                    display: block;
                    color: #007bff;
                    margin-top: 6px;
                    font-weight: bold;
                    font-size: 14px;
                    letter-spacing: 0.5px;
                    transition: color 0.3s ease;
                }

                #videoSection {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    gap: 15px;
                    /* Adds space between the videos */
                }

                .video-container {
                    width: 320px;
                    cursor: pointer;
                    /* Fixed width for consistency */
                    /* Space below each video container */
                    background-color: #ffffff;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 4px 4px 10px 3px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    /* Adds smooth animation for hover effects */
                }

                .video-container video {
                    width: 100%;
                    /* Make video responsive */
                    height: 200px;
                    /* Fixed height to keep videos consistent in size */
                    object-fit: cover;
                    /* Maintain aspect ratio without extra white space */
                    transition: object-fit 0.3s ease;
                    /* Smooth transition for object-fit change */
                }

                .video-container label {
                    display: block;
                    color: #007bff;
                    margin-top: 6px;
                    font-weight: bold;
                    font-size: 14px;
                    letter-spacing: 0.5px;
                    padding: 10px;
                    transition: color 0.3s ease;
                    /* Smooth color transition on hover */
                }

                .video-container:hover {
                    transform: scale(1.05);
                    /* Slightly enlarges the video on hover */
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                    /* Increases the shadow on hover */
                }

                /* Optional: Adjust video container on smaller screens */
                @media (max-width: 768px) {
                    .video-container {
                        width: 100%;
                        /* Full width on small screens */
                    }
                }

                /* Responsive adjustments */
                @media (max-width: 768px) {
                    #videoSection {
                        grid-template-columns: 1fr;
                        /* Stack videos on smaller screens */
                    }
                }

                .rejected-items-link {
                    display: inline-block;
                    /* Allows padding and margins to be applied */
                    background-color: #b7202e !important;
                    /* Gradient background */
                    color: white;
                    /* White text for contrast */
                    padding: 7px 15px;
                    /* Adequate padding */
                    border-radius: 5px;
                    /* Rounded corners */
                    text-decoration: none;
                    /* Remove underline */
                    transition: background 0.3s ease, transform 0.2s ease;
                    /* Smooth transitions */
                    /* Distance from the top edge */
                }

                .rejected-items-link:hover {
                    background: linear-gradient(135deg, #ff7961, #f44336);
                    /* Reverse gradient on hover */
                    transform: translateY(-2px);
                    text-decoration: none;
                    color: black;
                    /* Slight lift effect */
                }






                .no-media-message {
                    border: 2px dashed #ccc;
                    border-radius: 10px;
                    color: #555;
                    padding: 30px;
                    font-size: 1.2rem;
                    text-align: center;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease;
                    margin: 1.5rem 0;
                    /* Margin for spacing above and below */
                }

                .no-media-message img {
                    max-width: 150px;
                    /* Adjust as needed */
                    height: auto;
                    /* Maintain aspect ratio */
                    margin-bottom: 20px;
                    /* Space below the image */
                    border-radius: 10px;
                    /* Round the corners of the image */
                    transition: transform 0.3s;
                    /* Smooth scaling effect */
                }

                .no-media-message img:hover {
                    transform: scale(1.05);
                    /* Slightly enlarge on hover */
                }

                .no-media-message p {
                    margin-top: 0;
                    /* Remove top margin for better alignment */
                    font-weight: bold;
                    /* Make the text bold */
                    color: #333;
                    /* Darker text color for better readability */
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

                input[type="checkbox"] {
                    width: 1.5em;
                    /* Adjust width as needed */
                    height: 1.5em;
                    /* Adjust height as needed */
                    border: 2px solid #007bff;
                    /* Set border color */
                    border-radius: 4px;
                    /* Adjusts vertical alignment with the label */
                }

                /* Container for the checkbox and label */
                .checkbox-container {
                    display: flex;
                    align-items: center;
                    /* Center the checkbox and label vertically */
                }

                /* Style for the checkbox */
                .checkbox-container input[type="checkbox"] {
                    width: 1.2em;
                    /* Size of the checkbox */
                    height: 1.2em;
                    /* Size of the checkbox */
                    margin-right: 10px;
                    margin-bottom: 5px;
                    /* Space between checkbox and label */
                }

                .checkbox-container label {
                    font-size: 1em;
                    /* Font size of the label */
                    cursor: pointer;
                    /* Pointer cursor for better UX */
                    color: green;
                    /* Darker text color for better readability */
                    transition: color 0.3s ease;
                    /* Smooth color transition on hover */
                }

                /* Hover effect for the label */
                .checkbox-container label:hover {
                    color: #0056b3;
                    /* Change color on hover for better interactivity */
                }


                .icon-responsive {
                    width: 24px;
                    /* Default size for larger screens */
                    height: 24px;
                    /* Default size for larger screens */
                }

                @media (max-width: 767.98px) {
                    .icon-responsive {
                        width: 16px;
                        /* Reduced size for smaller screens */
                        height: 16px;
                        /* Reduced size for smaller screens */
                    }
                }
            </style>
        </head>

        <body>
            <?php include 'navbar.php' ?>
            <div class="wrapper">
                <div class="main-panel fade-in">

                    <?php
                    if (isset($_GET['filter_type'])) {
                        $filterType = $_GET['filter_type'];
                    } else {
                        $filterType = 'all'; // Default to show all media
                    }

                    $numImages = 0;
                    $numVideos = 0;

                    // Query to fetch the number of images and videos based on the selected filter
                    if ($filterType == 'images' || $filterType == 'all') {
                        $mediaQuery = "SELECT COUNT(*) AS num_images FROM mediafile WHERE event_id = '$eventId' AND file_type = 'photo' AND (approval_status = 'approved' OR approval_status = 'awaiting')";
                        $result = mysqli_query($conn, $mediaQuery);
                        if ($result) {
                            $row = mysqli_fetch_assoc($result);
                            $numImages = $row['num_images'];
                        }
                    }

                    if ($filterType == 'videos' || $filterType == 'all') {
                        $videoQuery = "SELECT COUNT(*) AS num_videos FROM mediafile WHERE event_id = '$eventId' AND file_type = 'video' AND (approval_status = 'approved' OR approval_status = 'awaiting')";
                        $result = mysqli_query($conn, $videoQuery);
                        $row = mysqli_fetch_assoc($result);
                        $numVideos = $row['num_videos'];
                    }
                    ?>

                    <div class="d-flex justify-content-end mb-3">
                        
                        <a href="view_rejected_items_admin.php?event_id=<?php echo $eventId; ?>" class="rejected-items-link">
                            Rejected Items (<?php echo $rejectedItemsCount; ?>)
                        </a>
                    </div>





                    <h2><?php echo $subfolderName; ?></h2>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <?php
                                while ($subfolder = mysqli_fetch_assoc($resultSubfolders)) {
                                    ?>
                                    <div class="col-md-3">
                                        <a href="subfolder_view.php?event_id=<?php echo $subfolder['event_id']; ?>"
                                            class="folder-card p-0">
                                            <div class="card-body">
                                                <p class="card-title" style="color: black;"><img src="images/folder.png">
                                                    <?php echo $subfolder['event_name']; ?>
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="filter-section mb-3 mt-2">
                        <label for="filter_type" class="filter-label mr-2 mt-1">Filter</label>
                        <select id="filter_type" class="form-control d-inline-block w-auto" onchange="filterMedia()">
                            <option value="all">All media</option>
                            <option value="images">Images</option>
                            <option value="videos">Videos</option>
                        </select>
                    </div>

                    <?php if ($numImages == 0 && $numVideos == 0): ?>
                        <div class="no-media-message">
                            <img src="images/noitems.jpg" alt="No Media">
                            <p>No Items uploaded by committee!</p>
                        </div>
                    <?php else: ?>
                        <button id="approveAll" class="approve-button mb-4 mt-2">Approve All Items for this folder</button>

                        <!-- Ensure both image and video sections are within the same form -->
                        <form id="mediaForm" method="post"
                            action="process_selected_images_admin.php?event_id=<?php echo $eventId; ?>">

                            <!-- Images Section -->
                            <div id="imagesSection" class="media-section"
                                style="display: <?php echo ($filterType == 'all' || $filterType == 'images') ? 'block' : 'none'; ?>;">
                                <?php if ($numImages > 0): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="checkbox-container">
                                            <input type="checkbox" id="select_all_images"
                                                onclick="toggleCheckboxes(this, 'imageCheckbox')">
                                            <label for="select_all_images">Select All Images</label>
                                        </div>
                                        <div class="button-group d-none d-md-flex">
                                            <button type="submit" name="download_images" class="btn icon-btn p-2">
                                                <img src="images/download_icon.png" alt="Download" class="icon-image icon-responsive">
                                            </button>
                                            <button type="submit" name="reject_images" class="btn icon-btn p-2" id="rejectImages">
                                                <img src="images/reject.svg" alt="Reject" class="icon-image icon-responsive">
                                            </button>
                                            <button type="button" name="share_images" class="btn icon-btn p-2"
                                                onclick="shareSelected('images')">
                                                <img src="images/share.svg" alt="Share" class="icon-image icon-responsive">
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-md-none mt-2">
                                        <div class="button-group">
                                            <button type="submit" name="download_images" class="btn icon-btn p-2">
                                                <img src="images/download_icon.png" alt="Download" class="icon-image icon-responsive">
                                            </button>
                                            <button type="submit" name="reject_images" class="btn icon-btn p-2" id="rejectImages">
                                                <img src="images/reject.svg" alt="Reject" class="icon-image icon-responsive">
                                            </button>
                                            <button type="button" name="share_images" class="btn icon-btn p-2"
                                                onclick="shareSelected('images')">
                                                <img src="images/share.svg" alt="Share" class="icon-image icon-responsive">
                                            </button>
                                        </div>
                                    </div>


                                    <div class="grid-container mt-2" id="media-container">
                                        <?php
                                        $mediaQuery = "SELECT * FROM mediafile WHERE event_id = '$eventId' 
        AND file_type = 'photo' 
        AND (approval_status = 'approved' OR approval_status = 'awaiting') 
        ORDER BY FIELD(approval_status, 'awaiting', 'approved')";
                                        $resultMedia = mysqli_query($conn, $mediaQuery);

                                        while ($media = mysqli_fetch_assoc($resultMedia)) {
                                            ?>
                                            <div
                                                class="card mb-3 <?php echo $media['approval_status'] == 'awaiting' ? 'awaiting-border' : ''; ?>">
                                                <img src="../uploads/<?php echo $media['file_name']; ?>" class="card-img-top image-slide"
                                                    alt="Uploaded Image">
                                                <div class="card-body">
                                                <?php if ($media['approval_status'] == 'awaiting') { ?>
                                                        <span class="awaiting-text" style="font-size: 0.7rem; color: red;">Awaiting</span>
                                                    <?php } ?>
                                                    <div class="checkbox-container">
                                                        <input type="checkbox" class="imageCheckbox" name="selected_items[]"
                                                            value="<?php echo $media['file_name']; ?>">
                                                        <label></label>
                                                    </div>
                                                    <input type="hidden" name="all_images[]" value="<?php echo $media['file_name']; ?>">

                                                   
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>


                                <?php else: // If no images uploaded ?>
                                    <div class="no-media-message">
                                        <img src="images/noitems.jpg" alt="No Media">
                                        <p>No images uploaded by committee!</p>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <!-- Videos Section -->
                            <div id="videoSection" class="media-section"
                                style="display: <?php echo ($filterType == 'all' || $filterType == 'videos') ? 'block' : 'none'; ?>;">
                                <?php if ($numVideos > 0): ?>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="checkbox-container">
                                            <input type="checkbox" id="select_all_videos"
                                                onclick="toggleCheckboxes(this, 'videoCheckbox')">
                                            <label for="select_all_videos">Select All Videos</label>
                                        </div>
                                        <div class="button-group d-none d-md-flex">
                                            <button type="submit" name="download_videos" class="btn icon-btn p-2">
                                                <img src="images/download_icon.png" alt="Download" class="icon-image icon-responsive">
                                            </button>
                                            <button type="submit" name="reject_videos" class="btn icon-btn p-2" id="rejectVideos">
                                                <img src="images/reject.svg" alt="Reject" class="icon-image icon-responsive">
                                            </button>
                                            <button type="button" name="share_videos" class="btn icon-btn p-2"
                                                onclick="shareSelected('videos')">
                                                <img src="images/share.svg" alt="Share" class="icon-image icon-responsive">
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-md-none mt-2">
                                        <div class="button-group">
                                            <button type="submit" name="download_videos" class="btn icon-btn p-2">
                                                <img src="images/download_icon.png" alt="Download" class="icon-image icon-responsive">
                                            </button>
                                            <button type="submit" name="reject_videos" class="btn icon-btn p-2" id="rejectVideos">
                                                <img src="images/reject.svg" alt="Reject" class="icon-image icon-responsive">
                                            </button>
                                            <button type="button" name="share_videos" class="btn icon-btn p-2"
                                                onclick="shareSelected('videos')">
                                                <img src="images/share.svg" alt="Share" class="icon-image icon-responsive">
                                            </button>
                                        </div>
                                    </div>


                                    <div id="videoSection" class="d-flex flex-wrap mt-2">
                                        <?php
                                        $mediaQuery = "SELECT * FROM mediafile WHERE event_id = '$eventId' 
        AND file_type = 'video' 
        AND (approval_status = 'approved' OR approval_status = 'awaiting') 
        ORDER BY FIELD(approval_status, 'awaiting', 'approved')";
                                        $resultMedia = mysqli_query($conn, $mediaQuery);

                                        while ($media = mysqli_fetch_assoc($resultMedia)) {
                                            ?>
                                            <div
                                                class="video-container <?php echo $media['approval_status'] == 'awaiting' ? 'awaiting-border' : ''; ?>">
                                                <video controls>
                                                    <source src="../uploads/videos/<?php echo htmlspecialchars($media['file_name']); ?>"
                                                        type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <?php if ($media['approval_status'] == 'awaiting') { ?>
                                                    <span class="awaiting-text " style="font-size: 0.7rem; color: red;">Awaiting</span>
                                                <?php } ?>
                                                <label>
                                                    <input type="checkbox" name="selected_videos[]" class="videoCheckbox"
                                                        value="<?php echo htmlspecialchars($media['file_name']); ?>">
                                                </label>
                                                <input type="hidden" name="all_videos[]"
                                                    value="<?php echo htmlspecialchars($media['file_name']); ?>">

                                               
                                            </div>

                                        <?php } ?>
                                    </div>

                                <?php else: // If no videos uploaded ?>
                                    <div class="no-media-message">
                                        <img src="images/noitems.jpg" alt="No Media">
                                        <p>No Videos uploaded by committee!</p>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </form>
                    <?php endif; ?>








                </div>
            </div>
            <?php include '../footer.php' ?>

            <!-- Slideshow Modal -->
            <!-- Slideshow Modal -->
            <div class="modal fade" id="slideshowModal" tabindex="-1" role="dialog" aria-labelledby="slideshowModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal2" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close modal1-close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <!-- Carousel items will be dynamically added here -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Keep only one version of jQuery -->
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

            <!-- Popper.js and Bootstrap.js -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


            <script>

                $(document).ready(function () {
                    // Handle clicks on grid view images
                    $('.card-img-top').click(function () {
                        var imageSrc = $(this).attr('src');
                        var img = $('<img>').attr('src', imageSrc).addClass('d-block w-100');
                        var item = $('<div>').addClass('carousel-item active').append(img);
                        $('#carouselExampleControls .carousel-inner').empty().append(item);
                        $('#slideshowModal').modal('show');
                    });
                });


                function toggleCheckboxes(masterCheckbox, checkboxClass) {
                    const checkboxes = document.querySelectorAll(`.${checkboxClass}`);
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = masterCheckbox.checked;
                    });
                }

                function openModal(imageSrc) {
                    var modal = document.getElementById("imageModal");
                    var modalImg = document.getElementById("modalImage");
                    var span = document.getElementsByClassName("custom-close-button")[0];

                    modal.style.display = "block";
                    modalImg.src = imageSrc;

                    span.onclick = function () {
                        modal.style.display = "none";
                    }

                    // Close the modal when clicking outside the image
                    window.onclick = function (event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                }
                document.querySelectorAll('.video-container video').forEach(video => {
                    video.addEventListener('play', function () {
                        this.style.objectFit = 'contain'; // Change to contain when video is played
                    });

                    video.addEventListener('pause', function () {
                        this.style.objectFit = 'cover'; // Change back to cover when video is paused
                    });

                    video.addEventListener('ended', function () {
                        this.style.objectFit = 'cover'; // Ensure it goes back to cover when video ends
                    });
                });

                document.getElementById("approveAll").addEventListener("click", function () {
                    // Confirmation dialog
                    const confirmApproval = confirm("This action will approve all items in the folder. Do you want to proceed?");

                    if (confirmApproval) {
                        const eventId = <?php echo json_encode($eventId); ?>; // Get the event ID from PHP

                        fetch('approve.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ event_id: eventId })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Reload the page to reflect changes
                                    location.reload();
                                } else {
                                    alert("Failed to approve all items: " + (data.error || 'Unknown error'));
                                }
                            })
                            .catch(error => console.error("Error:", error));
                    }
                });


                function filterMedia() {
                    var filterValue = document.getElementById("filter_type").value;
                    var imagesSection = document.getElementById("imagesSection");
                    var videoSection = document.getElementById("videoSection");

                    if (filterValue === "images") {
                        imagesSection.style.display = "block";
                        videoSection.style.display = "none";
                    } else if (filterValue === "videos") {
                        imagesSection.style.display = "none";
                        videoSection.style.display = "block";
                    } else {
                        imagesSection.style.display = "block";
                        videoSection.style.display = "block";
                    }
                }
                function shareSelected(type) {
                    const selectedItems = [];
                    const checkboxes = document.querySelectorAll(`input[name="selected_${type === 'images' ? 'items' : 'videos'}[]"]:checked`);

                    checkboxes.forEach(checkbox => {
                        const fileName = checkbox.value;
                        let mediaUrl;

                        // Ensure the correct paths based on your file structure
                        if (type === 'images') {
                            mediaUrl = `https://gallery.kjsieit.in/uploads/${fileName}`;  // Adjust path as necessary
                        } else {
                            mediaUrl = `https://gallery.kjsieit.in/uploads/videos/${fileName}`; // Adjust path as necessary
                        }

                        // Test the media URL by logging it
                        console.log(mediaUrl);

                        // Optional: Check if the media file exists (if you have a method for that)
                        // You can implement an AJAX call here to check if the URL returns 200 OK response
                        selectedItems.push(mediaUrl);
                    });

                    if (selectedItems.length > 0) {
                        const message = `Check out these ${type === 'images' ? 'images' : 'videos'}: \n` + selectedItems.join('\n');
                        const encodedMessage = encodeURIComponent(message);
                        const shareUrl = `mailto:?subject=Check out these ${type === 'images' ? 'images' : 'videos'}&body=${encodedMessage}`;
                        window.open(shareUrl);
                    } else {
                        alert(`Please select at least one ${type === 'images' ? 'image' : 'video'} to share.`);
                    }
                }

                
            </script>
<script>
$(document).ready(function() {
    // Reject images
    $('#rejectImages').on('click', function() {
        var selectedItems = $('input[name="selected_items[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedItems.length > 0) {
            $.ajax({
                url: 'process_selected_images_admin.php?event_id=<?php echo $eventId; ?>&action=reject_images',
                type: 'POST',
                data: { selected_items: selectedItems },
                success: function(response) {
                    alert('Images rejected successfully!');
                    // Optionally refresh the media section or remove the rejected items from the DOM
                    location.reload(); // Refresh the page to see the updates
                },
                error: function(xhr, status, error) {
                    alert('An error occurred while rejecting images: ' + error);
                }
            });
        } else {
            alert('Please select at least one image to reject.');
        }
    });

    // Reject videos
    $('#rejectVideos').on('click', function() {
        var selectedVideos = $('input[name="selected_videos[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedVideos.length > 0) {
            $.ajax({
                url: 'process_selected_images_admin.php?event_id=<?php echo $eventId; ?>&action=reject_videos',
                type: 'POST',
                data: { selected_videos: selectedVideos },
                success: function(response) {
                    alert('Videos rejected successfully!');
                    // Optionally refresh the media section or remove the rejected items from the DOM
                    location.reload(); // Refresh the page to see the updates
                },
                error: function(xhr, status, error) {
                    alert('An error occurred while rejecting videos: ' + error);
                }
            });
        } else {
            alert('Please select at least one video to reject.');
        }
    });
});
</script>


        </body>

        </html>


        <?php
    } else {
        // Subfolder not found, redirect to the main page
        header("Location: admin_index.php");
        exit();  // Ensure no further code is executed
    }
} else {
    // Event ID not provided in the URL, redirect to the main page
    header("Location: admin_index.php");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>