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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="images/shopping.png">
  <link rel="stylesheet" href="categories.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
  <title>HomieMart</title>
</head>
<style>
  .category h4 {
    font-size: 18px;
    font-weight: 600;
    margin: 20px;
    text-align: left;
}
</style>
<body>
  <header class="main-section">
    <video autoplay muted loop class="background-video">
        <source src="videos/gardening.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <nav class="navbar">
      <div class="logo">HomieMart</div>
      <ul class="nav-links">
        <li> <a href="index.php">Home</a></li>
        <li> <a href="collaborate.php">Collaborate</a></li>
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

    <div class="dyecraft-head">
        <h1>Gardening Products</h1>
    </div>
  </header>

  <section class="community-section">
    <div class="content">
      <h2>Join With Our Community</h2>
      <p>
        Discover the joy of gardening and transform your outdoor space with our exclusive range of gardening products. Our collection is designed to cater to gardeners of all skill levels, from beginners nurturing their first plants to seasoned green thumbs looking for the best tools and supplies. Explore high-quality seeds, eco-friendly fertilizers, durable tools, and decorative planters that make gardening a delightful experience. By joining our community, you’ll gain access to expert tips, seasonal planting guides, and inspiration to create your dream garden. Whether you’re growing vegetables, cultivating flowers, or designing a lush landscape, our products and resources are here to help you every step of the way.   
      </p>
      <button class="join-button">Join Our Community</button>
    </div>
  </section>

  <?php
    $category = 'Gardening Products';
    $description = 'Indoor plants, gardening kits, and planters.';

    $sql = "SELECT p.*, r.business_type 
            FROM products p 
            JOIN register r ON p.homemaker_email = r.email 
            WHERE r.business_type = '" . mysqli_real_escape_string($conn, $category) . "'";

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
          <div class="product">
              <img src="<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" />
              <p>By : <?php echo htmlspecialchars($row['homemaker_name']); ?></p>
              <h4><?php echo htmlspecialchars($row['product_name']); ?></h4>
              <div class="product-details">
                  <p>LKR <?php echo number_format($row['product_price'], 2); ?></p>
              </div>
          </div>
        <?php
              }
          } else {
              echo "<p>No $category stock available.</p>";
          }
        ?> 
    </div>
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
            return "To create an account, click on the 'Sign Up' button at the top of the page, enter your details, and follow the prompts. Once your account is created, you'll be able to place orders, track shipments, and manage your account details";
        
        }else if (lowerCaseMessage.includes("what shipping options do you offer?")) {
            return "We offer standard and expedited shipping. The available options will be displayed during checkout, along with their respective delivery times and costs.";
        } else {
            return "Please Ask only HomieMart Related Question, Type Without spelling mistake";
        }
    }
});
  </script>
</html>