<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Unset all cookies related to "Remember me"
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, "/");
}
if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, "/");
}
if (isset($_COOKIE['user_type'])) {
    setcookie('user_type', '', time() - 3600, "/");
}

// Redirect to login page
header("Location: index.php");
exit();
?>
