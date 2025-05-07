<?php
session_start();
include 'db_connection.php';

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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $category = $conn->real_escape_string($_POST['category']);
    $skills = $conn->real_escape_string($_POST['skills']);

    // Insert data into the database
    $sql = "INSERT INTO visiters (Username, Email, Mobile, Address, Business, Skills_Products)
            VALUES ('$name', '$email', '$phone', '$address', '$category', '$skills')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Form submitted successfully!'); window.location.href='collaborate.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="images/shopping.png">
  <link rel="stylesheet" href="collaborate.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
  <title>HomieMart</title>
</head>
<style>
  * {
    font-family: 'Poppins', sans-serif;
  }
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
</style>
<body>
  <header class="main-section">
    <nav class="navbar">
      <div class="logo">HomieMart</div>
      <ul class="nav-links">
        <li> <a href="index.php">Home</a></li>
        <li> <a href="collaborate.php" class="active">Collaborate</a></li>
        <li> <a href="marketplace.php">Marketplace</a></li>
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

    <div class="collaborate-head">
        <h1>Collaborate</h1>
    </div>
  </header>

    <!-- Collaboration Info Section -->
    <section class="collaboration-info">
        <div class="container">
            <h2>Why Collaborate with Us?</h2>
            <div class="info-grid">
                <div class="info-item">
                    <img src="images/international.png" alt="Global Reach">
                    <h3>Global Reach</h3>
                    <p>Showcase your products to a worldwide audience and grow your customer base.</p>
                </div>
                <div class="info-item">
                    <img src="images/customer-service.png" alt="Support & Resources">
                    <h3>Support & Resources</h3>
                    <p>Access tools, tutorials, and resources to enhance your skills and business.</p>
                </div>
                <div class="info-item">
                    <img src="images/grassroots.png" alt="Community-Driven">
                    <h3>Community-Driven</h3>
                    <p>Be part of a supportive community that values creativity and sustainability.</p>
                </div>
            </div>
        </div>
    </section>


