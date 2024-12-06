<?php
session_start(); // Initialize the session

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: home.php");
exit;
