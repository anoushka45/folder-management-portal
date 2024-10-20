<?php
include '../_dbconnect.php';

// Start the session
session_start();


// Store the current page as last page in session before rendering this page


// Include your database configuration file

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

// Check if the event ID is provided in the URL
if (!isset($_GET['event_id'])) {
  // Redirect back to the referring page with an error message
  header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Event ID not provided");
  exit();
}

// Get the event ID from the URL
$eventId = $_GET['event_id'];

// Fetch the event name using the event ID
$eventNameQuery = "SELECT event_name FROM event WHERE event_id = '$eventId'";
$resultEventName = mysqli_query($conn, $eventNameQuery);

if ($resultEventName && mysqli_num_rows($resultEventName) > 0) {
  $eventRow = mysqli_fetch_assoc($resultEventName);
  $eventName = $eventRow['event_name'];
} else {
  $eventName = "Event"; // Default fallback
}


// Query to fetch rejected images/videos for the current event
$rejectedQuery = "SELECT * FROM mediafile WHERE event_id = '$eventId' AND (approval_status = 'rejected' OR approval_status = 'pending')";
$resultRejected = mysqli_query($conn, $rejectedQuery);

if (!$resultRejected) {
  // Handle error if query fails
  echo "Error fetching rejected images/videos: " . mysqli_error($conn);
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Rejected Items</title>
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <link rel="stylesheet" href="../style.css">
  <style>
    .wrapper {
      flex: 1;
      background: #fff;
    }

    .media-item {
      padding: 15px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      margin-bottom: 20px;
      height: 120px;
      background: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s, background-color 0.3s;
    }

    .media-item:hover {
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      background-color: #f9f9f9;
    }

    .media-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .file-name {
      margin: 0;
      cursor: pointer;
      color: black;
      font-weight: 500;
    }

    .file-name:hover {
      text-decoration: underline;
    }

    .checkbox-right {
      margin-left: auto;
    }

    .status-container {
      margin-top: 5px;
    }

    .button-group {
      margin-top: 10px;
    }

    .btn-link {
      padding: 5px 10px;
      font-size: 14px;
    }

    .modal-content img {
      width: 100%;
      height: auto;
    }

    .modal-content video {
      width: 100%;
      height: auto;
    }

    .no-media {
      border: 2px dashed lightgrey;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      color: grey;
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
      display: flex;
      flex-direction: column;
      height: 100%;
      /* Make cards full height for consistency */
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
    input[type="checkbox"] {
      margin-right: 8px;
      display: block;
      height:24px;
      width: 20px;
      cursor: pointer;
    }
    #media-container .checkbox-container input[type="checkbox"] {
      margin-right: 8px;
      height:24px;
      width: 20px;
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

    .video-container {
      width: 100%;
      height: 200px;
      /* Maintain height when not in fullscreen */
      object-fit: cover;
      /* Default behavior when not in fullscreen */
      border-bottom: 2px solid #007bff;
      /* Bottom border for distinction */
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
  </style>
</head>

<body>
  <?php include 'navbar.php' ?>

  <div class="wrapper">
    <div class="main-panel fade-in">
      <h2 class="mb-4">Rejected Items for <?php echo htmlspecialchars($eventName); ?></h2>

      <?php if (mysqli_num_rows($resultRejected) > 0): ?>
        <form id="restoreForm" action="restore.php?event_id=<?php echo $eventId; ?>" method="POST">
  <!-- Photo Section -->
  <h4>Photos</h4>
  <div id="media-container" class="row mb-4">
    <?php $hasPhotos = false; ?>
    <?php while ($media = mysqli_fetch_assoc($resultRejected)): ?>
      <?php if ($media['file_type'] == 'photo'): ?>
        <?php $hasPhotos = true; ?>
        <div class="col mb-4">
          <div class="card">
            <img src="../uploads/<?php echo $media['file_name']; ?>" alt="<?php echo $media['file_name']; ?>"
              class="card-img-top" onclick="openModal('<?php echo '../uploads/' . $media['file_name']; ?>')" style="cursor: pointer;" />
            <div class="card-body">
              <div class="checkbox-container">
                <input type="checkbox" name="selected_images[]" value="<?php echo $media['file_name']; ?>" />
                <br>
                <label><?php echo htmlspecialchars($media['file_name']); ?></label>
              </div>
              <div class="status-container">
                <?php if ($media['approval_status'] == 'pending'): ?>
                  <h6 class="text-success">Status: Pending</h6>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endwhile; ?>

    <?php if (!$hasPhotos): ?>
      <div class="col-12">
        <p class="text-muted no-media">No rejected photos found for this event.</p>
      </div>
    <?php endif; ?>
  </div>

  <h4>Videos</h4>
  <div id="media-container" class="row mb-4">
    <?php $hasVideos = false; ?>
    <?php mysqli_data_seek($resultRejected, 0); ?>
    <?php while ($media = mysqli_fetch_assoc($resultRejected)): ?>
      <?php if ($media['file_type'] == 'video'): ?>
        <?php $hasVideos = true; ?>
        <div class="col mb-4">
          <div class="card">
            <video controls class="card-img-top video-container">
              <source src="../uploads/videos/<?php echo $media['file_name']; ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
            <div class="card-body">
              <div class="checkbox-container">
                <input type="checkbox" name="selected_images[]" value="<?php echo $media['file_name']; ?>" />
                <label><?php echo htmlspecialchars($media['file_name']); ?></label>
              </div>
              <div class="status-container">
                <?php if ($media['approval_status'] == 'pending'): ?>
                  <h6 class="text-success">Status: Pending</h6>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endwhile; ?>

    <?php if (!$hasVideos): ?>
      <div class="col-12">
        <p class="text-muted no-media">No rejected videos found for this event.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Restore and Delete buttons -->
  <div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-success" id="restoreButton">Restore Items</button>
    <button type="button" class="btn btn-danger ml-3" id="deleteButton">Delete Items</button>
  </div>
</form>

<div id="responseMessage" class="mt-3"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    // Handle the Restore button click
    $('#restoreButton').click(function() {
      submitForm('restore');
    });

    // Handle the Delete button click
    $('#deleteButton').click(function() {
      submitForm('delete');
    });

    function submitForm(action) {
      // Collect selected images/videos
      const selectedItems = $('input[name="selected_images[]"]:checked').map(function() {
        return this.value;
      }).get();

      if (selectedItems.length === 0) {
        $('#responseMessage').html('<div class="alert alert-warning">Please select items to ' + action + '.</div>');
        return;
      }

      // Send the AJAX request
      $.ajax({
        url: $('#restoreForm').attr('action'),
        type: 'POST',
        data: {
          selected_images: selectedItems,
          [action]: true
        },
        success: function(response) {
          $('#responseMessage').html('<div class="alert alert-success">Items successfully ' + action + 'd.</div>');
          // Optionally, reload or update the media items without a full page reload
        },
        error: function(xhr, status, error) {
          $('#responseMessage').html('<div class="alert alert-danger">Error processing request: ' + error + '</div>');
        }
      });
    }
  });
</script>




      <?php else: ?>
        <div class="alert alert-info" role="alert">
          No rejected items found for this event.
        </div>
      <?php endif; ?>
    </div>



  </div>
  <!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color: transparent; border: none;">
      <div class="modal-body text-center">
        <img src="" id="modalImage" class="img-fluid" alt="Modal Image" />
      </div>
    </div>
  </div>
</div>



 
  <!-- Include jQuery and Bootstrap JS -->
   
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

function openModal(imageSrc) {
      // Set the image source for the modal
      document.getElementById('modalImage').src = imageSrc;

      // Show the modal
      $('#imageModal').modal('show');
    }

    document.querySelectorAll('.video-container').forEach(video => {
      video.addEventListener('click', () => {
        if (video.requestFullscreen) {
          video.requestFullscreen();
        } else if (video.mozRequestFullScreen) { // Firefox
          video.mozRequestFullScreen();
        } else if (video.webkitRequestFullscreen) { // Chrome, Safari, and Opera
          video.webkitRequestFullscreen();
        } else if (video.msRequestFullscreen) { // IE/Edge
          video.msRequestFullscreen();
        }
      });
    });

   
    // Change object-fit to contain in fullscreen
    document.addEventListener('fullscreenchange', () => {
      const videos = document.querySelectorAll('.video-container');
      videos.forEach(video => {
        if (document.fullscreenElement) {
          video.style.objectFit = 'contain'; // Change to contain in fullscreen
        } else {
          video.style.objectFit = 'cover'; // Reset to cover when exiting fullscreen
        }
      });
    });

  </script>

<script>
function openModal(imageSrc) {
  document.getElementById('modalImage').src = imageSrc;
  $('#imageModal').modal('show');
}
</script>

<script>
$(document).ready(function() {
  $('#restoreButton').click(function() {
    // Serialize the form data
    var formData = $('#restoreForm').serialize() + '&action=restore'; // Append action

    // Send AJAX request to restore.php
    $.ajax({
      url: $('#restoreForm').attr('action'),
      type: 'POST',
      data: formData, 
      success: function(response) {
        // Handle success (response is returned from restore.php)
        alert('Items set to status pending!');
        // Optionally refresh the page or update the UI
        location.reload();
      },
      error: function() {
        alert('An error occurred while restoring items.');
      }
    });
  });

  $('#deleteButton').click(function() {
    // Serialize the form data
    var formData = $('#restoreForm').serialize() + '&action=delete'; // Append action

    // Send AJAX request to restore.php
    $.ajax({
      url: $('#restoreForm').attr('action'),
      type: 'POST',
      data: formData,
      success: function(response) {
        // Handle success (response is returned from restore.php)
        alert('Items deleted successfully!');
        // Optionally refresh the page or update the UI
        location.reload();
      },
      error: function() {
        alert('An error occurred while deleting items.');
      }
    });
  });
});
</script>

</body>

</html>