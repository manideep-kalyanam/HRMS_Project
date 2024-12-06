<?php
require '../config/db.php';
require '../includes/auth.php'; // Ensures user is logged in

$user_role = $_SESSION['role'];
$user_firstname = $_SESSION['firstname'];
$user_lastname = $_SESSION['lastname'];
?>

<?php require '../includes/header.php'; ?>
<div class="dashboard-container">
    <h1>Welcome, <?php echo htmlspecialchars($user_firstname . ' ' . $user_lastname); ?>!</h1>

    <?php if ($user_role === 'admin'): ?>
        <h2>Admin Dashboard</h2>
        <div class="dashboard-grid">
            <a href="employees.php">
                <img src="../assets/images/employees_icon.png" alt="Manage Employees">
            </a>
            <a href="admin_attendance.php">
                <img src="../assets/images/attendance_icon.png" alt="Manage Attendance">
            </a>
            <a href="admin_payroll.php">
                <img src="../assets/images/payroll_icon.png" alt="Manage Payroll">
            </a>
        </div>
    <?php elseif ($user_role === 'employee'): ?>
        <h2>Employee Dashboard</h2>
        <div class="dashboard-grid">
            <a href="profile.php">
                <img src="../assets/images/profile_icon.png" alt="View Profile">
            </a>
            <a href="attendance.php">
                <img src="../assets/images/attendance_icon.png" alt="View Attendance">
            </a>
            <a href="payroll.php">
                <img src="../assets/images/payroll_icon.png" alt="View Salary Details">
            </a>
        </div>
    <?php endif; ?>

</div>
<?php require '../includes/footer.php'; ?>
