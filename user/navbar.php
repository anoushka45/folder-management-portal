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
                            <a class="nav-link" href="user_index.php" style="margin-top:8px;">All Committees</a>
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




