<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your CSS -->
</head>
<body>
    <header class="site-header">
        <nav class="navbar">
            <div class="logo">
                <img src="../assets/images/logo.png" alt="Company Logo">
            </div>
            <ul class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php // User is logged in ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="employees.php">Employees</a></li>
                        <li><a href="admin_attendance.php">Attendance</a></li>
                        <li><a href="admin_payroll.php">Payroll</a></li>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'employee'): ?>
                        <li><a href="attendance.php">Attendance</a></li>
                        <li><a href="payroll.php">Payroll</a></li>
                        <li><a href="profile.php">Profile</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <?php // User not logged in, show public pages ?>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="main-content">
