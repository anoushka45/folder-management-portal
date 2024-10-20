<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user clicked the logout button
if (isset($_POST['logout'])) {
  // Unset all session variables
  unset($_SESSION['committee_username']);

  // Redirect the user to the login page
  header("Location: login.html");
  exit();
}

// Check if the user is logged in
if (!isset($_SESSION['committee_username'])) {
  // Redirect to the login page if not logged in
  header("Location: login.html");
  exit();
}

// Fetch the logged-in user's username and committee ID
$loggedInUser = $_SESSION['committee_username'];
$committeeQuery = "SELECT committee_id FROM committee WHERE login_username = '$loggedInUser'";
$resultCommittee = mysqli_query($conn, $committeeQuery);

if ($resultCommittee) {
  $row = mysqli_fetch_assoc($resultCommittee);
  $committeeId = $row['committee_id'];

  // Check if an event ID is provided in the URL
  if (isset($_GET['event_id'])) {
    $eventId = $_GET['event_id'];

    // Query to fetch details of the selected event
    $eventQuery = "SELECT * FROM event WHERE event_id = '$eventId'";
    $resultEvent = mysqli_query($conn, $eventQuery);

    if ($resultEvent && mysqli_num_rows($resultEvent) > 0) {
      $event = mysqli_fetch_assoc($resultEvent);
      $eventName = $event['event_name'];

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
        <title><?php echo $eventName; ?></title>
        <!-- Include Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="../style.css">
        <style>
          @media (max-width: 769px) {
            h2 {
              font-size: 22px;
              /* Medium screens (tablet) */
            }

            .create {
              font-size: 15px;
              /* Medium screens (tablet) */
            }
          }

          .custom-modal-header {
            background-color: #b7202e;
            /* Bright pink to soft orange */
            color: white;
            border-bottom: none;
            padding: 20px;
            text-align: center;

          }

          .create:hover {
            text-decoration: none;
          }

          /* Make modal background transparent */
          /* Center the modal and make it transparent */
          .modal-dialog.custom-modal-dialog1 {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 90vh;
          }

          .modal1 {
            border: none;
            background-color: transparent;
            /* Remove default border */
          }

          .carousel-inner img {
            max-width: 100%;
            /* Ensure the image does not overflow the container */
            max-height: 80vh;
            /* Adjust the maximum height to fit within the viewport height */
            object-fit: contain;
            /* Maintain aspect ratio and cover the container */
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



          .upload-button {
            background: linear-gradient(135deg, #4CAF50, #81C784);
            /* Green gradient */
            /* Coral gradient */
            /* Gradient from dark green to light green */
            border: none;
            padding: 8px 10px;
            /* Adequate padding */
            border-radius: 5px;
            /* Smooth corners */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            /* Soft shadow for depth */
            font-weight: 500;
            /* Bold text for better visibility */
            cursor: pointer;
            /* Pointer cursor on hover */
            transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
            color: white;
            /* White text for contrast */
            /* Smooth transitions */
          }

          .upload-button:hover {
            background: linear-gradient(135deg, #81c784, #4caf50);
            /* Reverse gradient on hover */
            box-shadow: 0px 6px 14px rgba(0, 0, 0, 0.3);
            /* Deeper shadow on hover */
            transform: translateY(-2px);
            color: white;
            /* White text for contrast */
            /* Slight lift effect on hover */
          }

          .upload-button:active {
            outline: none;
            transform: translateY(1px);
            /* Pressed effect */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
            /* Reduce shadow when pressed */
          }

          /* The Modal (background) */
          .custom-modal-background {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
          }

          /* Modal Content (Image) */
          .custom-modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            /* Limit width to 90% of the viewport width */
            max-height: 90vh;
            /* Limit height to 90% of the viewport height */
            height: auto;
            /* Maintain aspect ratio */
            width: auto;
            /* Maintain aspect ratio */
            position: absolute;
            /* Absolute positioning for centering */
            top: 50%;
            /* Center vertically */
            left: 50%;
            /* Center horizontally */
            transform: translate(-50%, -50%);
            /* Adjust for centering */
          }

          .custom-modal-content1 {
            margin: auto;
            display: block;
            max-width: 90%;
            /* Limit width to 90% of the viewport width */
            max-height: 90vh;
            /* Limit height to 90% of the viewport height */
            height: auto;
            /* Maintain aspect ratio */
            width: auto;
            /* Maintain aspect ratio */
            position: absolute;
            /* Absolute positioning for centering */
            top: 45%;
            /* Center vertically */
            left: 50%;
            /* Center horizontally */
            transform: translate(-50%, -50%);
            /* Adjust for centering */
          }

          /* The Close Button */
          .custom-close-button {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
          }

          .custom-close-button:hover,
          .custom-close-button:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
          }

          /* Styling the context menu */
          .context-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 1000;
            min-width: 150px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
          }

          .context-menu ul {
            list-style: none;
            margin: 0;
            padding: 5px 0;
          }

          .context-menu ul li {
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
            color: #333;
            transition: background-color 0.3s ease, color 0.3s ease;
          }

          .context-menu ul li:hover {
            background-color: #f1f1f1;
            color: #000;
          }

          /* Active state to give feedback */
          .context-menu ul li:active {
            background-color: #e2e6ea;
            color: #000;
          }

          .video-container {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 15px;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 4px 4px 10px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Adds smooth animation for hover effects */
          }

          .video-container:hover {
            transform: scale(1.05);
            cursor: pointer;

            /* Slightly enlarges the video on hover */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            /* Increases the shadow for a more dynamic effect */
          }

          #videoSection {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 15px;
            /* Adds space between the videos */
          }

          .video-container video {
            display: block;
            margin: 0 auto;

            object-fit: cover;
            /* Fits the thumbnail without extra white space */
            transition: transform 0.3s ease;
          }


          .video-container:hover video {
            transform: scale(1.02);

            /* Adds a slight zoom effect on hover */
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
            /* Adds smooth color transition on hover */
          }

          .video-container:hover label {
            color: #0056b3;
            /* Changes the label color on hover */
          }

          /* Ensure the grid container is responsive and properly aligned */
          #media-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
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

          @media (max-width: 1120px) {}

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
            cursor: pointer;
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



          .filter-label {
            font-weight: bold;
            /* Bold label for emphasis */
            margin-bottom: 8px;
            /* Space below the label */
            display: block;
            /* Ensures label is on a new line */
            color: #333;
            /* Darker color for better visibility */
          }

          @keyframes fadeIn {
            0% {
              opacity: 0;
            }

            100% {
              opacity: 1;
            }
          }

          .main-panel {
            opacity: 0;
            /* Initial state */
            animation: fadeIn 0.5s ease-in forwards;
            /* Apply the animation */
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

          .checkbox-container input[type="checkbox"] {
            width: 1.5em;
            /* Size of the checkbox */
            height: 1.5em;
            /* Size of the checkbox */
            margin-right: 10px;
            margin-top: 5px;
            /* Space between checkbox and label */
          }

          .select-all-label {
            font-weight: bold;
            color: #b7202e;
            /* Customize label text color */
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
          }

          /* Custom style for the checkbox */
          .select-all-label input[type="checkbox"] {
            accent-color: #007bff;
            /* Customize checkbox color */
            width: 20px;
            /* Increase width */
            height: 20px;
            /* Increase height */
            margin-right: 8px;
            /* Space between checkbox and label text */
          }
        </style>
      </head>

      <body>

        <?php include 'navbar.php' ?>




        <div class="wrapper">
          <div class="mx-5 d-flex justify-content-end gx-1 mt-3">
            <a href="view_rejected_items.php?event_id=<?php echo $eventId; ?>"
              class="btn btn-danger p-2 px-3 btn-sm btn-md btn-lg">
              Rejected Items (<?php echo $rejectedItemsCount; ?>)
            </a>


          </div>

          <div class="main-panel fade-in">
            <h2><?php echo $eventName; ?></h2>

            <div class="row">
              <?php while ($subfolder = mysqli_fetch_assoc($resultSubfolders)) {
                $subfolderId = $subfolder['event_id'];
                ?>
                <div class="col-md-3">
                  <a href="subfolder.php?event_id=<?php echo $subfolderId; ?>"
                    style="text-decoration: none; display: block; box-shadow: none;"
                    oncontextmenu="showSubfolderContextMenu(event, <?php echo $subfolderId; ?>); return false;">
                    <div class="folder-card p-0" id="subfolder_<?php echo $subfolderId; ?>">
                      <div class="card-body">
                        <p class="card-title" style="color:black;">
                          <img src="images/folder.png" alt="Folder">
                          <?php echo $subfolder['event_name']; ?>
                        </p>
                      </div>
                    </div>
                  </a>

                </div>

                <!-- Context Menu for Subfolder -->
                <div id="context-menu-subfolder-<?php echo $subfolderId; ?>" class="context-menu">
                  <ul>
                    <li onclick="renameSubfolder(<?php echo $subfolderId; ?>)">Rename</li>
                    <li onclick="deleteSubfolder(<?php echo $subfolderId; ?>)">Delete</li>
                  </ul>
                </div>
              <?php } ?>

              <!-- Add the plus icon -->
              <div class="col-md-3">
                <a href="#" class="folder-card p-0" style="box-shadow: none;" data-toggle="modal"
                  data-target="#createSubfolderModal">
                  <div class="card-body">
                    <p class="card-title create-subfolder">
                      <img src="images/plus-solid.svg" class="create-icon mb-1 " style="width: 1rem;" alt="Create icon">
                      <span class="create" style="color:#b7202e; font-weight:bold;">New</span>
                    </p>
                  </div>
                </a>
              </div>
            </div>

            <div class="row d-flex align-items-center mt-2">
              <div class="media-filter col-md-6">
                <label for="mediaFilter" class="filter-label">Choose Media Type:</label>
                <select id="mediaFilter" class="form-control filter-select" onchange="filterMedia()">
                  <option value="all">All</option>
                  <option value="images">Images</option>
                  <option value="videos">Videos</option>
                </select>
              </div>

              <div class="col-md-6 mt-4 text-right">
                <button class="upload-button mt-2 w-100" data-toggle="modal" data-target="#uploadModal">
                  <img src="images/upload_icon.png" alt=""> </button>
              </div>
            </div>

            <section id="mediaDisplayArea">
              <!-- Display Uploaded Images -->
              <div id="imageSection">
                <?php
                // Query to fetch number of images
                $imageCountQuery = "SELECT COUNT(*) AS num_images FROM mediafile WHERE event_id = '$eventId' AND file_type = 'photo'  AND (approval_status = 'approved' OR approval_status = 'awaiting')";
                $result = mysqli_query($conn, $imageCountQuery);
                $row = mysqli_fetch_assoc($result);
                $numImages = $row['num_images'];
                ?>

                <?php if ($numImages > 0): ?>
                  <form id="imageForm" method="post" action="process_selected_images.php?event_id=<?php echo $eventId; ?>" onsubmit="return submitImageForm(event)">
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
        <label class="mb-0 select-all-label">
            <input type="checkbox" id="selectAllImages" onclick="toggleCheckboxes(this, 'imageCheckbox')">
            Select All Images
        </label>
        <div class="d-flex gap-2 mt-2 mt-md-0 icon-container">
            <button type="button" class="btn btn-grey icon-btn" onclick="downloadSelected('image')">
                <img src="images/download_icon.png" alt="Download">
            </button>
            <button type="submit" class="btn btn-grey icon-btn mx-1" name="action" value="delete">
                <img src="images/deleteIcon.svg" alt="Delete">
            </button>
            <button type="button" name="share_images" class="btn icon-btn p-2" onclick="shareSelected('images')">
                <img src="../admin/images/share.svg" alt="Share" class="icon-image icon-responsive">
            </button>
        </div>
    </div>

    <div id="media-container" class="grid-container mt-4 mx-0">
        <?php
        // Query to fetch uploaded images for the current event
        $mediaQuery = "SELECT * FROM mediafile WHERE event_id = '$eventId' AND file_type = 'photo' AND (approval_status = 'approved' OR approval_status = 'awaiting') ORDER BY time_stamp DESC ";
        $resultMedia = mysqli_query($conn, $mediaQuery);

        while ($media = mysqli_fetch_assoc($resultMedia)): ?>
            <div class="card mb-3">
                <img src="../uploads/<?php echo htmlspecialchars($media['file_name']); ?>"
                    class="card-img-top image-slide" alt="Uploaded Image">
                <div class="card-body">
                    <label class="checkbox-container">
                        <input type="checkbox" name="selected_items[]" class="imageCheckbox"
                            value="<?php echo htmlspecialchars($media['file_name']); ?>">
                    </label>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</form>
                <?php else: ?>
                  <div class="no-media-message">
                    <img src="images/noitems.jpg" alt="No Media">
                    <p>No Images uploaded.</p>
                  </div>



                <?php endif; ?>
              </div>

              <!-- Display Uploaded Videos -->
              <div id="videoSection">
                <?php
                // Query to fetch number of videos
// Query to fetch number of videos excluding rejected ones
                $videoCountQuery = "SELECT COUNT(*) AS num_videos FROM mediafile WHERE event_id = '$eventId' AND file_type = 'video' AND (approval_status = 'approved' OR approval_status = 'awaiting') ";
                $result = mysqli_query($conn, $videoCountQuery);
                $row = mysqli_fetch_assoc($result);
                $numVideos = $row['num_videos'];
                ?>

                <?php if ($numVideos > 0): ?>
                  <br>
                  <form id="videoForm" method="post" action="process_selected_images.php?event_id=<?php echo $eventId; ?>" onsubmit="return submitVideoForm(event)">
    <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
        <label class="mb-0 select-all-label">
            <input type="checkbox" id="selectAllVideos" onclick="toggleCheckboxes(this, 'videoCheckbox')">
            Select All Videos
        </label>
        <div class="d-flex gap-2 mt-2 mt-md-0 icon-container">
            <button type="button" class="btn btn-grey icon-btn" onclick="downloadSelected('video')">
                <img src="images/download_icon.png" alt="Download" class="icon">
            </button>
            <button type="submit" class="btn btn-grey icon-btn mx-1" name="action" value="delete">
                <img src="images/deleteIcon.svg" alt="Delete" class="icon">
            </button>
            <button type="button" name="share_videos" class="btn icon-btn p-2" onclick="shareSelected('videos')">
                <img src="../admin/images/share.svg" alt="Share" class="icon-image icon-responsive">
            </button>
        </div>
    </div>

    <div class="mt-4 mx-0">
        <?php
        // Query to fetch uploaded videos for the current event
        $mediaQuery = "SELECT * FROM mediafile WHERE event_id = '$eventId' AND file_type = 'video' AND (approval_status = 'approved' OR approval_status = 'awaiting') ORDER BY time_stamp DESC";
        $resultMedia = mysqli_query($conn, $mediaQuery);

        while ($media = mysqli_fetch_assoc($resultMedia)): ?>
            <div class="video-container mb-3">
                <video width="320" height="240" controls>
                    <source src="../uploads/videos/<?php echo htmlspecialchars($media['file_name']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <label class="checkbox-container">
                    <input type="checkbox" name="selected_videos[]" class="videoCheckbox"
                        value="<?php echo htmlspecialchars($media['file_name']); ?>">
                </label>
            </div>
        <?php endwhile; ?>
    </div>
</form>
                <?php else: ?>
                  <div class="no-media-message">
                    <img src="images/noitems.jpg" alt="No Media">
                    <p>No videos uploaded.</p>
                  </div>

                <?php endif; ?>
              </div>

            </section>










          </div>
        </div>

        <?php include '../footer.php' ?>
        <!-- Modal Structure -->
        <div class="modal fade" id="slideshowModal" tabindex="-1" role="dialog" aria-labelledby="slideshowModalLabel"
          aria-hidden="true">
          <div class="modal-dialog custom-modal-dialog1 modal-lg" role="document">
            <div class="modal-content modal1">
              <div class="modal-header modal1-header">
                <button type="button" class="close modal1-close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                  <div class="carousel-inner">
                    <!-- Slides go here -->
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Video Modal -->

        <!-- Create New Subfolder Modal -->
        <div class="modal fade" id="createSubfolderModal" tabindex="-1" role="dialog"
          aria-labelledby="createSubfolderModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header  custom-modal-header">
                <h5 class="modal-title" id="createSubfolderModalLabel">Create New Subfolder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form action="addfolder.php" method="post">
                  <div class="form-group">
                    <label for="subfolderName">Subfolder Name</label>
                    <input type="text" class="form-control" id="subfolderName" name="folderName" required>
                    <input type="hidden" name="parentFolderId" value="<?php echo $eventId; ?>">
                  </div>
                  <button type="submit" class="btn text-light" style="background-color:#b7202e;">Create Subfolder</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Create New Subfolder Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
          aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header  custom-modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Items</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="uploadForm" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="images">Upload Images</label>
                    <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*">
                  </div>
                  <div class="form-group">
                    <label for="videos">Upload Videos</label>
                    <input type="file" class="form-control-file" id="videos" name="videos[]" multiple accept="video/*">
                  </div>
                  <input type="hidden" name="eventId" value="<?php echo $eventId; ?>">
                  <button type="submit" class="btn text-light " style="background-color:#b7202e;">Upload</button>
                </form>
                <br>
                <!-- Progress Bar -->
                <div id="progressContainer" style="display:none;">
                  <progress id="uploadProgress" value="0" max="100" style="width:100%;"></progress>
                  <span id="progressText">0%</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- JavaScript for handling the form submission and page refresh -->



        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

        <script src="scripts/committee.js"></script>

        <script>      
        function submitImageForm(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Create a FormData object from the form
    var formData = new FormData(document.getElementById('imageForm'));

    // Perform the AJAX request
    fetch('process_selected_images.php?event_id=<?php echo $eventId; ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Handle the response here
        alert('Images deleted successfully!'); // You can customize this as needed
        location.reload(); // Refresh the page to see changes
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function submitVideoForm(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Create a FormData object from the form
    var formData = new FormData(document.getElementById('videoForm'));

    // Perform the AJAX request
    fetch('process_selected_images.php?event_id=<?php echo $eventId; ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Handle the response here
        alert('Videos deleted successfully!'); // You can customize this as needed
        location.reload(); // Refresh the page to see changes
    })
    .catch(error => {
        console.error('Error:', error);
    });
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
          }</script>
      </body>

      </html>

      <?php
    } else {
      // Event not found
      echo "Event not found";
    }
  } else {
    // Event ID not provided in the URL
    echo "Event ID not provided";
  }
} else {
  // Unable to fetch committee details
  echo "Error fetching committee details: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>