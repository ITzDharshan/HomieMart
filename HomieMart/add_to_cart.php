<?php
session_start();
include 'db_connection.php'; // Include your database connection file

if (!isset($_SESSION['customer_email'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

$user_email = $_SESSION['customer_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    // Check if product already exists in cart
    $check_query = "SELECT * FROM cart WHERE user_email = ? AND product_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("si", $user_email, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if product already exists
        $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE user_email = ? AND product_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $user_email, $product_id);
        $stmt->execute();
    } else {
        // Insert new product into cart
        $insert_query = "INSERT INTO cart (user_email, product_id, product_name, price, image, quantity) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sisss", $user_email, $product_id, $product_name, $product_price, $product_image);
        $stmt->execute();
    }

    // Redirect to cart page
    header("Location: cart.php");
    exit();
}
?>
