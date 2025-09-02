<?php
session_start();

// Unset only the session variable for the admin
if (isset($_SESSION['adminid'])) {
    unset($_SESSION['adminid']);
}

// Optionally unset other admin-specific session variables if any exist

// Redirect to admin login page or index page
header('location:index.php');
exit();
?>
