<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_email'])) {
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['customer_email'];

// Fetch cart items
$cart_query = "SELECT * FROM cart WHERE user_email = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$subtotal = 0;

$showPopup = false;
$orderSuccess = false;
$order_id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $phone = $_POST['phone'];
    $total_price = $_POST['total_price'];
    $card_name = $_POST['card_name'];
    $card_number = $_POST['card_number'];
    $cvv = $_POST['cvv'];
    $exp_date = $_POST['exp_date'];
    $user_email = $_SESSION['customer_email'];

    // Try-Catch logic
    try {
        // Insert order details
        $order_query = "INSERT INTO orders (user_email, first_name, last_name, address, city, state, postal_code, phone, total_price, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("ssssssssd", $user_email, $first_name, $last_name, $address, $city, $state, $postal_code, $phone, $total_price);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Insert cart items
        $cart_query = "SELECT * FROM cart WHERE user_email = ?";
        $stmt_cart = $conn->prepare($cart_query);
        $stmt_cart->bind_param("s", $user_email);
        $stmt_cart->execute();
        $cart_result = $stmt_cart->get_result();
        
        while ($item = $cart_result->fetch_assoc()) {
            $product_name = $item['product_name'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $adminRevenue = $quantity * $price * 0.3;
            $homemakerRevenue = $quantity * $price * 0.7;

            // Calculate item total price
            $item_total = $quantity * $price;
            
            // Add item total to the overall order total
            $total_price += $item_total;
            
            $order_item_query = "INSERT INTO order_items (order_id, product_name, quantity, price, total) VALUES (?, ?, ?, ?, ?)";
            $stmt_item = $conn->prepare($order_item_query);
            $stmt_item->bind_param("isidd", $order_id, $product_name, $quantity, $price, $item_total);
            $stmt_item->execute();

            $homemaker_email = get_homemaker_email($product_name, $conn); // You'll need to define this function

            // Insert into revenue table
            $revenue_query = "INSERT INTO revenue (order_id, product_name, admin_revenue, homemaker_revenue, homemaker_email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($revenue_query);
            $stmt->bind_param("issds", $order_id, $product_name, $adminRevenue, $homemakerRevenue, $homemaker_email);
            $stmt->execute();
        }

        // Clear cart
        $delete_cart_query = "DELETE FROM cart WHERE user_email = ?";
        $stmt_delete = $conn->prepare($delete_cart_query);
        $stmt_delete->bind_param("s", $user_email);
        $stmt_delete->execute();

        $orderSuccess = true;
        $showPopup = true;
    } catch (Exception $e) {
        $orderSuccess = false;
        $showPopup = true;
    }
}
function get_homemaker_email($product_name, $conn) {
    $query = "SELECT homemaker_email FROM products WHERE product_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        return $row['homemaker_email'];
    }
    return null;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/shopping.png">
    <title>Shipping Form</title>
</head>
<style>
     body {
    font-family: 'Poppins', sans-serif;
    background-color: #f7f7f7;
    padding: 20px;
    background: url('images/profile.jpg') no-repeat center center/cover;
}

.back-btn a{
    display: inline-flex;
    align-items: center;
    font-size: 15px;
    color: #fff;
    background-color: rgba(0, 0, 0, 0.5);
    border: 2px solid white;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
}
  
.back-btn a:hover {
    background: darkgoldenrod;
    color: #fff;
}
  
.container {
    display: flex;
    width: 95%;
    margin: 20px auto;
    gap: 20px;
}

.cart-section {
    width: 100%;
    background: rgba(0, 0, 0, 0.8);
    padding: 20px;
    border-radius: 8px;
    overflow-y: auto;
    overflow-x: none;
    height: 70vh;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    color: white;
}

.cart-section::-webkit-scrollbar {
    width: 8px; /* Width of the scrollbar */
}

.cart-section::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 5px;
}

.cart-section::-webkit-scrollbar-thumb {
    background: white;
    border-radius: 5px;
}

.cart-section::-webkit-scrollbar-thumb:hover {
    background: wheat; 
}


