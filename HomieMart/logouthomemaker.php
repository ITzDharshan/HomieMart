<?php
session_start();

if (isset($_SESSION['email'])) {
    unset($_SESSION['email']);
}
if (isset($_SESSION['role'])) {
    unset($_SESSION['role']);
}

session_write_close(); // This ensures that the session data is properly stored, but doesn't destroy other sessions

header("Location: HM_index.php");
exit();
?>
