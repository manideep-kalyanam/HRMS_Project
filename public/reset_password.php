<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['temp_password']) || !$_SESSION['temp_password']) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $conn->prepare("UPDATE hrms_users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $new_password, 'id' => $user_id]);

        unset($_SESSION['temp_password']);
        header("Location: dashboard.php");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<?php require '../includes/header.php'; ?>
<h1>Reset Password</h1>
<form action="reset_password.php" method="POST">
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit">Reset Password</button>
</form>
<?php require '../includes/footer.php'; ?>
