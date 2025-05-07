<?php
include 'db_connection.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['fname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    header("Refresh:2");

    if ($password !== $confirmPassword) {
        $message = "Passwords do not match";
        $toastClass = "error";
    } else {
       
        $checkEmailStmt = $conn->prepare("SELECT email FROM register WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if ($checkEmailStmt->num_rows > 0) {
            $message = "Email ID already exists";
            $toastClass = "info";
        } else {
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Updated SQL query to include mobile number
            $stmt = $conn->prepare("INSERT INTO register (fname, email, mobile, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $mobile, $hashedPassword);

            if ($stmt->execute()) {
                $message = "Account created successfully";
                $toastClass = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $toastClass = "error";
            }

            $stmt->close();
        }

        $checkEmailStmt->close();
    }

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="signup.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="images/shopping.png">
  <title>HomieMart | Sign Up</title>
  <style>
    .toast {
      padding: 10px 20px;
      border-radius: 5px;
      margin: 20px auto;
      width: 80%;
      text-align: center;
      color: balck;
    }
    
    .error { background-color: #dc3545; }
    .info { background-color: #007bff; }
  </style>
</head>
<body>
    <div class="container">
        <div class="left">
          <img src="images/men.jpg" alt="Girl" class="bg-image">
          <h1>Become a part of something extraordinary<br><span>join us today!</span></h1>
        </div>
        <div class="right">
          <h2>Sign Up</h2>
          <?php if (!empty($message)): ?>
            <div class="toast <?php echo $toastClass; ?>"><?php echo $message; ?></div>
          <?php endif; ?>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="text" name="fname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="mobile" placeholder="Mobile Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Re-enter Password" required>
            <div class="checkbox">
              <input type="checkbox" id="terms" required>
              <label for="terms">I’ve read and agree with Terms of Service and our Privacy Policy</label>
            </div>
            <button type="submit"  class="signup-btn">Sign up</button>
            <p>Already have an account? <a href="signin.php">Sign in</a></p>
          </form>
        </div>
      </div>      
</body>
</html>