<!-- Collaboration Form Section -->
<section class="collaboration-form">
    <div class="container">
        <h2>Join Us Today!</h2>
        <p>Fill out the form below, and our team will get back to you with your login credentials.</p>
        <form id="collaborateForm" action="collaborate.php" method="POST">
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="category">Select Homemaker's Business Type:</label>
                <select id="category" name="category" required>
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
            <div class="form-group">
                <label for="skills">Your Skills/Products</label>
                <textarea id="skills" name="skills" rows="4" required></textarea>
            </div>
            <button type="submit">Submit</button>
        </form>
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

    const searchIcon = document.getElementById('search-icon');
    const searchInput = document.getElementById('search-input');
    const navIcon = document.querySelector('.nav-icon');

    searchIcon.addEventListener('click', () => {
    if (searchInput.style.display === 'none' || searchInput.style.display === '') {
        searchInput.style.display = 'block';
        searchInput.focus();
    } else {
        searchInput.style.display = 'none';
    }
    });

    // Close search input when clicking outside
    document.addEventListener('click', (e) => {
    if (!navIcon.contains(e.target)) {
        searchInput.style.display = 'none';
    }
    });



    document.addEventListener('DOMContentLoaded', () => {
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.querySelector('.nav-links');
    const icon = menuIcon.querySelector('i'); // Get the icon inside the menu icon container

    // Toggle navigation menu visibility and icon
    menuIcon.addEventListener('click', () => {
    navLinks.classList.toggle('show');

    // Toggle the icon class
    if (navLinks.classList.contains('show')) {
        icon.classList.remove('bx-menu');
        icon.classList.add('bx-x');
    } else {
        icon.classList.remove('bx-x');
        icon.classList.add('bx-menu');
    }
    });

    // Close the menu if clicked outside
    document.addEventListener('click', (e) => {
    if (!menuIcon.contains(e.target) && !navLinks.contains(e.target)) {
        navLinks.classList.remove('show');

        // Reset icon to bx-menu when menu is closed
        if (icon.classList.contains('bx-x')) {
        icon.classList.remove('bx-x');
        icon.classList.add('bx-menu');
        }
    }
    });
    });

    // Get the button
    const backToTopButton = document.getElementById('backToTop');

    // When the user scrolls down 200px from the top of the document, show the button
    window.onscroll = function() {
        if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        backToTopButton.style.display = 'block';
        } else {
        backToTopButton.style.display = 'none';
        }
    };

    // When the user clicks the button, scroll back to the top of the document
    backToTopButton.onclick = function(e) {
        e.preventDefault(); // Prevent the default anchor behavior
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll to the top with smooth animation
    };

    document.getElementById('profile-icon').addEventListener('click', function(event) {
      event.stopPropagation(); // Prevent closing immediately
      document.querySelector('.profile-container').classList.toggle('active');
  });
  
  // Close the popup when clicking outside
  document.addEventListener('click', function(event) {
      const profileContainer = document.querySelector('.profile-container');
      if (!profileContainer.contains(event.target)) {
          profileContainer.classList.remove('active');
      }
  });

  document.addEventListener("DOMContentLoaded", function () {
    const chatbotToggler = document.getElementById("chatbot-toggler");
    const chatbotPopup = document.querySelector(".chatbot-popup");
    const closeChatbot = document.getElementById("close-chatbot");
    const chatBody = document.querySelector(".chat-body");
    const chatForm = document.querySelector(".chat-form");
    const messageInput = document.querySelector(".message-input");
    const sendMessageBtn = document.getElementById("send-message");

    // Toggle Chatbot Popup
    chatbotToggler.addEventListener("click", () => {
        document.body.classList.toggle("show-chatbot");
    });

    closeChatbot.addEventListener("click", () => {
        document.body.classList.remove("show-chatbot");
    });

    // Handle Sending Messages
    chatForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const userMessage = messageInput.value.trim();
        if (userMessage === "") return;

        appendMessage("user", userMessage);
        messageInput.value = "";

        // Show typing indicator
        showTypingIndicator();

        // Simulate bot response with a delay
        setTimeout(() => {
            removeTypingIndicator();
            const botResponse = generateBotResponse(userMessage);
            appendMessage("bot", botResponse);
        }, 2000);
    });

    // Function to Append Message to Chat
    function appendMessage(sender, message) {
        const messageElement = document.createElement("div");
        messageElement.classList.add("message", sender === "user" ? "user-message" : "bot-message");

        if (sender === "bot") {
            messageElement.innerHTML = `
                <svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
                  <path
                    d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"
                  />
                </svg>
                <p class="message-text">${message}</p>
            `;
        } else {
            messageElement.innerHTML = `<p class="message-text">${message}</p>`;
        }

        chatBody.appendChild(messageElement);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Show Typing Indicator
    function showTypingIndicator() {
        const typingIndicator = document.createElement("div");
        typingIndicator.classList.add("message", "bot-message", "typing-indicator");
        typingIndicator.innerHTML = `
            <svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
              <path
                d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"
              />
            </svg>
            <span class="dots">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </span>
        `;
        chatBody.appendChild(typingIndicator);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Remove Typing Indicator
    function removeTypingIndicator() {
        const typingIndicator = document.querySelector(".typing-indicator");
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Function to Generate Bot Responses
  function generateBotResponse(userMessage) {
    const lowerCaseMessage = userMessage.toLowerCase();

    if (lowerCaseMessage.includes("hello")) {
        return "Hello! How can I assist today?";
    } else if (lowerCaseMessage.includes("do you offer discounts or promotions?")) {
        return "Yes, we often have special offers and discounts! Make sure to sign up for our newsletter or follow us on social media to stay updated on the latest promotions.";
    } else if (lowerCaseMessage.includes("how do i create an account")) {
        return "To create an account, click on the 'Sign Up' button at the top of the page, enter your details, and follow the prompts. Once your account is created, you'll be able to place orders, track shipments, and manage your account details.";
    } else if (lowerCaseMessage.includes("what shipping options do you offer?")) {
        return "We offer standard and expedited shipping. The available options will be displayed during checkout, along with their respective delivery times and costs.";
    } else if (lowerCaseMessage.includes("how can i track my order?")) {
        return "To track your order, go to 'My Orders' in your account and click on the tracking link provided for your shipment.";
    } else if (lowerCaseMessage.includes("what is your return policy?")) {
        return "We offer a 30-day return policy. Items must be unused and in their original packaging. Please visit our 'Returns' page for more details.";
    } else if (lowerCaseMessage.includes("what payment methods do you accept?")) {
        return "We accept major credit cards, PayPal, and other secure payment options displayed at checkout.";
    } else if (lowerCaseMessage.includes("do you have a physical store?")) {
        return "No, we operate exclusively online to bring you the best prices and convenience!";
    } else if (lowerCaseMessage.includes("how long does delivery take?")) {
        return "Delivery times vary based on your location and chosen shipping method. Estimated times will be displayed during checkout.";
    } else if (lowerCaseMessage.includes("can i cancel or change my order?")) {
        return "You can cancel or modify your order within a limited time after placing it. Please visit 'My Orders' and check if changes are available.";
    } else if (lowerCaseMessage.includes("how do i contact customer support?")) {
        return "You can contact our customer support via live chat, email, or the contact form on our website.";
    } else if (lowerCaseMessage.includes("do you sell gift cards?")) {
        return "Yes! We offer digital gift cards in various amounts. You can find them in our 'Gift Cards' section.";
    } else {
        return "Please ask only HomieMart-related questions. Make sure to type without spelling mistakes.";
    }
  }
});
  </script>

</html> 
