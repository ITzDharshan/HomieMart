<?php
session_start();
include 'db_connection.php'; // Include your database connection

if (!isset($_SESSION['customer_email'])) {
  // Redirect to the login page
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
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="images/shopping.png">
  <link rel="stylesheet" href="marketplace.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
  <title>HomieMart</title>
</head>
<style>
  ::-webkit-scrollbar-thumb {
  background: var(--scroll-thumb-color, darkgoldenrod);
}

::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

::-webkit-scrollbar-track {
  background: #2e2e2e;
}

::-webkit-scrollbar-thumb {
  background: darkgoldenrod;
  border-radius: 5px;
  transition: background 0.3s ease;
}

* {
  scrollbar-width: thin;
  scrollbar-color: darkgoldenrod #2e2e2e;
}
  #chatbot-toggler {
  position: fixed;
  bottom: 110px;
  right: 20px;
  border: none;
  height: 65px;
  width: 65px;
  display: flex;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: black;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
  z-index: 1000;
}
#chatbot-toggler:hover {
  background: darkgoldenrod;
}
body.show-chatbot #chatbot-toggler {
  transform: rotate(90deg);
}
#chatbot-toggler span {
  color: #fff;
  position: absolute;
}
#chatbot-toggler span:last-child,
body.show-chatbot #chatbot-toggler span:first-child {
  opacity: 0;
}
body.show-chatbot #chatbot-toggler span:last-child {
  opacity: 1;
}
.chatbot-popup {
  position: fixed;
  right: 90px;
  bottom: 30px;
  width: 400px;
  overflow: hidden;
  background: rgba(255, 242, 126, 0.3);
  border-radius: 15px;
  opacity: 0;
  pointer-events: none;
  transform: scale(0.2);
  transform-origin: bottom right;
  box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1),
      0 32px 64px -48px rgba(0, 0, 0, 0.5);
  transition: all 0.1s ease;
  backdrop-filter: blur(20px);
}
body.show-chatbot .chatbot-popup {
  opacity: 1;
  pointer-events: auto;
  transform: scale(1);
}
.chat-header {
  display: flex;
  align-items: center;
  padding: 15px 22px;
  background: darkgoldenrod;
  justify-content: space-between;
}
.chat-header .header-info {
  display: flex;
  gap: 10px;
  align-items: center;
}
.header-info .chatbot-logo {
  width: 35px;
  height: 35px;
  padding: 6px;
  fill: darkgoldenrod;
  flex-shrink: 0;
  background: #fff;
  border-radius: 50%;
}
.header-info .logo-text {
  color: #fff;
  font-weight: lighter;
  font-size: 1.31rem;
  letter-spacing: 0.02rem;
}
.chat-header #close-chatbot {
  border: none;
  color: #fff;
  height: 40px;
  width: 40px;
  font-size: 1.9rem;
  margin-right: -10px;
  padding-top: 2px;
  cursor: pointer;
  border-radius: 50%;
  background: none;
  transition: 0.2s ease;
}
.chat-body {
  padding: 25px 22px;
  gap: 20px;
  display: flex;
  height: 250px;
  overflow-y: auto;
  margin-bottom: 82px;
  flex-direction: column;
  scrollbar-width: thin;
  scrollbar-color: #ccccf5 transparent;
}
.chat-body,
.chat-form .message-input:hover {
  scrollbar-color: #ccccf5 transparent;
}
.chat-body .message {
  display: flex;
  gap: 11px;
  align-items: center;
}
.chat-body .message .bot-avatar {
  width: 35px;
  height: 35px;
  padding: 6px;
  fill: #fff;
  flex-shrink: 0;
  margin-bottom: 2px;
  align-self: flex-end;
  border-radius: 50%;
  background: darkgoldenrod;
}
.chat-body .message .message-text {
  padding: 12px 16px;
  max-width: 75%;
  font-size: 0.95rem;
}
.chat-body .bot-message.thinking .message-text {
  padding: 2px 16px;
}
.chat-body .bot-message .message-text {
  background: #fff6dc;
  border-radius: 13px 13px 13px 3px;
  border: 2px solid darkgoldenrod;
}
.chat-body .user-message {
  flex-direction: column;
  align-items: flex-end;
}
.chat-body .user-message .message-text {
  color: #fff;
  background: darkgoldenrod;
  border-radius: 13px 13px 3px 13px;
}
.chat-body .user-message .attachment {
  width: 50%;
  margin-top: -7px;
  border-radius: 13px 3px 13px 13px;
}
.chat-body .bot-message .thinking-indicator {
  display: flex;
  gap: 4px;
  padding-block: 15px;
}
.chat-body .bot-message .thinking-indicator .dot {
  height: 7px;
  width: 7px;
  opacity: 0.7;
  border-radius: 50%;
  background: darkgoldenrod;
  animation: dotPulse 1.8s ease-in-out infinite;
}
.chat-body .bot-message .thinking-indicator .dot:nth-child(1) {
  animation-delay: 0.2s;
}
.chat-body .bot-message .thinking-indicator .dot:nth-child(2) {
  animation-delay: 0.3s;
}
.chat-body .bot-message .thinking-indicator .dot:nth-child(3) {
  animation-delay: 0.4s;
}
@keyframes dotPulse {
  0%,
  44% {
      transform: translateY(0);
  }
  28% {
      opacity: 0.4;
      transform: translateY(-4px);
  }
  44% {
      opacity: 0.2;
}
}
.chat-footer {
  position: absolute;
  bottom: 0;
  width: 90%;
  background: #fff;
  padding: 15px 22px 20px;
}
.chat-footer .chat-form {
  display: flex;
  align-items: center;
  position: relative;
  background: #fff;
  border-radius: 32px;
  outline: 2px solid darkgoldenrod;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.06);
  transition: 0s ease, border-radius 0s;
}
.chat-form:focus-within {
  outline: 2px solid darkgoldenrod;
}
.chat-form .message-input {
  width: 100%;
  height: 20px;
  outline: none;
  resize: none;
  border: none;
  max-height: 180px;
  scrollbar-width: thin;
  border-radius: inherit;
  font-size: 12px;
  padding: 14px 0 12px 18px;
  scrollbar-color: transparent transparent;
}
.chat-form .chat-controls {
  gap: 3px;
  height: 47px;
  display: flex;
  padding-right: 6px;
  align-items: center;
  align-self: flex-end;
}
.chat-form .chat-controls button {
  height: 35px;
  width: 35px;
  border: none;
  cursor: pointer;
  color: darkgoldenrod;
  border-radius: 50%;
  font-size: 1.15rem;
  background: none;
  transition: 0.2s ease;
}
.chat-form .chat-controls button:hover,
body.show-emoji-picker .chat-controls #emoji-picker {
  color: rgb(160, 117, 10);
  background: #f1f1ff;
}
.chat-form .chat-controls #send-message {
  color: #fff;
  display: none;
  background: darkgoldenrod;
}
.chat-form .chat-controls #send-message:hover {
  background: darkgoldenrod;
}
.chat-form .message-input:valid~.chat-controls #send-message {
  display: block;
}
.chat-form .file-upload-wrapper {
  position: relative;
  height: 35px;
  width: 35px;
}
.chat-form .file-upload-wrapper :where(button, img) {
  position: absolute;
}
.chat-form .file-upload-wrapper img {
  height: 100%;
  width: 100%;
  object-fit: cover;
  border-radius: 50%;
}
.chat-form .file-upload-wrapper #file-cancel {
  color: #ff0000;
  background: #fff;
}
.chat-form .file-upload-wrapper :where(img, #file-cancel),
.chat-form .file-upload-wrapper.file-uploaded #file-upload {
  display: none;
}
.chat-form .file-upload-wrapper.file-uploaded img,
.chat-form .file-upload-wrapper.file-uploaded:hover #file-cancel {
  display: block;
}
em-emoji-picker {
  position: absolute;
  left: 50%;
  top: -337px;
  width: 100%;
  max-width: 350px;
  visibility: hidden;
  max-height: 330px;
  transform: translateX(-50%);
}
body.show-emoji-picker em-emoji-picker {
  visibility: visible;
}
/* Responsive media query for mobile screens */
@media (max-width: 520px) {
  #chatbot-toggler {
      right: 20px;
      bottom: 20px;
  }
  .chatbot-popup {
      right: 0;
      bottom: 0;
      height: 100%;
      border-radius: 0;
      width: 100%;
  }
  .chatbot-popup .chat-header {
      padding: 12px 15px;
  }
  .chat-body {
      height: calc(90% - 55px);
      padding: 25px 15px;
  }
  .chat-footer {
      padding: 10px 15px 15px;
  }
  .chat-form .file-upload-wrapper.file-uploaded #file-cancel {
      opacity: 0;
  }
}

