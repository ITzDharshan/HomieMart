<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <link rel="icon" type="image/png" href="images/shopping.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }

        .popup-content { 
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            width: 350px;
            height: 400px;
            max-width: 90%;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .popup.show .popup-content {
            display: block;
        }

        .popup-content h1 {
            font-size: 34px;
            margin-bottom: 10px;
            color: red;
            font-weight: lighter;
        }

        .popup-content p {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .icon {
            width: 200px;
            height: 200px;
            margin: 10px;
        }

        #popup-button {
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 40px;
            text-decoration: none;
            color: royalblue;
        }

        #popup-button:hover {
            color: darkgoldenrod;
        }
    </style>
</head>
<body>

    <!-- Popup container -->
    <div class="popup-content">
        <img id="popup-icon" class="icon" src="images/error-404.gif" alt="Popup Icon" />
        <h1 id="popup-title">🚫 Access Denied</h1>
        <p id="popup-message">You are not authorized to access this page.</p>
        <a href="HM_index.php" id="popup-button">Go to Login</a>
    </div>

</body>
</html>