.cart-header {
    padding-bottom: 10px;
    border-bottom: 2px solid #ddd;
}

.cart-header h2 {
    font-size: 30px;
    margin-left: 10px;
}

form {
    margin-top: 20px;
    padding-left: 10px;
}

.input-group {
    font-family: 'Poppins', sans-serif;
    display: flex;
    gap: 5%;
    margin-bottom: 15px;
}

.input-group div {
    width: 45%;
}

.input-group.full-width {
    display: flex;
    flex-direction: column;
    width: 95%;
    margin-bottom: 15px;
}

label {
    display: block;
    font-size: 14px;
    margin-bottom: 5px;
    color: #ddd;
}

input[type="text"], [type="password"]{
    font-family: 'Poppins', sans-serif;
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.9);
    color: #333;
}

input[type="text"]:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
}

.credit-card-info {
    margin-top: 15px;
}

.summary-items p {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.total {
    font-weight: bold;
    font-size: 16px;
    margin-top: 15px;
}

.total p {
    display: flex;
    justify-content: space-between;
    color: #a8a8a8;
}

.total span {
    color: #007bff;
}

.checkout-btn {
    font-family: 'Poppins', sans-serif;
    width: 100%;
    display: block;
    background: royalblue;
    color: white;
    text-align: center;
    padding: 15px;
    text-decoration: none;
    margin-top: 10%;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

.checkout-btn:hover {
    background: #0056b3;
}


/* Basic popup container */
.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

/* Popup content box */
.popup-content { 
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    width: 350px;
    height: 450px;
    max-width: 90%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    z-index: 1000;
    border: 5px solid darkgoldenrod;
}

.popup.show .popup-content {
    display: block;
}

.popup-content h2 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
}

.popup-content p {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
}

.icon {
    width: 150px;
    height: 150px;
    margin: 20px;
}

/* Button styling */
#popup-button {
    padding: 10px 20px;
    color: black;
    background-color: white;
    border: 2px solid black;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    margin-top: 40px;
}

#popup-button:hover {
    background: darkgoldenrod;
    color: #fff;
}

