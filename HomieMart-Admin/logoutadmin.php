<?php
session_start();

if (isset($_SESSION['admin_email'])) {
    unset($_SESSION['admin_email']);
}

session_write_close(); 

header("Location: index.php");
exit();
?>
