<?php
session_start(); // Start the session

// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

require '../includes/header.php';

if (isset($_SESSION['error_message'])): ?>
    <div style="display:flex; align-items:center; justify-content:center;">
        <div style="background-color:#FF5252; width:400px; color:#fff; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center;">
            <?php echo $_SESSION['error_message']; ?>
        </div>
    </div>
<?php unset($_SESSION['error_message']); endif; ?>
<style>
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        width: 100%;
    }

    form h2 {
        margin-bottom: 10px;
        font-size: 24px;
        color: #0056D2;
        text-align: center;
    }

    form input {
        width: 400px; 
        padding: 10px; 
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
    }

    form button {
        width: 400px; 
        padding: 10px;
        background-color: #0056D2;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    form button:hover {
        background-color: #0041A8; 
    }
</style>
<form action="../process/login_process.php" method="POST">
    <h2>Login</h2>
    <div class="form-group">
        <input type="email" name="email" placeholder="Email" required>
    </div>
    <div class="form-group">
        <input type="password" name="password" placeholder="Password" required>
    </div>
    <div class="form-group full-width">
        <button type="submit">Login</button>
    </div>
</form>
<?php require '../includes/footer.php'; ?>
