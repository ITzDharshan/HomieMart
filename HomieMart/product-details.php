<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_email'])) {
  // Redirect to the login page if not logged in
  header("Location: index.php");
  exit();
}

if (isset($_GET['id'])) {
  $productId = intval($_GET['id']); // sanitize input
  $sql = "SELECT p.*, r.business_type 
          FROM products p
          JOIN register r ON p.homemaker_email = r.email
          WHERE p.id = $productId";
  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
      $product = mysqli_fetch_assoc($result);
      $homemaker_email = $product['homemaker_email'];
      $homemaker_name = $product['homemaker_name']; 
  } else {
      echo "<p>Product not found.</p>";
      exit;
  }
} else {
  echo "<p>No product selected.</p>";
  exit;
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

// Retrieve customer's name
$customer_email = $_SESSION['customer_email'];
$customer_query = $conn->prepare("SELECT fname FROM register WHERE email = ?");
$customer_query->bind_param("s", $customer_email);
$customer_query->execute();
$customer_result = $customer_query->get_result();

if ($customer_result->num_rows > 0) {
  $customer = $customer_result->fetch_assoc();
  $customer_name = $customer['fname'];
} else {
  die("Customer details not found.");
}

// Check if a message was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . uniqid() . "_" . $file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
          $image_path = $target_file;
        } else {
            die("Image upload failed.");
        }
    }

    // Insert message into chat_messages table
    $insert = $conn->prepare("INSERT INTO chat_messages (customer_email, customer_name, homemaker_email, homemaker_name, message, image, product_id, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $insert->bind_param("ssssssi", $customer_email, $customer_name, $homemaker_email, $homemaker_name, $message, $image_path, $productId);

      if ($insert->execute()) {
          // Success: Display alert and redirect to the product details page
          echo "<script>alert('Message sent successfully!'); window.location.href='product-details.php?id=$productId';</script>";
      } else {
          // Error: Display alert with error message
          echo "<script>alert('Error: " . $insert->error . "'); window.location.href='product-details.php?id=$productId';</script>";
      }

}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/shopping.png">
    <link rel="stylesheet" href="product-details.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
    <title>HomieMart</title>
</head>
<style>
  * {
    font-family: 'Poppins', sans-serif;
  }

  .product-right a{
    text-decoration: none;
  }
  .chat-popup {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 400px;
        background-color: white;
        border: 1px solid #ccc;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        z-index: 1000;
        border-radius: 8px;
    }

    .chat-popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close-btn  {
        cursor: pointer;
        border: none;
        background-color: transparent;
        font-size: 18px;
    }

    textarea {
        width: 95%;
        height: 100px;
        margin-top: 10px;
        padding: 5px;
        border: 2px solid darkgoldenrod;
        border-radius: 8px;
    }

    input[type="file"] {
        margin-top: 10px;
    }

    #imagePreview {
        margin-top: 10px;
        max-width: 100%;
        max-height: 150px;
        overflow: hidden;
    }

    .send {
        width: 100%;
        padding: 10px;
        background-color: darkgoldenrod;
        color: black;
        border: none;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s, color 0.3s;
        border-radius: 8px;
    }

    .send:hover {
        background-color: black;
        color: white;
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
    </header>

    <div class="product-container">
      <div class="product-left">
          <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="main-image" id="mainImage">
      </div>
      <div class="product-right">
          <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
          <p>
              <a href="javascript:void(0);" onclick="openChatBox('<?php echo htmlspecialchars($product['homemaker_email']); ?>')">
                  <?php echo htmlspecialchars($product['homemaker_email']); ?>
              </a>
          </p>
          <p class="description"><?php echo htmlspecialchars($product['product_description']); ?></p>
          <p class="price">LKR <?php echo number_format($product['product_price'], 2); ?></p>
          <br>
          <div class="buttons">
              <a href="checkout.php?id=<?php echo $product['id']; ?>" class="buy-now-link">
                  <button class="buy-now">Buy Now</button>
              </a>
              <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['product_price']; ?>">
                <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['product_image']); ?>">
                <input type="hidden" name="quantity" id="cartQuantity" value="1"> <!-- Default Quantity -->

                <button type="submit" class="add-to-cart">
                    <i class='bx bx-cart'></i> Add to Cart
                </button>
            </form>
          </div>

          <p class="delivery-info">
              Free Delivery On Order Over $20
              <img src="images/delivery-truck.png" alt="Delivery Truck" style="width: 25px; height: 25px; margin-left: 5px; vertical-align: middle;"> 
          </p>
      </div>
  </div>

  <div id="chatBox" class="chat-popup">
    <div class="chat-popup-header">
        <span id="chatTitle"></span>
        <button onclick="closeChatBox()" class="close-btn"><i class='bx bx-x'></i></button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <textarea name="message" required placeholder="Enter your message..."></textarea><br>
      <input type="file" name="image" id="imageAttachment" accept="image/*" onchange="previewImage()" />
      <div id="imagePreview" style="margin-top: 10px; max-width: 100px;"></div>
      <button type="submit" class="send">Send Message</button>
    </form>
  </div>

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

    <script>   
        function updateQuantity(change) {
            let quantityElement = document.getElementById('quantity');
            let currentQuantity = parseInt(quantityElement.textContent);
            let newQuantity = currentQuantity + change;
            if (newQuantity < 1) newQuantity = 1;
            quantityElement.textContent = newQuantity;
        }

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

    function openChatBox(homemakerName, homemakerEmail) {
        document.getElementById('chatBox').style.display = 'block';
        document.getElementById('chatTitle').innerText = 'Chat with ' + homemakerName;
        document.getElementById('messageBox').dataset.homemakerEmail = homemakerEmail;
    }

    function closeChatBox() {
        document.getElementById('chatBox').style.display = 'none';
    }

    function previewImage() {
        var file = document.getElementById('imageAttachment').files[0];
        var reader = new FileReader();

        reader.onloadend = function () {
            var imgPreview = document.getElementById('imagePreview');
            imgPreview.innerHTML = ''; 

            // Create image element
            var img = document.createElement('img');
            img.src = reader.result;
            img.style.width = '100%';
            img.style.height = 'auto';

            // Create the close button
            var closeBtn = document.createElement('button');
            closeBtn.innerHTML = 'X';
            closeBtn.style.position = 'absolute';
            closeBtn.style.top = '0';
            closeBtn.style.right = '0';
            closeBtn.style.backgroundColor = 'red';
            closeBtn.style.color = 'white';
            closeBtn.style.border = 'none';
            closeBtn.style.padding = '5px';
            closeBtn.style.cursor = 'pointer';
            closeBtn.onclick = function () {
                imgPreview.innerHTML = '';
            };

            var previewContainer = document.createElement('div');
            previewContainer.style.position = 'relative';
            previewContainer.style.display = 'inline-block';
            previewContainer.appendChild(img);
            previewContainer.appendChild(closeBtn);

            imgPreview.appendChild(previewContainer);
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    
    </script>
</body>
</html>