<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .error-content {
            margin-bottom: 20px;
        }

        h1 {
            font-size: 100px;
            margin: 0;
            color: #ff6347; /* Tomato color */
        }

        h2 {
            font-size: 24px;
            margin: 10px 0;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
        }

        .buttons {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3; /* Darker shade */
        }

        .btn-secondary {
            background-color: #6c757d; /* Bootstrap secondary color */
        }

        .btn-secondary:hover {
            background-color: #5a6268; /* Darker shade */
        }

        .error-image img {
            max-width: 400px;
            width: 100%;
            height: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-content">
            <h1>404</h1>
            <h2>Oops! Page Not Found</h2>
            <p>We're sorry, but the page you're looking for doesn't exist. It may have been moved or deleted.</p>
            <div class="buttons">
                <a href="/" class="btn">Back to Homepage</a>
                <a href="/contact" class="btn btn-secondary">Contact Us</a>
            </div>
        </div>
        <div class="error-image">
            <img src="https://example.com/path-to-your-image.png" alt="Error Image" />
        </div>
    </div>
</body>
</html>
