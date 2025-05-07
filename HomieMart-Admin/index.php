<?php
include 'db_connection.php';

$message = "";
$toastClass = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    header("Refresh:2");
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Prepare and execute SQL query
    if ($stmt = $conn->prepare("SELECT password FROM adminsign WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_password);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $db_password)) {
                session_start();
                $_SESSION['admin_email'] = $email;
                header("Location: home.php");
                exit();
            } else {
                $message = "Incorrect password";
                $toastClass = "bg-danger";
            }
        } else {
            $message = "Email not found";
            $toastClass = "bg-warning";
        }

        $stmt->close();
    } else {
        $message = "Database error: " . $conn->error;
        $toastClass = "bg-danger";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="icon" type="image/png" href="img/shopping.png">
    <title>HomieMart-Admin | Sign In</title>
</head>
<body>
<div class="container">
        <div class="log-form">
            <h2>Sign In</h2>
            <div class="text-overlay">
                <h1>Welcome Back!</h1>
                <?php if (!empty($message)): ?>
                    <div class="toast <?php echo $toastClass; ?>"><?php echo $message; ?></div>
                <?php endif; ?>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="loginForm">
                <input type="email" name="email" id="email" placeholder="Email" required>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <p class="forgot"><a href="resetadmin.php">Reset Password?</a></p>
                <button type="submit" class="signin-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>