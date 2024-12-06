<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM hrms_users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Temporary password check
    if ($user && password_verify('Temp@123', $user['password'])) {
        $_SESSION['temp_password'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        header("Location: ../public/reset_password.php");
        exit;
    }

    // Regular login
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        header("Location: ../public/dashboard.php");
        exit;
    }

    // Login failed
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: ../public/login.php");
    exit;
}
?>