.typing-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dots {
  display: flex;
  gap: 4px;
}

.dot {
  width: 6px;
  height: 6px;
  background-color: gray;
  border-radius: 50%;
  animation: typing 1.5s infinite;
}

.dot:nth-child(1) { animation-delay: 0s; }
.dot:nth-child(2) { animation-delay: 0.3s; }
.dot:nth-child(3) { animation-delay: 0.6s; }

@keyframes typing {
  0% { opacity: 0.3; }
  50% { opacity: 1; }
  100% { opacity: 0.3; }
}

#priceRange {
    width: 100%;
    max-width: 600px;
}

#applyFilter {
    background-color: darkgoldenrod;
    color: white;
    padding: 10px 20px; 
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease; 
}

#applyFilter:hover {
  background-color: black;
  color: darkgoldenrod;
}


</style>
<body>
  <header class="main-section">
    <nav class="navbar">
      <div class="logo">HomieMart</div>
      <ul class="nav-links">
        <li> <a href="index.php">Home</a></li>
        <li> <a href="collaborate.php">Collaborate</a></li>
        <li> <a href="marketplace.php" class="active">Marketplace</a></li>
        <li> <a href="aboutus.php">About us</a></li>
        <li> <a href="contact.php">Contact</a></li>
      </ul>

      <div class="nav-icon">
        <i class='bx bx-search' id="search-icon"></i> 
        <input type="text" class="search-input" id="search-input" placeholder="Search here...">
        <a href="cart.php">
            <i class='bx bx-cart'></i> 
        </a>
        <a href="notification.php">
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

    <div class="marketplace-head">
        <h1>Marketplace</h1>
    </div>
  </header>

  <div class="container-left">
    <!-- Sidebar -->
    <aside class="sidebar">
        <section class="categories">
            <h3>Category</h3>
            <ul>
                <li> <a href="#dye-crafts">Dye Crafts</a></li>
                <li> <a href="#gardening-products">Gardening Products</a></li>
                <li> <a href="#baking">Baking</a></li>
                <li> <a href="#handicrafts">Handicrafts</a></li>
                <li> <a href="#sustainable-products">Sustainable Products</a></li>
                <li> <a href="#cooking">Cooking</a></li>
                <li> <a href="#art-and-painting">Art and Paintings</a></li>
                <li> <a href="#knitting-and-crochet">Knitting and Crochet</a></li>
                <li> <a href="#wellness-products">Wellness Products</a></li>
                <li> <a href="#stationery-and-paper-crafts">Stationery and Paper Crafts</a></li>
            </ul>
        </section>
        <section class="filter">
            <h3>Filter by Price</h3>
            <input type="range" id="priceRange" min="0" max="10000" value="10000" step="1" />
            <p>Price: LKR <span id="priceValue">10000</span></p>
            <button id="applyFilter">Apply Filter</button>
        </section>

    </aside>

    <!-- Main Content -->
    <main class="content">
        <!-- Banner -->
        <div class="banner">
            <h1>Pure Big Discount</h1>
            <p>Save up to 50% off on your first order</p>
            <input type="email" placeholder="Your email address" />
            <button>Subscribe</button>
        </div>

        <section class="products">
              <?php
                // Get the price filter from the URL, if available
                $priceFilter = isset($_GET['price']) ? intval($_GET['price']) : 10000;  // Default to 10000 if not set

                $categories = [
                    'Dye Crafts' => 'Hand-dyed fabrics and artistic creations.',
                    'Gardening Products' => 'Indoor plants, gardening kits, and planters.',
                    'Baking' => 'Cakes, cookies, bread, and pastries.',
                    'Handicrafts' => 'Pottery, jewelry, decor, and more.',
                    'Sustainable Products' => 'Eco-friendly bags and reusable household items.',
                    'Cooking' => 'Home-cooked meals, snacks, and desserts.',
                    'Art and Painting' => 'Decorative wall art, paintings, and illustrations.',
                    'Knitting and Crochet' => 'Woolen clothing, blankets, and accessories.',
                    'Wellness Products' => 'Homemade soaps, skincare, and remedies.',
                    'Stationery and Paper Crafts' => 'Greeting cards, journals, and bookmarks.'
                ];

                foreach ($categories as $category => $description) {
                    // Modify SQL query to filter by price
                    $sql = "SELECT p.*, r.business_type 
                            FROM products p 
                            JOIN register r ON p.homemaker_email = r.email 
                            WHERE r.business_type = '" . mysqli_real_escape_string($conn, $category) . "' 
                            AND p.product_price <= $priceFilter";  // Filter products by price
                    $result = mysqli_query($conn, $sql);
                ?>

                <div class="category">
                    <h2 id="<?php echo strtolower(str_replace(' ', '-', $category)); ?>"><?php echo htmlspecialchars($category); ?></h2>
                    <p><?php echo htmlspecialchars($description); ?></p>
                    <div class="product-grid">
                        <?php
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <a href="product-details.php?id=<?php echo $row['id']; ?>" class="product-link">
                                <div class="product">
                                    <img src="<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" />
                                    <p><?php echo htmlspecialchars($row['homemaker_name']); ?></p>
                                    <h4><?php echo htmlspecialchars($row['product_name']); ?></h4>
                                    <div class="product-details">
                                        <p>LKR <?php echo number_format($row['product_price'], 2); ?></p>
                                        <button class="add-to-cart">
                                            <i class='bx bx-cart'></i>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        <?php
                                }
                            } else {
                                if ($priceFilter == 10000) {
                                    echo "<p>No $category stock available.</p>";
                                } else {
                                    echo "<p>No $category stock available within the selected price range.</p>";
                                }
                            }
                        ?> 
                    </div>
                    <hr class="black-line" style="border: 0; border-top: 2px solid black; width: 100%; margin: 20px auto;">
                </div>
            <?php } ?>

        </section>            
    </main> <!-- End of Main Content -->

    
