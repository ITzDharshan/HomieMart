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

// Handle AJAX cancel request before outputting any HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_SESSION['customer_email'])) {
    $order_id = intval($_POST['order_id']);
    $user_email = $_SESSION['customer_email'];

    $query = "UPDATE orders SET payment_status = 'Cancelled' WHERE order_id = ? AND user_email = ? AND payment_status IN ('Pending', 'Processing')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $order_id, $user_email);

    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        echo 'error';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/shopping.png">
    <title>Notification Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f8f9fa;
}

.head {
    padding: 20px;
    width: 100%;
    background: #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.head a {
    text-decoration: none;
    color: black;
}

.head a:hover {
    color: darkgoldenrod;
}

.head p {
    margin: 0;
    font-size: 16px;
    color: #333;
    font-weight: 500;
}

.container {
    display: flex;
    height: 89vh;
}

.container h2{
    font-weight: lighter;
    font-size: 35px;
}

/* Left Panel */
.left-panel {
    width: 30%;
    padding: 20px;
    background: #fff;
    border-right: 1px solid #ddd;
    overflow-y: auto;
}

.message-history {
    margin-top: 20px;
}

.message {
    padding: 15px;
    background: #f0f0f0;
    margin-bottom: 10px;
    border-radius: 5px;
}

.message span {
    display: block;
    font-size: 12px;
    color: #555;
    margin-top: 5px;
}

/* Right Panel */
.right-panel {
    width: 70%;
    padding: 20px;
    background: #fff;
    overflow-y: auto;
}

.right-panel h2 {
    margin-bottom: 20px;
    font-size: 35px;
    color: #333;
}

