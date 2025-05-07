<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_email'])) {
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['customer_email'];

// Fetch cart items from database
$cart_query = "SELECT * FROM cart WHERE user_email = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Cart Page</title>
    <link rel="icon" type="image/png" href="images/shopping.png">
    <link rel="stylesheet" href="cart.css">
</head>
<style>
    .qty-btn {
    text-decoration: none;
}
</style>
<body>
    <div class="back-btn"><a href="marketplace.php">Continue Shopping</a></div>
    <div class="container">
        <div class="cart-section">
            <div class="cart-header">
                <h2>Cart</h2>
            </div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $subtotal = 0;
                        if ($result->num_rows > 0) {
                        while ($item = $result->fetch_assoc()) {
                        $total_price = $item['price'] * $item['quantity'];
                        $subtotal += $total_price;
                    ?>
                    <tr class="cart-item">
                        <td>
                            <div class="product">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product">
                                <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                            </div>
                        </td>
                        <td>LKR <?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="quantity-selector">
                                <a href="update_cart.php?action=decrease&id=<?php echo $item['product_id']; ?>" class="qty-btn">-</a>
                                <span class="qty"><?php echo $item['quantity']; ?></span>
                                <a href="update_cart.php?action=increase&id=<?php echo $item['product_id']; ?>" class="qty-btn">+</a>
                            </div>
                        </td>
                        <td>LKR <?php echo number_format($total_price, 2); ?></td>
                        <td>
                            <a href="update_cart.php?action=remove&id=<?php echo $item['product_id']; ?>">
                                <i class='bx bxs-message-square-x'></i>
                            </a>
                        </td>
                    </tr>
                <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>Your cart is empty.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="cart-totals">
            <h2>Cart Totals</h2>
            <p>Shipping: Free</p>
            <p>Tax: LKR 0</p>
            <p>Subtotal: LKR <?php echo number_format($subtotal, 2); ?></p>
            <p>Total: <strong>LKR <?php echo number_format($subtotal, 2); ?></strong></p>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
    </div>
</body>

<script>
    function updateQuantity(change, id) {
        let quantityElement = document.getElementById(id);
        let currentQuantity = parseInt(quantityElement.textContent);
        let newQuantity = currentQuantity + change;
        if (newQuantity < 1) newQuantity = 1;
        quantityElement.textContent = newQuantity;
    }
</script>

</html>