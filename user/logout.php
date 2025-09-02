<?php
session_start();

// Unset only the session variable for the user
if (isset($_SESSION['userid'])) {
    unset($_SESSION['userid']);
}

// Optionally unset other user-specific session variables if any exist

// Redirect to login page or index page
header('location:../index.php');
exit();
?>
