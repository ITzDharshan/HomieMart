<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_email'])) {
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['customer_email'];

if (isset($_GET['action'], $_GET['id'])) {
    $product_id = $_GET['id'];

    if ($_GET['action'] == 'increase') {
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_email = ? AND product_id = ?";
    } elseif ($_GET['action'] == 'decrease') {
        $sql = "UPDATE cart SET quantity = quantity - 1 WHERE user_email = ? AND product_id = ? AND quantity > 1";
    } elseif ($_GET['action'] == 'remove') {
        $sql = "DELETE FROM cart WHERE user_email = ? AND product_id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $user_email, $product_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: cart.php");
exit();
?>
