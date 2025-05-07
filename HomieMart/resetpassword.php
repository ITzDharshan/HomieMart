<?php
include 'db_connection.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $toastClass = "warning";
    } elseif ($password === $confirmPassword) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email exists
        $stmt = $conn->prepare("SELECT * FROM register WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update password
            $updateStmt = $conn->prepare("UPDATE register SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $hashedPassword, $email);

            if ($updateStmt->execute()) {
                $message = "Password updated successfully";
                $toastClass = "success";
            } else {
                $message = "Error updating password";
                $toastClass = "danger";
            }
            $updateStmt->close();
        } else {
            $message = "Email not found";
            $toastClass = "warning";
        }
        $stmt->close();
    } else {
        $message = "Passwords do not match";
        $toastClass = "warning";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/shopping.png">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        .toast {
            padding: 10px;
            border-radius: 5px;
            color: #fff;
            margin-bottom: 20px;
        }

        .toast.success {
            background-color:rgb(0, 129, 30);
        }

        .toast.danger {
            background-color:rgb(124, 0, 12);
        }

        .toast.warning {
            background-color:rgb(134, 101, 0);
            color: #000;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form .form-control {
            margin-bottom: 15px;
        }

        label {
            margin-bottom: 5px;
            font-weight: lighter;
        }

        input {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: lighter;
            width: 70%;
            display: block;
            margin: 0 auto;
            text-align: center;
        }

        button:hover {
            background-color: #555;
        }

        .text-center {
            text-align: center;
            font-weight: lighter;
        }

        .text-center a {
            color: navy;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        .icon {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .icon i {
            font-size: 50px;
            color: green;
        }

        .email-check {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if ($message): ?>
            <div class="toast <?php echo htmlspecialchars($toastClass); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="icon">
                <i class="fa fa-user-circle-o"></i>
            </div>
            <h3 class="text-center">Change Your Password</h3>

            <div class="form-control">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
                <span id="email-check" class="email-check"></span>
            </div>

            <div class="form-control">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-control">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>

            <button type="submit">Reset Password</button>

            <div class="text-center">
                <p>
                    <a href="./signin.php">Login</a>
                </p>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('#email').on('blur', function () {
                var email = $(this).val();
                if (email) {
                    $.ajax({
                        url: 'check_email.php',
                        type: 'POST',
                        data: { email: email },
                        success: function (response) {
                            if (response.trim() === 'exists') {
                                $('#email-check').html('<i class="fa fa-check" style="color: green;"></i>');
                            } else {
                                $('#email-check').html('<i class="fa fa-times" style="color: red;"></i>');
                            }
                        }
                    });
                } else {
                    $('#email-check').html('');
                }
            });
        });
    </script>
</body>

</html>
