<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user clicked the logout button
// Check if the user clicked the logout button
if (isset($_POST['logout'])) {
    // Unset the session variable specific to the logged-in committee
    if (isset($_SESSION['committee_id'])) {
        unset($_SESSION["committee_username_{$_SESSION['committee_id']}"]);
    }

    // Unset the committee_id session variable
    unset($_SESSION['committee_id']);

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
$committeeQuery = "SELECT committee_id, committee_name FROM committee WHERE login_username = '$loggedInUser'";
$resultCommittee = mysqli_query($conn, $committeeQuery);

if ($resultCommittee) {
    $row = mysqli_fetch_assoc($resultCommittee);
    $committeeId = $row['committee_id'];
    $committeeName = $row['committee_name'];

    // Query to fetch all folders added by the committee
    $foldersQuery = "SELECT * FROM event 
                 WHERE parent_event_id IS NULL 
                 AND event_id IN (
                     SELECT event_id FROM event_committee_mapping WHERE committee_id = '$committeeId'
                 )
                 ORDER BY time_stamp DESC";

    $resultFolders = mysqli_query($conn, $foldersQuery);
    $noFolders = mysqli_num_rows($resultFolders) === 0;

    // Fetch notifications only for existing events and media files



    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="../style.css">
        <title>My Folders</title>
        <style>
            /* Default button styling */
            .addfolder {
                background-color:#b7202e;                /* Bootstrap Success Color */
                border: none;
                border-radius: 8px;
                /* Rounded corners */
                padding: 12px 24px;
                /* Padding for a larger button */
                font-size: 16px;
                font-weight: 500;
                color: #fff;
                /* White text */
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                /* Subtle shadow */
                transition: all 0.3s ease;
                /* Smooth transition for hover effects */
                display: inline-block;
            }

            /* Hover effect */
            .addfolder:hover {
                background-color: #218838;
                /* Darker green on hover */
                transform: translateY(-3px);
                /* Subtle lift on hover */
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
                /* Enhanced shadow on hover */
            }

            /* Focused state */
            .addfolder:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.4);
                /* Green border glow when focused */
            }

            /* Button full width on smaller screens */
            @media (max-width: 575.98px) {
                .addfolder {
                    width: 100%;
                }
            }

            @media (max-width: 769px) {
                h2 {
                    font-size: 22px;
                    /* Medium screens (tablet) */
                }

                .create {
                    font-size: 15px;
                    /* Medium screens (tablet) */
                }

                .addfolder {
                    width: 100%;
                }
            }

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

            /* Circular button fixed to the bottom right */
            .

            /* Modal styles */
            .modal-body p {
                margin: 0 0 10px;
            }

            /* Circular button with animation */
            .help-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                background-color: #007bff;
                color: white;
                border-radius: 50%;
                border: none;
                font-size: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                z-index: 1000;
                animation: bounceIn 0.5s ease-in-out;
                /* Bounce animation on page load */
                transition: transform 0.3s ease-in-out, background-color 0.3s;
            }

            /* Hover effect with a scale-up and color change */
            .help-button:hover {
                background-color: #0056b3;
                transform: scale(1.1);
                /* Slightly increase the size */
            }

            /* Style for the modal header with a gradient */
            .modal-header {
                background-color:#b7202e;                /* Bright pink to soft orange */
                color: white;
                border-bottom: none;
                padding: 20px;
                text-align: center;
               
            }

            .modal-title {
                font-size: 1.7rem;
                font-weight: 700;
                letter-spacing: 1px;
               

            }

            /* Style for the modal body */
            .modal-body {
                color: black;
                /* Charcoal gray for readability */
                padding: 30px;
                font-size: 1.1rem;
                line-height: 1.7;
                border-bottom-left-radius: 15px;
                border-bottom-right-radius: 15px;
            }

            /* Styling for text in the modal */
            .modal-body p {
                margin-bottom: 20px;
            }

            .modal-body strong {
                color: #FF6F61;
                /* Coral for highlighting */
            }

            /* Footer styling with a clean button */
            .modal-footer {
                border-top: none;
                display: flex;
                justify-content: center;
                border-bottom-left-radius: 15px;
                border-bottom-right-radius: 15px;
            }

            /* Styled close button */
            .modal-footer .btn,
            .addfolderbtn {
                padding: 10px 20px;
                font-size: 1rem;
                border-radius: 25px;
                /* Rounded button */
                background-color:#b7202e;                /* Matching gradient for buttons */
                border: none;
                font-weight: 500;
                color: white;
                transition: background-color 0.3s ease;
            }

            .modal-footer .btn:hover {
                background: linear-gradient(135deg, #FFB347, #FF6F61);
                /* Darker gradient on hover */
            }

            /* Customize the close button in the header */
            .modal-header .close {
                color: white;
                opacity: 1;
            }

            .modal-header .close:hover {
                color: #FFE0B2;
                /* Light color on hover */
            }

            /* Modal background */
            .modal-content {
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            }

            /* Modal animation */
            .modal.fade .modal-dialog {
                transform: translate(0, -50px);
                transition: transform 0.3s ease-out;
            }

            .modal.show .modal-dialog {
                transform: translate(0, 0);
            }


            /* Pulse animation to draw attention */
            .help-button::before {
                content: '';
                position: absolute;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: rgba(0, 123, 255, 0.4);
                z-index: -1;
                animation: pulse 1.5s infinite;
                opacity: 0;
            }

            .heading {
                color: #b7202e !important;
                font-weight: 600;
                font-size: 20px;

            }

            /* Keyframes for bounce effect on load */
            @keyframes bounceIn {
                0% {
                    transform: scale(0.5);
                    opacity: 0;
                }

                50% {
                    transform: scale(1.2);
                    opacity: 1;
                }

                100% {
                    transform: scale(1);
                }
            }

            /* Keyframes for pulsing glow */
            @keyframes pulse {
                0% {
                    transform: scale(0.8);
                    opacity: 0.6;
                }

                100% {
                    transform: scale(1.5);
                    opacity: 0;
                }
            }
        </style>
    </head>

    <body>

        <?php include 'navbar.php' ?>

        <div class="wrapper">
            <div class="main-panel fade-in ">
                <h2>My Folders</h2>

                <!-- Button to add a new folder -->
                <!-- Button to add a new folder -->
                <!-- Button to add a new folder -->
                <!-- Button to add a new folder -->
                <button type="button" class="btn btn-success mb-3 addfolder" data-toggle="modal"
                    data-target="#addFolderModal">
                    Add New Folder
                </button>




                <div class="row">
                    <?php
                    while ($folder = mysqli_fetch_assoc($resultFolders)) {
                        $eventId = $folder['event_id'];
                        echo '<div class="col-md-3">';
                        // Move the anchor tag outside the folder card div
                        echo '<a href="additems.php?event_id=' . $eventId . '" style="text-decoration: none; color:black;">'; // Use style to remove default underline
                
                        echo '<div class="folder-card p-3" id="folder_' . $eventId . '" style="position: relative; box-shadow: none;" oncontextmenu="showContextMenu(event, ' . $eventId . '); return false;">';

                        // Folder icon and name
                        echo '<p><img src="images/folder.png" alt="Folder">' . $folder['event_name'] . '</p>';

                        echo '</div>'; // Close folder-card div
                        echo '</a>'; // Close anchor tag
                        echo '</div>';

                        // Context Menu for Folder
                        echo '<div id="context-menu-' . $eventId . '" class="context-menu">';
                        echo '<ul>';
                        echo '<li onclick="renameFolder(' . $eventId . ')">Rename</li>';
                        echo '<li onclick="deleteFolder(' . $eventId . ')">Delete</li>';
                        echo '</ul>';
                        echo '</div>';
                    }
                    ?>

                </div>



                <!-- Repeat context menu for other folders by dynamically generating IDs -->
                <!-- Circular Help Button -->
                <button id="helpButton" class="help-button" data-toggle="modal" data-target="#helpModal">
                    ?
                </button>

            </div>
        </div>

        <!-- Modal for adding a new folder -->
        <div class="modal fade" id="addFolderModal" tabindex="-1" role="dialog" aria-labelledby="addFolderModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFolderModalLabel">Add New Folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="addfolder.php" method="post">
                            <div class="form-group">
                                <label for="folderName">Folder Name</label>
                                <input type="text" class="form-control" id="folderName" name="folderName" required>
                            </div>
                            <button type="submit" class="btn addfolderbtn">Add Folder</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for welcome message -->
        <div class="modal fade" id="welcomeModal" tabindex="-1" role="dialog" aria-labelledby="welcomeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="welcomeModalLabel">Welcome,
                            <?php echo htmlspecialchars($committeeName); ?>!
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        Welcome to the media portal. Here, you can create folders and subfolders to organize your
                        media items. These items will be reviewed and approved or rejected by the admin. To get started,
                        simply click on the "Add New Folder" button.
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Got it!</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Help Modal -->
        <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <div class="d-flex justify-content-center">
                            <h5 class="modal-title" id="helpModalLabel">Media Portal</h5>
                        </div>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <div class="modal-body">
                        <div class="mt-3">
                            <p class="fs-4 heading mb-3">Welcome to the media portal!</p>
                            <p class="fs-5 text-dark mb-2">
                                To get started, click on <strong>Add New Folder</strong>.
                            </p>
                            <p class="fs-6 text-dark mb-2">
                                To <strong>Rename</strong> or <strong>Delete</strong> a folder, right-click on the folder or
                                hold (for mobile devices) to view options.
                            </p>
                            <p class="fs-6 text-dark mb-2">
                                The <strong>Notifications</strong> show information about Media rejected by the admin.
                            </p>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../footer.php' ?>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
        <script src="scripts/index.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                <?php if ($noFolders): ?>
                    $('#welcomeModal').modal('show');
                <?php endif; ?>
            });
        </script>

    </body>

    </html>

    <?php
} else {
    // Unable to fetch committee details
    header("Location: login.html");
    exit();

}

// Close the database connection
mysqli_close($conn);
?>