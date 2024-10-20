<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-image: url('https://wallpapers.com/images/hd/orange-clear-frames-bdvamh3q0f3xkc33.jpg');
      background-size: cover; /* Make the background cover the entire body */
      background-position: center; /* Center the image */
      background-repeat: no-repeat; /* Prevent the image from repeating */
      margin: 0;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }
    
    
    .navbar {
     
      color: black;
                font-style: bold;
      font-size: larger;
      width: 100%;
      margin-bottom: auto;
    }
    
    .navbar-toggler-icon {
      background-color: none;
    }
    
    .nav-item {
      color: white;
    }
    
    form {
      background-color: #fff;
      padding: 70px;
      border-radius: 8px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      width: 400px;
      text-align: center;
      margin: auto;
      margin-top:10px;
    }
    
    h2 {
      color: #060101;
      margin-bottom: 30px;
      font-size: 34px;
    }
    
    label {
      display: block;
      margin: 15px 0 8px;
      color: #555;
      font-size: 14px;
      text-align: left;
    }
    
    input {
      width: 100%;
      padding: 12px;
      margin: 8px 0 20px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
      transition: border-color 0.3s;
    }
    
    input:focus {
      border-color: #4caf50;
    }
    
    .forgot-password {
      text-align: right;
      margin-top: -10px;
      margin-bottom: 20px;
      color: #555;
    }
    
    button {
      background-color: #b7202e;
      color: white;
      padding: 15px 25px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }
    
    button:hover {
      background-color: #45a049;
    }
    
    .footer {
      background-color: #333; /* Set your desired background color */
      color: #fff; /* Set your desired text color */
      padding: 15px 0;
      text-align: center;
    }
    
    .footer img {
      max-width: 100%;
      height: auto;
    }
    
    .social-links a {
      color: #fff;
      margin: 0 10px;
    }
    
    /* Additional Styling for Responsive Design */
    @media (max-width: 576px) {
    
      .navbar-brand img {
            width: 80px; /* Reduce logo size */
        }
    
      form {
          width: 70%;
          margin-top: 10px;
      }
      h2 {
        font-size: 20px;
    
      }
      .credentials{
        font-size: 12px;
    
      }
    
    }
    /* Define the keyframes for the fade-in animation */
    @keyframes fadeIn {
    from {
    opacity: 0;
    }
    to {
    opacity: 1;
    }
    }
    
    /* Apply the fade-in animation to the login form */
    .fade-in {
    animation: fadeIn 1s ease-in-out;
    }
    
    
            label {
        display: block;
        margin: 15px 0 5px; /* Adjust margin for better spacing */
        color: #b7202e;
        font-size: 15px;
        font-weight: 500;
        text-align: left;
    }
    
    input {
        width: 100%;
        padding: 12px;
        margin: 8px 0 20px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 8px; /* Rounded corners for input fields */
        font-size: 16px;
        transition: border-color 0.3s, box-shadow 0.3s; /* Add transition for shadow */
    }
    
    input:focus {
        border-color: #4caf50; /* Change border color on focus */
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5); /* Add a subtle shadow effect */
    }
    
    .credentials {
                color: black;
                font-style: bold;
                padding: 25px;
                font-family: Arial, sans-serif;
                text-align: center;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
            }
    
            .credentials p {
                margin: 8px 0;
                font-size: 1rem;
                line-height: 1.5;
                font-weight: 500;
            }
    
            .credentials p span {
                font-weight: bold;
            }
    
            @media (max-width: 576px) {
               
    
                .credentials p {
                    font-size: 0.8rem; /* Adjusted image size */
                }
            } 
    </style>
    </head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Login
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="userlogin.php">User Login</a>
                        <a class="dropdown-item" href="../admin/facultylogin.php">Admin Login</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../committee/login.html">Committee login</a>
                    </div>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="userlogin.php">User portal</a>
                </li>
                
               
            </ul>
        </div>
    </nav>

    <form id="login-form" action="user_login.php" method="post" class="fade-in">
    <h2>User Login</h2>
    <div id="error-message" style="display: none; color: red; text-align: center;">Incorrect username or password.</div>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    

    <button type="submit">Login</button>
</form>


<div class="credentials">
      <p class="c-text">Project Made By: <span>Anoushka Vyas</span> </p>
  </div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
        // Check if there's an error parameter in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        if (error === 'invalid_credentials') {
            // Display the error message container
            document.getElementById('error-message').style.display = 'block';
        }
    </script>
</body>
</html>