</div>

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
            <li><a href="collaborate.php">Collaborate</a></li>
            <li><a href="marketplace.php">Marketplace</a></li>
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
<script>
  window.addEventListener('scroll', () => {
    const scrollPosition = window.scrollY;
    const maxScroll = document.body.scrollHeight - window.innerHeight;

    // Calculate a value between 0 and 1
    const scrollPercent = scrollPosition / maxScroll;

    // Create RGB color based on scroll position (cycling through light colors)
    const r = Math.floor(200 + 55 * Math.sin(scrollPercent * Math.PI * 2));
    const g = Math.floor(200 + 55 * Math.sin(scrollPercent * Math.PI * 2 + 2));
    const b = Math.floor(200 + 55 * Math.sin(scrollPercent * Math.PI * 2 + 4));

    const color = `rgb(${r}, ${g}, ${b})`;

    // Apply color to scrollbar
    document.documentElement.style.setProperty('--scroll-thumb-color', color);

    // Firefox fallback
    document.documentElement.style.scrollbarColor = `${color} #2e2e2e`;
  });


  const priceRange = document.getElementById('priceRange');
  const priceValue = document.getElementById('priceValue');
  const applyFilterButton = document.getElementById('applyFilter');

  // Get the price filter value from the URL (if exists)
  const urlParams = new URLSearchParams(window.location.search);
  const savedPrice = urlParams.get('price');

  // If a price filter exists, set the range input and price value accordingly
  if (savedPrice) {
      priceRange.value = savedPrice;
      priceValue.textContent = savedPrice;
  }

  // Update the displayed price value when the user changes the range
  priceRange.addEventListener('input', function() {
      priceValue.textContent = priceRange.value;
  });

  // Apply the filter when the button is clicked
  applyFilterButton.addEventListener('click', function() {
      const price = priceRange.value;
      const url = new URL(window.location.href);
      url.searchParams.set('price', price);
      window.location.href = url;  // Reload the page with the new query parameter
  });
</script>
<script src="marketplace.js"></script>
</html>
