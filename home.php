<?php
// Include your database configuration file
include '_dbconnect.php';

// Debugging: Check if the database connection is successful
if ($conn) {
    // echo "Database connection successful!"; // Uncomment for debugging
} else {
    die("Database connection failed.");
}

// Fetch approved photos from the mediafile table along with committee names
$query = "SELECT mf.file_name, c.committee_name 
          FROM mediafile mf 
          JOIN committee c ON mf.committee_id = c.committee_id 
          WHERE mf.file_type = 'photo' AND mf.approval_status = 'approved' 
          ORDER BY RAND() LIMIT 6";

$result = mysqli_query($conn, $query);

// Check for errors in the SQL query
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

// Store the image paths and committee names in an array
$images = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Ensure the path to the image is correct
    $images[] = [
        'path' => 'uploads/' . htmlspecialchars($row['file_name']), // Adjust path if needed
        'committee' => htmlspecialchars($row['committee_name']) // Committee name
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KJSIT Gallery</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-image: url('https://wallpapers.com/images/hd/orange-clear-frames-bdvamh3q0f3xkc33.jpg');
    background-size: cover; /* Optional: to cover the entire element */
    background-position: center; /* Optional: to center the image */
    background-repeat: no-repeat;
            padding: 0;
            color: #333;
            overflow-x: hidden;
        }

        * {
            box-sizing: border-box;
        }

        /* Gallery Section Styles */
        .gallery-section {
            text-align: center;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gallery-section h3 {
            font-size: 37px;
            color: #ffffff;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .gallery {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 40px 20px;
            flex-wrap: wrap;
        }

        .gallery img {
            width: 320px;
            height: 220px;
            object-fit: cover;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative; /* Needed for tooltip positioning */
        }

        .gallery img:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .tooltip {
    position: absolute;
    background-color: rgba(0, 0, 0, 0.6); /* Darker background for better contrast */
    color: #fff; /* White text for contrast */
    padding: 12px 12px; /* Increased padding for a more spacious feel */
    border-radius: 8px; /* Rounded corners */
    visibility: hidden; /* Initially hidden */
    transition: visibility 0.2s, opacity 0.3s ease-in-out; /* Smoother transition */
    z-index: 10; /* Ensure it's above other elements */
    font-size: 14px; /* Font size */
    white-space: nowrap; /* Prevent text from wrapping */
    bottom: 80px; /* Adjust position above the image */
    left: 50%; /* Center horizontally */
    transform: translateX(-50%); /* Center the tooltip */
    opacity: 0; /* Start invisible */
}

.gallery img:hover + .tooltip {
    visibility: visible; /* Make it visible on hover */
    opacity: 1; /* Fade in effect */
}

.hero-section {
        
    /* Lavender to light purple */

    color: black;
            /* Dark text color for contrast */
            padding: 60px 20px;
            /* Padding for spacing */
            text-align: center;
            /* Centered text */
        }

        .title{
            font-size: 2.2rem;
            font-style: normal;
            /* Increase font size for more impact */
            margin-bottom: 30px;
            /* More space below the heading */
            font-weight: 700;
            /* Semi-bold text for emphasis */
            text-transform: uppercase;
            /* Uppercase for stylistic effect */
            letter-spacing: 2px;
            /* Space between letters for a modern look */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            /* Soft shadow for depth */
            /* A coral color for the heading */
        }

        .hero-section p {
    font-size: 1.4rem; /* Slightly larger font size for readability */
    line-height: 1.8; /* Increased line height for better spacing */
    max-width: 800px; /* Limiting width for readability */
    margin: 0 auto; /* Centering the paragraph */
    font-style: italic; /* Italic style for a more elegant look */
    padding: 30px;
    color: black;
    /* Padding for better readability on smaller screens */
    background-color: white; /* White background */
    border-radius: 8px; /* Optional: rounded corners for a softer look */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: subtle shadow for depth */
    transition: color 0.3s ease; /* Smooth transition for hover effect */
}

      
   

        /* Footer Styles */
        .footer {
            text-align: center;
            padding: 30px;
            background-color: #b7202e;
            color: white;
            margin-top: 40px;
            position: relative;
            overflow: hidden;
        }

        .footer p {
            font-size: 16px;
            margin: 0;
        }


/* Media queries for smaller screens */
@media (max-width: 768px) {
    .gallery-section h3,   .title , .hero-section p {

        font-size: 20px;}


}

/* Keyframes for fade-in animation */
@keyframes fadeIn {
    0% {
        opacity: 0; /* Start invisible */
        transform: translateY(20px); /* Move down a bit */
    }
    100% {
        opacity: 1; /* Fully visible */
        transform: translateY(0); /* Reset position */
    }
}

/* Apply the fade-in animation to the section */
.fade-in {
    animation: fadeIn 0.8s ease forwards; /* Trigger the animation */
}


.modal-content {
    background-color: transparent; /* Black with transparency */
    border: none; /* Remove border */
}

.modal-body {
    display: flex;
    justify-content: center;
    align-items: center;
}

.img-fluid {
    max-width: 100%;
    height: auto;
}
.close {
    font-size: 50px; /* Increase font size for the close button */
    color: white; /* Adjust color if needed */
    transition: color 0.3s; /* Smooth transition for hover effect */
}

.close:hover{
    cursor: pointer;
}

.hero-section {
    color: black;
    padding: 80px 40px;
    text-align: center;
    border-radius: 15px; /* Rounded corners for a softer look */
}

.title {
    font-size: 3rem; /* Increased font size for more impact */
    font-weight: 700; /* Bold for emphasis */
    text-transform: uppercase; /* Uppercase for style */
    letter-spacing: 3px;
    color:#b7202e; /* Increased letter spacing for a modern look */
}

.sub-title {
    font-size: 1.6rem; /* Slightly smaller than the title */
    font-weight: 500; /* Medium weight */
    color: #444; /* Dark gray color for a softer contrast */
    margin-bottom: 30px; /* Space below the subtitle */
    text-shadow: 1px 1px 4px rgba(255, 255, 255, 0.5); /* Light shadow for a glowing effect */
}

    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light" style="
    
    ">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./admin/facultylogin.php" style="color: white;">Admin Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./user/userlogin.php" style="color: white;">User Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./committee/login.html" style="color: white;">Committee Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section >
    <section>
    <div class="hero-section">
        <p class="fade-in">
            <span class="title">MediaNest Media Portal</span>
            <br>
            <span class="sub-title">Your Gateway to a Vibrant Collection of Media</span>
            <br>
            Discover a comprehensive archive of media from various events organized by committees, including academic seminars, cultural programs, technical fests, and institutional initiatives. <span class="d-none d-md-block">This platform enables both participants and organizers to view, manage, and reflect on past events. Log in to browse through categorized galleries, oversee media content, and efficiently manage media resources for upcoming committee activities.</span>
        </p>
    </div>
</section>


    <!-- Gallery Section -->
  <!-- Include Bootstrap CSS (if not already included) -->

<div class="gallery-section fade-in">
    <h3 class="responsive-subheading">Explore Media by Various Committees</h3>
    <div class="gallery">
        <?php
        // Display images in the gallery section with committee names as tooltips
        foreach ($images as $image) {
            echo '<div class="gallery-item" style="position: relative;">';
            echo '<img src="' . htmlspecialchars($image['path']) . '" alt="Event Image" class="gallery-image" data-toggle="modal" data-target="#imageModal" data-img-src="' . htmlspecialchars($image['path']) . '">'; // Data attributes for Bootstrap
            echo '<div class="tooltip">' . htmlspecialchars($image['committee']) . '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- Bootstrap Modal Structure -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            <div class="modal-body">
                <img class="img-fluid" id="modalImage" src="" alt="Enlarged Image">
            </div>
        </div>
    </div>
</div>


    <!-- Explore Button -->
    <div style="text-align: center; margin-top: 40px;">
        <a href="./user/userlogin.php" style="
            background-color: #b7202e;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;">
            View More
        </a>
    </div>
    </section>
    <!-- Hero Section -->
 
    <br>
    <?php include 'footer.php' ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include jQuery and Bootstrap JS (if not already included) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // When the modal is shown, set the image source
    $('#imageModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var imgSrc = button.data('img-src'); // Extract info from data-* attributes
        var modalImage = $(this).find('#modalImage'); // Find the image in modal
        modalImage.attr('src', imgSrc); // Set the image source
    });
});
</script>

    <script>$(document).ready(function() {
    // When the modal is shown, set the image source
    $('#imageModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var imgSrc = button.data('img-src'); // Extract info from data-* attributes
        var modalImage = $(this).find('#modalImage'); // Find the image in modal
        modalImage.attr('src', imgSrc); // Set the image source
    });
});</script>

</body>

</html>
