<?php
session_start();

if (isset($_SESSION['customer_email'])) {
    unset($_SESSION['customer_email']);
}
if (isset($_SESSION['role'])) {
    unset($_SESSION['role']);
}

session_write_close(); // This ensures that the session data is properly stored, but doesn't destroy other sessions

header("Location: signin.php");
exit();
?>