.popup {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

</style>
<body>
    <div class="back-btn"><a href="cart.php">Back to Cart</a></div>
        <div class="container">
            <div class="cart-section">
                <div class="cart-header">
                    <h2>Billing Details</h2>
                </div>
                <form action="checkout.php" method="POST">
                    <div class="input-group">
                        <div>
                            <label>First Name</label>
                            <input type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div>
                            <label>Last Name</label>
                            <input type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="input-group full-width">
                        <label>Address</label>
                        <input type="text" name="address" placeholder="Address" required>
                    </div>
                    <div class="input-group">
                        <div>
                            <label>City</label>
                            <input type="text" name="city" required>
                        </div>
                        <div>
                            <label>State</label>
                            <input type="text" name="state" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div>
                            <label>Postal or zip code</label>
                            <input type="text" name="postal_code" required>
                        </div>
                        <div>
                            <label>Phone</label>
                            <input type="text" name="phone" required>
                        </div>
                    </div>
                    <input type="hidden" name="total_price" value="<?php echo number_format($subtotal, 2); ?>">

                    <div class="cart-header">
                        <h2>Payment Info</h2>
                    </div>                   
                                       
                    <div class="credit-card-info">
                        <div class="input-group full-width">
                            <label>Name on Card</label>
                            <input type="text" name="card_name" placeholder="Cardholder Name" required>
                        </div>
                        <div class="input-group full-width">
                            <label>Card Number</label>
                            <input type="text" id="card-number" name="card_number" placeholder="0000-0000-0000-0000" required>
                        </div>
                        <div class="input-group">
                            <div>
                                <label>CVV Number</label>
                                <input type="password" id="cvv" name="cvv" placeholder="CVV" required>
                            </div>
                            <div>
                                <label>Exp. Date (MM/YY)</label>
                                <input type="text" id="exp-date" name="exp_date" placeholder="MM/YY" required>
                            </div>
                        </div>
                    </div>

                    <div class="cart-header">
                        <h2>Summary</h2>
                    </div>
                        <div class="summary-items">
                            <?php
                                if ($result->num_rows > 0) {
                                    while ($item = $result->fetch_assoc()) {
                                        $total_price = $item['price'] * $item['quantity'];
                                        $subtotal += $total_price;
                                        ?>
                                        <p><?php echo htmlspecialchars($item['product_name']); ?> <span>LKR <?php echo number_format($total_price, 2); ?></span></p>
                                        <?php
                                    }
                                } else {
                                    echo "<p>Your cart is empty.</p>";
                                }
                            ?>
                        </div>
                        <div class="total">
                            <p>Shipping Fee <span>LKR 0.00</span></p>
                            <p>Total <span><strong>LKR <?php echo number_format($subtotal, 2); ?></strong></span></p>
                        </div>
                        <button type="submit" class="btn checkout-btn">Proceed to Checkout</button>
                </form>
            </div>
            
            <div id="popup" class="popup" style="display:none;">
                <div class="popup-content">
                    <img id="popup-icon" class="icon" src="images/load-time.gif" alt="Popup Icon" />
                    <h2 id="popup-title">Processing...</h2>
                    <p id="popup-message">Please wait while we process your order.</p>
                    <button id="popup-button" onclick="handleButton()" style="display: none;"></button>
                </div>
            </div>             
        </div>     
</body>
<script>
    document.getElementById('card-number').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-numeric characters
        if (value.length > 16) value = value.slice(0, 16); // Limit to 16 digits

        // Format with hyphens (but allow deletion)
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += '-';
            }
            formattedValue += value[i];
        }

        e.target.value = formattedValue; // Set formatted value
    });

    document.getElementById('cvv').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 3); // Allow only 3 digits
    });

    document.getElementById('exp-date').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-numeric characters
        if (value.length > 4) value = value.slice(0, 4); // Limit to 4 digits
        if (value.length > 2) value = value.slice(0, 2) + '/' + value.slice(2); // Insert "/"
        e.target.value = value;
    });


    function showPopupWithDelay(title, message, type) {
        const popup = document.getElementById("popup");
        const titleElement = document.getElementById("popup-title");
        const messageElement = document.getElementById("popup-message");
        const icon = document.getElementById("popup-icon");
        const button = document.getElementById("popup-button");

        // Initial loading state
        popup.style.display = "block";
        titleElement.style.color = "#000";
        icon.src = "images/load-time.gif";
        titleElement.innerText = "Processing...";
        messageElement.innerText = "Please wait while we process your order.";
        button.style.display = "none";

        // Simulate 3-second delay
        setTimeout(() => {
            if (type === "success") {
                titleElement.style.color = "green";
                icon.src = "images/credit-card.gif";
                titleElement.innerText = title;
                messageElement.innerHTML = message;
                button.innerText = "Continue Shopping";
                button.dataset.action = "success";
            } else if (type === "error") {
                titleElement.style.color = "red";
                icon.src = "images/error.gif";
                titleElement.innerText = title;
                messageElement.innerText = message;
                button.innerText = "Retry";
                button.dataset.action = "error";
            }
            button.style.display = "inline-block";
        }, 3000);
    }

    function handleButton() {
        const button = document.getElementById("popup-button");
        if (button.dataset.action === "success") {
            window.location.href = "index.php";
        } else if (button.dataset.action === "error") {
            window.location.reload();
        }
    }

    <?php if ($showPopup): ?>
        document.addEventListener("DOMContentLoaded", function() {
        <?php if ($orderSuccess): ?>
            showPopupWithDelay("Order Placed..!", "Your order ID is <strong>#<?php echo htmlspecialchars($order_id); ?></strong><br> Order has been placed successfully!", "success");
        <?php else: ?>
            showPopupWithDelay("Order Failed..!", "Something went wrong! Please try again.", "error");
        <?php endif; ?>
    });
    <?php endif; ?>

</script>
</html>
