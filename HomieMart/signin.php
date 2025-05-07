<?php
include 'db_connection.php';

$message = "";
$toastClass = "";

if (isset($_GET['redirect'])) {
  $redirectPage = htmlspecialchars($_GET['redirect']);
  echo "<p>Please sign in to access the $redirectPage page.</p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    header("Refresh:2");

    // Prepare and execute
    $stmt = $conn->prepare("SELECT password FROM register WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $db_password)) {
            $message = "Login successful";
            $toastClass = "bg-success";

            // Start the session and redirect to the dashboard or home page
            session_start();
            $_SESSION['customer_email'] = $email;
            header("Location: index.php");
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
    $conn->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="signin.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="images/shopping.png">
  <title>HomieMart | Sign In</title>
  <style>
    .toast {
      padding: 10px 20px;
      border-radius: 5px;
      margin: 20px auto;
      width: 80%;
      text-align: center;
      color: black;
    }
   
  </style>
</head>
<body>
    <div class="container">
        <div class="left">
          <img src="images/women1.jpg" alt="Sign In" class="bg-image">
          <h1>Step into your personalized world<br><span>sign in to continue!</span></h1>
        </div>
        <div class="right">
          <h2>Sign In</h2>
          <div class="text-overlay">
            <h1>Welcome Back!</h1>
          </div>
          <!-- Display Message -->
          <?php if (!empty($message)): ?>
            <div class="toast <?php echo $toastClass; ?>"><?php echo $message; ?></div>
          <?php endif; ?>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <p class="forgot"><a href="resetpassword.php">Forgot Password?</a></p>
            <button type="submit" class="signin-btn">Login</button>
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            <hr style="border: 1px solid gray; width: 100%;">
            <p>Are you a Homemaker? <a href="HM_index.php">Login here</a></p>
          </form>
        </div>
      </div>      
</body>
</html>