/* Order Card Layout */
.order-card {
    display: flex;
    justify-content: space-between;
    gap: 25px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    margin-bottom: 25px;
    background: #fafafa;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

/* Left Side - Order Header */
.order-header {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.order-status span {
    padding: 6px 12px;
    border-radius: 5px;
    font-weight: 600;
    font-size: 13px;
    display: inline-block;
    width: fit-content;
}

/* Order Details */
.order-details p {
    margin: 0 0 6px 0;
    font-size: 14px;
}

.order-details p strong {
    color: #555;
    font-weight: 600;
}

.order-details p span {
    color: #222;
}

/* Right Side - Order Items */
.order-items {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border: 1px solid #e6e6e6;
    background: #fff;
    border-radius: 6px;
    transition: box-shadow 0.3s ease;
}

.item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Item Image */
.item img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 5px;
}

/* Item Info */
.item h4 {
    margin: 0 0 5px 0;
    font-size: 15px;
    color: #333;
}

.item p {
    margin: 0;
    font-size: 13px;
    color: #555;
}

.price {
    color: #111;
    font-weight: bold;
    font-size: 14px;
}

</style>
<body>
    <div class="head">
        <a href="index.php">< Back to Home</a>
        <?php
        if (isset($_SESSION['customer_email']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Customer') {
                echo "<p>Welcome, " . $_SESSION['customer_email'] . "</p>";}
        ?>
    </div>
    <div class="container">
        <div class="left-panel">
            <h2>Message History</h2>
                <?php
                    if (isset($_SESSION['customer_email']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Customer') {
                        $customer_email = $_SESSION['customer_email'];

                        // Query to get messages only for this customer
                        $sql = "SELECT * FROM chat_messages 
                                WHERE homemaker_email IS NOT NULL 
                                AND reply_message IS NOT NULL 
                                AND customer_email = ? 
                                ORDER BY created_at DESC";

                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "s", $customer_email);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        date_default_timezone_set('Asia/Colombo'); // Set timezone to Sri Lanka

                        echo '<div class="message-history">';
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<div class="message">';
                                echo '<p><strong>@' . htmlspecialchars($row['homemaker_email']) . ':</strong> ' . htmlspecialchars($row['reply_message']) . '</p>';
                                echo '<span>' . date('M d, Y', strtotime($row['created_at'])) . '</span>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No messages found for your account.</p>';
                        }
                        echo '</div>';
                    }
                ?>
        </div>

        <div class="right-panel">
            <h2>Your Orders</h2>
            <div class="order-history">
                <?php
                    // Assuming the session is already started
                    if (isset($_SESSION['customer_email']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Customer') {
                        $customer_email = $_SESSION['customer_email']; // Get logged-in user's email

                        // Query to get orders based on logged-in customer's email
                        $query = "
                            SELECT 
                                oi.order_id,
                                oi.product_name,
                                oi.created_at,
                                oi.quantity,
                                oi.price,
                                o.address,
                                o.city,
                                o.state,
                                o.user_email,
                                o.payment_status,
                                p.product_image
                            FROM order_items oi
                            INNER JOIN orders o ON oi.order_id = o.order_id
                            INNER JOIN products p ON oi.product_name = p.product_name
                            WHERE o.user_email = ?  -- Filter orders by customer email
                            ORDER BY oi.order_id DESC;
                        ";

                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "s", $customer_email);  // Bind email to the query
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if ($result && mysqli_num_rows($result) > 0) {
                            // Grouping items by order_id
                            $orders = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                $orders[$row['order_id']]['order_details'] = [
                                    'order_id' => $row['order_id'],
                                    'created_at' => $row['created_at'],
                                    'address' => $row['address'],
                                    'city' => $row['city'],
                                    'state' => $row['state'],
                                    'user_email' => $row['user_email'],
                                    'payment_status' => $row['payment_status'],
                                ];
                                $orders[$row['order_id']]['items'][] = [
                                    'product_name' => $row['product_name'],
                                    'quantity' => $row['quantity'],
                                    'price' => $row['price'],
                                    'product_image' => $row['product_image']
                                ];
                            }

                            // Display orders
                            foreach ($orders as $order) {
                                $orderDetails = $order['order_details'];
                                $fullAddress = $orderDetails['address'] . ', ' . $orderDetails['city'] . ', ' . $orderDetails['state'];
                                $total_amount = 0;
                                foreach ($order['items'] as $itm) {
                                    $total_amount += $itm['price'] * $itm['quantity'];
                                }
                                ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div class="order-status">
                                            <?php
                                            $status = strtolower($orderDetails['payment_status']);
                                            $statusClass = '';
                                            $backgroundColor = '';
                                            $color = '';

                                            if ($status === 'completed') {
                                                $backgroundColor = '#d4edda';
                                                $color = '#155724';
                                            } elseif ($status === 'pending') {
                                                $backgroundColor = '#ffec99';
                                                $color = '#856404';
                                            } elseif ($status === 'processing') {
                                                $backgroundColor = '#add8e6';
                                                $color = '#003366';
                                            } else {
                                                $backgroundColor = '#f8d7da';
                                                $color = '#721c24';
                                            }
                                            ?>

                                            <!-- Dynamically set background color and text color -->
                                            <span style="background: <?= $backgroundColor ?>; color: <?= $color ?>;">
                                                <?= ucfirst($orderDetails['payment_status']) ?>
                                            </span>

                                            <?php if ($status === 'pending' || $status === 'processing') { ?>
                                                <button 
                                                    class="cancel-order-btn" 
                                                    data-order-id="<?= $orderDetails['order_id']; ?>"
                                                    style="margin-left: 10px; padding: 6px 12px; border-radius: 5px; font-weight: 600; font-size: 13px; background-color: #f8d7da; color: #721c24; border: none; cursor: pointer;">
                                                    Cancel Order
                                                </button>
                                            <?php } ?>
                                        </div>

                                        <div class="order-details">
                                            <p><strong>Order ID:</strong> <span>#<?= $orderDetails['order_id'] ?></span></p>
                                            <p><strong>Order Date:</strong> <span><?= $orderDetails['created_at'] ?></span></p>
                                            <p><strong>Ship To:</strong> <span><?= htmlspecialchars($fullAddress) ?></span></p>
                                            <p><strong>Payment Status:</strong> <span><?= $orderDetails['payment_status'] ?? 'Pending' ?></span></p>
                                            <p><strong>Total:</strong> <span>LKR <?= number_format($total_amount, 2) ?></span></p>
                                        </div>
                                    </div>
                                    <div class="order-items">
                                        <?php foreach ($order['items'] as $item) { ?>
                                            <div class="item">
                                                <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="product image" width="100" height="100">
                                                <div>
                                                    <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                                                    <p>Qty: <?= intval($item['quantity']) ?></p>
                                                    <p class="price">LKR <?= number_format($item['price'], 2) ?></p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>  
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p>No orders found for your account.</p>";
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelButtons = document.querySelectorAll('.cancel-order-btn');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id');

            if (confirm("Are you sure you want to cancel this order?")) {
                fetch('', { // Send to same file (empty string means current page)
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'order_id=' + orderId
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('Order cancelled successfully.');
                        location.reload();
                    } else {
                        alert('Failed to cancel order.');
                    }
                });
            }
        });
    });
});
</script>

</html>
