<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $selected_business_type = $_POST['homemakerBusiness']; // Get selected business type

    // Prepare the query
    $stmt = $conn->prepare("SELECT password, role, business_type FROM register WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password, $role, $business_type);
        $stmt->fetch();

        if ($role === 'Homemaker') {
            if (password_verify($password, $db_password)) {
                if ($selected_business_type === $business_type) { // Validate selected business type
                    $redirect_pages = [
                        'Dye Crafts' => 'DC_home.php',
                        'Gardening Products' => 'GP_home.php',
                        'Baking' => 'BK_home.php',
                        'Handicrafts' => 'HC_home.php',
                        'Sustainable Products' => 'SP_home.php',
                        'Cooking' => 'CK_home.php',
                        'Art and Painting' => 'AP_home.php',
                        'Knitting and Crochet' => 'KC_home.php',
                        'Wellness Products' => 'WP_home.php',
                        'Stationery and Paper Crafts' => 'SPC_home.php'
                    ];

                    if (isset($redirect_pages[$business_type])) {
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $role;
                        $_SESSION['business_type'] = $business_type;

                        header("Location: " . $redirect_pages[$business_type]);
                        exit();
                    } else {
                        echo "<script>alert('Invalid business category.'); window.location.href='HM_index.php';</script>";
                    }
                } else {
                    echo "<script>alert('Selected business type does not match your registered category.'); window.location.href='HM_index.php';</script>";
                }
            } else {
                echo "<script>alert('Incorrect password. Please try again.'); window.location.href='HM_index.php';</script>";
            }
        } else {
            echo "<script>alert('Access restricted to homemakers only.'); window.location.href='HM_index.php';</script>";
        }
    } else {
        echo "<script>alert('No homemaker account found with this email.'); window.location.href='HM_index.php';</script>";
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
    <link rel="stylesheet" href="HM_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="icon" type="image/png" href="images/shopping.png">
    <title>HomieMart-HomeMaker | Sign In</title>
</head>
<body>
    <div class="container">
        <div class="log-form">
            <h2>Sign In</h2>
            <div class="text-overlay">
                <h1>Welcome Back!</h1>
            </div>
            <form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="loginForm">
                <input type="email" name="email" id="email" placeholder="Email" required>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class='bx bx-hide' id="togglePassword"></i>
                </div>
                <div class="category-group">
                    <label for="homemakerBusiness">Select Homemaker's Business Type:</label>
                    <select name="homemakerBusiness" id="homemakerBusiness" class="select-box" required>
                        <option value="">Select Business Type</option>
                        <option value="Dye Crafts">Dye Crafts</option>
                        <option value="Gardening Products">Gardening Products</option>
                        <option value="Baking">Baking</option>
                        <option value="Handicrafts">Handicrafts</option>
                        <option value="Sustainable Products">Sustainable Products</option>
                        <option value="Cooking">Cooking</option>
                        <option value="Art and Painting">Art and Painting</option>
                        <option value="Knitting and Crochet">Knitting and Crochet</option>
                        <option value="Wellness Products">Wellness Products</option>
                        <option value="Stationery and Paper Crafts">Stationery and Paper Crafts</option>
                    </select>
                </div>
                <p class="forgot"><a href="resetpassword.php">Forgot Password?</a></p>
                <button type="submit" class="signin-btn">Login</button>
            </form>

        </div>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.classList.remove('bx-hide');
                this.classList.add('bx-show');
            } else {
                passwordField.type = 'password';
                this.classList.remove('bx-show');
                this.classList.add('bx-hide');
            }
        });       
    </script>
</body>
</html>
