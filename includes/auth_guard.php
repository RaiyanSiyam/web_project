<?php
// includes/auth_guard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page relative to the current script
    header("Location: ../../login.php");
    exit();
}
?>