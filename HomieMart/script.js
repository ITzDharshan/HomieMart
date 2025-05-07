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

const images = [
'images/image1.jpg',
'images/image2.jpg',
'images/image3.jpg',
'images/image4.jpg',
'images/image5.jpg',
'images/image6.jpg',
'images/image7.jpg',
'images/image8.jpg',
'images/image9.jpg',
'images/image10.jpg'
];

let currentIndex = 0;
const mainSection = document.querySelector('.main-section');
const prevBtn = document.getElementById('prev-btn');
const nextBtn = document.getElementById('next-btn');

// Function to update the background image
function updateBackground() {
mainSection.style.backgroundImage = `url(${images[currentIndex]})`;
}

// Show the next image
function showNextImage() {
currentIndex = (currentIndex + 1) % images.length;
updateBackground();
}

// Show the previous image
function showPrevImage() {
currentIndex = (currentIndex - 1 + images.length) % images.length;
updateBackground();
}

// Event listeners for buttons
prevBtn.addEventListener('click', showPrevImage);
nextBtn.addEventListener('click', showNextImage);

// Auto-change image every 5 seconds
setInterval(showNextImage, 5000);

document.addEventListener("DOMContentLoaded", () => {
const dots = document.querySelectorAll(".pagination .dot");
const testimonialSets = document.querySelectorAll(".testimonial-set");

dots.forEach((dot, index) => {
  dot.addEventListener("click", () => {
    // Remove active class from all dots and testimonial sets
    dots.forEach(d => d.classList.remove("active"));
    testimonialSets.forEach(set => set.classList.remove("active"));

    // Add active class to clicked dot and corresponding testimonial set
    dot.classList.add("active");
    testimonialSets[index].classList.add("active");
  });
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

  
function zoomIn(element) {
  // Remove active class from all images and add paused animation to all
  document.querySelectorAll(".slider span").forEach((el) => {
    el.classList.remove("active");
    el.classList.add("paused");
  });

  // Add active class to the clicked image to bring it forward
  element.parentElement.classList.add("active");
  
  // Pause animation for the clicked image (remove the paused class)
  element.parentElement.classList.remove("paused");
}