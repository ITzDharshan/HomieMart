<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Check if the user is logged in, if not redirect them to the login page
if (!isset($_SESSION['customer_email']) && basename($_SERVER['PHP_SELF']) != 'index.php')
{
    header("Location: index.php");
    exit();
}

$query = "SELECT role FROM register WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['customer_email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['role'] = $row['role'];
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  header("Refresh:2");
    // Retrieve and sanitize input
    $username = trim($_POST['fname']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit();
    }

    // Prepare SQL query to fetch user details
    $stmt = $conn->prepare("SELECT id, email, password FROM register WHERE fname = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if a user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $email, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['customer_email'] = $email;

            // Redirect to home page
            header("Location: home.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="images/shopping.png">
  <link rel="stylesheet" href="style.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
  <title>HomieMart</title>
</head>
<body>
  <header class="main-section">
    <nav class="navbar">
      <div class="logo">HomieMart</div>
      <ul class="nav-links">
        <li> <a href="index.php" class="active">Home</a></li>
        <li> <a href="<?php echo (isset($_SESSION['customer_email']) ? 'collaborate.php' : 'signin.php?redirect=collaborate'); ?>">Collaborate</a></li>
        <li><a href="<?php echo (isset($_SESSION['customer_email']) ? 'marketplace.php' : 'signin.php?redirect=marketplace'); ?>">Marketplace</a></li>
        <li> <a href="aboutus.php">About us</a></li>
        <li> <a href="contact.php">Contact</a></li>
      </ul>

      <div class="nav-icon">
        <i class='bx bx-search' id="search-icon"></i> 
        <input type="text" class="search-input" id="search-input" placeholder="Search here...">
        <a href="<?php echo (isset($_SESSION['customer_email']) ? 'cart.php' : 'signin.php?redirect=cart'); ?>">
          <i class='bx bx-cart'></i> 
        </a>
        <a href="<?php echo (isset($_SESSION['customer_email']) ? 'notification.php' : 'signin.php?redirect=notification'); ?>">
          <i class='bx bx-bell'></i> 
        </a>
        
        <div class="profile-container">
          <i class='bx bx-user' id="profile-icon"></i>
          
          <div class="profile-popup" id="profile-popup">
            <?php
              if (isset($_SESSION['customer_email']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Customer') {
                // If the logged-in user is a customer, display the email
                echo "<p>Welcome, " . $_SESSION['customer_email'] . "</p>";
                echo "<a href='logout.php' class='logout-btn'>Logout</a>";
            } elseif (!isset($_SESSION['customer_email']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'Customer')) {
                // If the user is not logged in or their role is not 'Customer', show login options
                echo "<p>Welcome to HomieMart</p>";
                echo "<a href='signin.php' class='signin-btn'>Sign In</a>";
                echo "<p>New here? <a href='signup.php' class='signup-link'>Sign Up</a></p>";
            }
            ?>
          </div>
        </div>

        <div id="menu-icon"><i class='bx bx-menu'></i> </div>
      </div>
    </nav>

    <div class="home">
      <button id="prev-btn" class="prev-btn"><i class='bx bx-chevron-left'></i></button>
      <div class="home-content">
        <h1>Empowering Homemakers Through Creativity and Commerce</h1>
        <div class="home-btn">
          <button class="join-btn">Join with us</button>
          <button class="learn-btn">learn more</button>
        </div>
      </div>
      <button id="next-btn" class="next-btn"><i class='bx bx-chevron-right'></i></button>
    </div>
  </header>

  <h1>Categories</h1>
  <section class="categories-section">
      <div class="categories">
          <div class="category-card dye-crafts">
              <h2>Dye Crafts</h2>
              <p>Hand-dyed fabrics and artistic creations.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'dyecraft.php' : 'signin.php?redirect=dyecraft'); ?>">Discover More</a>
          </div>
          <div class="category-card gardening-products">
              <h2>Gardening Products</h2>
              <p>Indoor plants, gardening kits, and planters.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'gardening.php' : 'signin.php?redirect=gardening'); ?>">Discover More</a>
          </div>
          <div class="category-card baking">
              <h2>Baking</h2>
              <p>Cakes, cookies, bread, and pastries.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'baking.php' : 'signin.php?redirect=baking'); ?>">Discover More</a>
          </div>
          <div class="category-card handicrafts">
              <h2>Handicrafts</h2>
              <p>Pottery, jewelry, decor, and more.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'handicraft.php' : 'signin.php?redirect=handicraft'); ?>">Discover More</a>
          </div>
          <div class="category-card sustainable-products">
              <h2>Sustainable Products</h2>
              <p>Eco-friendly bags and reusable household items.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'sustainable.php' : 'signin.php?redirect=sustainable'); ?>">Discover More</a>
          </div>
          <div class="category-card cooking">
              <h2>Cooking</h2>
              <p>Home-cooked meals, snacks, and desserts.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'cooking.php' : 'signin.php?redirect=cooking'); ?>">Discover More</a>
          </div>
          <div class="category-card art-and-painting">
              <h2>Art and Painting</h2>
              <p>Decorative wall art, paintings, and illustrations.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'art.php' : 'signin.php?redirect=art'); ?>">Discover More</a>
          </div>
          <div class="category-card knitting-and-crochet">
              <h2>Knitting and Crochet</h2>
              <p>Woolen clothing, blankets, and accessories.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'knitting.php' : 'signin.php?redirect=knitting'); ?>">Discover More</a>
          </div>
          <div class="category-card wellness-products">
              <h2>Wellness Products</h2>
              <p>Homemade soaps, skincare, and remedies.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'wellness.php' : 'signin.php?redirect=wellness'); ?>">Discover More</a>
          </div>
          <div class="category-card stationery">
              <h2>Stationery and Paper Crafts</h2>
              <p>Greeting cards, journals, and bookmarks.</p>
              <a href="<?php echo (isset($_SESSION['customer_email']) ? 'paper.php' : 'signin.php?redirect=paper'); ?>">Discover More</a>
          </div>
      </div>
  </section>

  <section class="info-section">
    <div class="info-content">
        <h2>HomeHub empowers homemakers by connecting their talents with customers seeking authentic, handmade products. <br> Join our community to explore creativity and commerce!</h2>
        <button class="learn-more-btn">Learn More</button>
    </div>
    <div class="image-gallery">
        <div class="main-image">
          <img src="images/image1.jpg" alt="Main image" />
        </div>
        <div class="thumbnail-images">
            <div class="thumbnail">
              <img src="images/image2.jpg" alt="Main image" />
            </div>
            <div class="thumbnail">
              <img src="images/image3.jpg" alt="Main image" />
            </div>
            <div class="thumbnail">
              <img src="images/image4.jpg" alt="Main image" />
            </div>
        </div>
    </div>
  </section>

  <section class="testimonials">
    <h2 class="testimonials-title">Testimonials</h2>
    <div class="testimonials-container">
      <!-- First set of testimonials -->
      <div class="testimonial-set active">
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>Impressed <br> by the professionalism and attention to detail.
          </p>
          <div class="user-info">
            <img src="images/cute.png" alt="User 1" class="user-image">
            <div class="user-details">
              <p class="user-name">Sophia Williams </p>
              <p class="user-handle">@sophiaw</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>A seamless experience from start to finish. Highly recommend!
          </p>
          <div class="user-info">
            <img src="images/Screenshot (16).png" alt="User 2" class="user-image">
            <div class="user-details">
              <p class="user-name">Liam Brown</p>
              <p class="user-handle">@olivia_smith</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>An exceptional experience, exceeded all expectations. Will definitely return!
          </p>
          <div class="user-info">
            <img src="images/Screenshot (16).png" alt="User 2" class="user-image">
            <div class="user-details">
              <p class="user-name">Noah Johnson</p>
              <p class="user-handle">@noah_j</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>Absolutely amazing! The service was top-notch and exceeded my expectations
          </p>
          <div class="user-info">
            <img src="images/Screenshot (16).png" alt="User 2" class="user-image">
            <div class="user-details">
              <p class="user-name">Emma Davis</p>
              <p class="user-handle">@emma_d</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>Reliable and trustworthy. Made my life so much easier.
          </p>
          <div class="user-info">
            <img src="images/cute.png" alt="User 3" class="user-image">
            <div class="user-details">
              <p class="user-name">Olivia Smith</p>
              <p class="user-handle">@olivia_smith</p>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Second set of testimonials -->
      <div class="testimonial-set">
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>Outstanding service from beginning to end. Truly remarkable!
          </p>
          <div class="user-info">
            <img src="images/cute.png" alt="User 1" class="user-image">
            <div class="user-details">
              <p class="user-name">Lucas Miller </p>
              <p class="user-handle">@lucas.m</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>Every detail was handled perfectly. Couldn’t ask for more!
          </p>
          <div class="user-info">
            <img src="images/Screenshot (16).png" alt="User 2" class="user-image">
            <div class="user-details">
              <p class="user-name">Ava Martinez</p>
              <p class="user-handle">@ava_martinez</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>Smooth, efficient, and professional. Exceeded my expectations!
          </p>
          <div class="user-info">
            <img src="images/Screenshot (16).png" alt="User 2" class="user-image">
            <div class="user-details">
              <p class="user-name">Ethan Garcia</p>
              <p class="user-handle">@ethan.garcia</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>An unforgettable experience delivered with excellence at every step.
          </p>
          <div class="user-info">
            <img src="images/Screenshot (16).png" alt="User 2" class="user-image">
            <div class="user-details">
              <p class="user-name">Isabella White</p>
              <p class="user-handle">@bella_w</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">
            <span class="quote">“</span>Impressive attention to detail and impeccable execution. Will return!
          </p>
          <div class="user-info">
            <img src="images/cute.png" alt="User 3" class="user-image">
            <div class="user-details">
              <p class="user-name">Zoe Lee</p>
              <p class="user-handle">@zoe_l</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  
    <div class="pagination">
      <span class="dot active" data-index="0"></span>
      <span class="dot" data-index="1"></span>
    </div>
  </section>

  <div class="Trending-head">
    <h2>Trending Items</h2>
  </div>

  <?php
    $latestImagesSql = "SELECT product_image FROM products ORDER BY id DESC LIMIT 8";
    $latestImagesResult = mysqli_query($conn, $latestImagesSql);
  ?>

  <section class="trending-items">
      <div class="slider">
          <?php
              if ($latestImagesResult && mysqli_num_rows($latestImagesResult) > 0) {
                  $i = 1;
                  while ($imageRow = mysqli_fetch_assoc($latestImagesResult)) {
                      echo '<span style="--i: ' . $i . '"><img src="' . htmlspecialchars($imageRow['product_image']) . '" alt="Trending Item" onclick="zoomIn(this)" /></span>';
                      $i++;
                  }
              } else {
                  echo "<p>No trending items available.</p>";
              }
          ?>
      </div>
  </section>
  
  <!-- Footer Section -->
  <footer class="footer">
    <div class="footer-about">
        <h3>HomieMart</h3>
        <p>Join our community of talented individuals and start your journey towards success and financial independence. At Homiemart, we’re here to support you every step of the way!</p>
    </div>
    <div class="footer-links">
        <h3>Quick Links</h3>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="<?php echo (isset($_SESSION['customer_email']) ? 'collaborate.php' : 'signin.php?redirect=collaborate'); ?>">Collaborate</a></li>
            <li><a href="<?php echo (isset($_SESSION['customer_email']) ? 'marketplace.php' : 'signin.php?redirect=marketplace'); ?>">Marketplace</a></li>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </div>
    <div class="footer-subscribe">
        <h3>HomieMart gave me the plateform to showcase my talent and reach new customers!</h3>
        <form action="#">
            <input type="email" placeholder="Enter your email">
            <button type="submit">Subscribe</button>
        </form>
    </div>
  </footer>

