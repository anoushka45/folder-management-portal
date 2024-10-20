<?php


// Query to fetch valid notifications only
$sql = "SELECT notifications.* 
        FROM notifications 
        INNER JOIN event ON notifications.event_id = event.event_id 
        WHERE (notification_type = 'Folder Added' OR notification_type = 'Restore Request') 
        ORDER BY notifications.timestamp DESC 
        LIMIT 5";
$result = mysqli_query($conn, $sql);
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

            <li class="nav-item">
                <a class="nav-link" href="admin_index.php" style="margin-top:8px;">All Committees</a>
            </li>
            <li class="nav-item dropdown" style="margin-top:8px;">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Notifications <span class="badge badge-danger"><?php echo count($notifications); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown"
                    style="max-height: 300px; overflow-y: auto;">
                    <!-- Generate HTML for each notification item -->
                    <?php foreach ($notifications as $notification): ?>
                        <?php
                        // Fetch additional details such as committee_name and timestamp for each notification
                        $committeeName = $notification['committee_name'];
                        $notificationType = $notification['notification_type'];
                        $message = $notification['message'];
                        $timestamp = date("F j, Y"); // Format timestamp
                        ?>
                        <a class="dropdown-item" href="#">
                            <strong> (<?php echo $committeeName; ?>):</strong>
                            <?php echo $message; ?>
                            <div class="small text-muted"><?php echo $timestamp; ?></div> <!-- Display timestamp -->
                        </a>
                    <?php endforeach; ?>
                    <!-- Display a message if there are no notifications -->
                    <?php if (empty($notifications)): ?>
                        <a class="dropdown-item" href="#">No new notifications</a>
                    <?php endif; ?>
                </div>
            </li>
            <li class="nav-item">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="nav-link">
                    <button type="submit" name="logout" class="btn btn-link" style="color:white;">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</nav>

</body>
</html>




