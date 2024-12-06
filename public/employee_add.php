<?php 
    require '../config/db.php';
    require '../includes/auth.php';
    session_start();
?>
<?php require '../includes/header.php'; ?>
<h1 style="text-align: center; color: #0056D2;">Add Employee</h1>
<form class="add-employee-form" action="../process/employee_process.php" method="POST">
    <div class="form-group">
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" placeholder="Enter First Name" required>
    </div>
    <div class="form-group">
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" placeholder="Enter Last Name" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter Email" required>
    </div>
    <div class="form-group">
        <label for="department">Department</label>
        <input type="text" id="department" name="department" placeholder="Enter Department" required>
    </div>
    <div class="form-group">
        <label for="joining_date">Joining Date</label>
        <input type="date" id="joining_date" name="joining_date" required>
    </div>
    <div class="form-group">
        <label for="contact_info">Contact Info</label>
        <input type="text" id="contact_info" name="contact_info" placeholder="Enter Contact Info" required>
    </div>
    <div class="form-group full-width">
        <button type="submit" name="add_employee">Add Employee</button>
    </div>
</form>
<?php require '../includes/footer.php'; ?>