<!-- Back to top button -->
<a href="#" id="backToTop" class="back-to-top">
  <i class='bx bx-up-arrow'></i>
</a>

<button id="chatbot-toggler">
  <span class="material-symbols-rounded">mode_comment</span>
  <span class="material-symbols-rounded">close</span>
</button>
<div class="chatbot-popup">
  <div class="chat-header">
    <div class="header-info">
      <svg class="chatbot-logo" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
        <path
          d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"
        />
      </svg>
      <h2 class="logo-text">HomieMart AI Assistant</h2>
    </div>
    <button id="close-chatbot" class="material-symbols-rounded">keyboard_arrow_down</button>
  </div>
  <div class="chat-body">
    <div class="message bot-message">
      <svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
        <path
          d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"
        />
      </svg>
      <!-- prettier-ignore -->
      <div class="message-text"> Hey there  <br /> How can I help you today? </div>
    </div>
  </div>
  <!-- Chatbot Footer -->
  <div class="chat-footer">
    <form action="#" class="chat-form">
      <textarea placeholder="Message..." class="message-input" required></textarea>
      <div class="chat-controls">
        <button type="button" id="emoji-picker" class="material-symbols-outlined">sentiment_satisfied</button>
        <div class="file-upload-wrapper">
          <input type="file" accept="image/*" id="file-input" hidden />
          <img src="#" />
          <button type="button" id="file-upload" class="material-symbols-rounded">attach_file</button>
          <button type="button" id="file-cancel" class="material-symbols-rounded">close</button>
        </div>
        <button type="submit" id="send-message" class="material-symbols-rounded">arrow_upward</button>
      </div>
    </form>
  </div>
</div>

</body>
  <script src="script.js"></script>
</html>