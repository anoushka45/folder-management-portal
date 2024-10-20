<?php
// Include your database configuration file
include '../_dbconnect.php';

// Start the session
session_start();

// Check if the user clicked the logout button
if (isset($_POST['logout'])) {
    // Start the session
    session_start();

    // Unset specific admin session variable
    unset($_SESSION['admin_username']); // or use session variables unique to the admin

    // Optionally, regenerate the session ID to prevent session fixation
    session_regenerate_id(true);

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


// Include your CSS and other head elements here



// Ch
// Fetch unique committees with their events using a JOIN
$committeesQuery = "SELECT DISTINCT committee.committee_id, committee.committee_name
                    FROM event_committee_mapping 
                    INNER JOIN committee ON event_committee_mapping.committee_id = committee.committee_id";

$resultCommittees = mysqli_query($conn, $committeesQuery);
$noCommittees = mysqli_num_rows($resultCommittees) === 0;


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Index</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
    <style>
        .wrapper {
            background: #fff;
            /* This pushes the footer to the bottom */
        }

        .folder-card {
            box-shadow: none;

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
    </style>
</head>

<body>

    <?php include 'navbar.php' ?>

    <div class="wrapper">
        <div class="main-panel fade-in">
            <h2>All Committees</h2>
            <div class="row">
                <?php while ($committee = mysqli_fetch_assoc($resultCommittees)): ?>
                    <div class="col-md-3">
                        <a href="committee_events.php?committee_id=<?php echo $committee['committee_id']; ?>"
                            class="folder-card p-3" style="box-shadow:none;">
                            <div class="card-body" style="padding:4px;">
                                <p style="color: black;">
                                    <img src="images/folder.png" alt="Folder">
                                    <?php echo $committee['committee_name']; ?>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($noCommittees): ?>
                <div class="alert alert-info" role="alert"
                    style="border: 2px dotted #007bff; padding: 15px; text-align: center; margin: 20px;">
                    No committees have uploaded any folders yet.
                </div>
            <?php endif; ?>

        </div>
    </div>


    <!-- Footer -->
    <?php include '../footer.php' ?>


    <!-- Modal for welcome message -->
    <div class="modal fade" id="welcomeAdminModal" tabindex="-1" role="dialog" aria-labelledby="welcomeAdminModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="welcomeAdminModalLabel">Welcome, Admin!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Welcome to the media portal. Here, you can view the folders uploaded by various committees and
                    manage the approval or rejection of items. To get started, click on any folder(if present) to review
                    its contents.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Understood</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($noCommittees): ?>
                $('#welcomeAdminModal').modal('show');
            <?php endif; ?>
        });
    </script>

</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>