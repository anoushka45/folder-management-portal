<?php

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
}
// Query to fetch valid notifications only
// Fetch notifications only for existing events with associated media files
$notificationsQuery = "SELECT DISTINCT notifications.* 
                       FROM notifications 
                       INNER JOIN event ON notifications.event_id = event.event_id 
                       INNER JOIN mediafile ON mediafile.event_id = event.event_id 
                       WHERE notifications.notification_type = 'rejected' 
                       AND mediafile.committee_id = '$committeeId'
                       ORDER BY notifications.timestamp DESC 
                       LIMIT 5";


$resultNotifications = mysqli_query($conn, $notificationsQuery);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .navbar {
            background-image: url('https://wallpapers.com/images/hd/orange-clear-frames-bdvamh3q0f3xkc33.jpg');
    background-size: cover; /* Ensure the image covers the entire navbar */
    background-position: center; /* Center the image */
    background-repeat: no-repeat;             /* Set your desired background color */
        }

        .navbar-dark .navbar-brand {
            color: #ffffff;
            /* Set your desired text color */
        }

        .navbar-dark .navbar-brand img {
            max-height: 40px;
            width: auto;
            object-fit: cover;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #ffffff;
            /* Set your desired text color */
        }

        .navbar-dark .navbar-toggler-icon {
            color: #ffffff;
            /* Set your desired text color */
        }

        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }

        @media (max-width: 576px) {
            .dropdown-menu {
                width: 100%;
                left: 0 !important;
                right: 0 !important;
                max-height: 200px;
            }

         
        }

        .logout-button {
            cursor: pointer;
            background-color: coral;
            border: none;
            color: black;
            margin-top: 8px;
            margin-left:0px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php" style="margin-top:8px;">All Events</a>
                </li>
                <li class="nav-item dropdown" style="margin-top: 8px;">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Notifications <span
                            class="badge badge-danger"><?php echo mysqli_num_rows($resultNotifications); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown"
                        style=" overflow-y: auto;">
                        <?php if (mysqli_num_rows($resultNotifications) > 0): ?>
                            <ul class="list-unstyled">
                                <?php while ($notification = mysqli_fetch_assoc($resultNotifications)): ?>
                                    <li class="dropdown-item">
                                        <?php
                                        // Format the timestamp
                                        $formattedTimestamp = date('d-m-Y');
                                        
                                        echo "{$notification['message']} <small class='text-muted d-block'>({$formattedTimestamp})</small>";
                                        ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <div class="dropdown-item" href="#">No new notifications</div>
                        <?php endif; ?>

                    </div>
                </li>
                <li class="nav-item">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="nav-link mx-0">
                        <button type="submit" name="logout" class="logout-button "
                            style="color:white;">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</body>

</html>

