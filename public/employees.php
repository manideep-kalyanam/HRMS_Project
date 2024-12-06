<?php
require '../config/db.php';
require '../includes/auth.php';
session_start();

// Fetch all employees with their details
$stmt = $conn->query("
    SELECT 
        hrms_employees.id AS employee_id, 
        hrms_users.firstname, 
        hrms_users.lastname, 
        hrms_users.email, 
        hrms_employees.department, 
        hrms_employees.joining_date, 
        hrms_employees.contact_info 
    FROM hrms_employees 
    INNER JOIN hrms_users ON hrms_employees.user_id = hrms_users.id
");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_employee'])) {
    $employee_id = $_POST['employee_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $joining_date = $_POST['joining_date'];
    $contact_info = $_POST['contact_info'];

    try {
        $conn->beginTransaction();

        // Update user details
        $stmt = $conn->prepare("
            UPDATE hrms_users 
            SET firstname = :firstname, lastname = :lastname, email = :email 
            WHERE id = (SELECT user_id FROM hrms_employees WHERE id = :employee_id)
        ");
        $stmt->execute([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'employee_id' => $employee_id,
        ]);

        // Update employee details
        $stmt = $conn->prepare("
            UPDATE hrms_employees 
            SET department = :department, joining_date = :joining_date, contact_info = :contact_info 
            WHERE id = :employee_id
        ");
        $stmt->execute([
            'department' => $department,
            'joining_date' => $joining_date,
            'contact_info' => $contact_info,
            'employee_id' => $employee_id,
        ]);

        $conn->commit();
        header("Location: employees.php");
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $employee_id = $_GET['delete_id'];

    try {
        $conn->beginTransaction();

        // Get the user ID associated with the employee
        $stmt = $conn->prepare("SELECT user_id FROM hrms_employees WHERE id = :employee_id");
        $stmt->execute(['employee_id' => $employee_id]);
        $user_id = $stmt->fetchColumn();

        if ($user_id) {
            // Delete from hrms_employees table
            $stmt = $conn->prepare("DELETE FROM hrms_employees WHERE id = :employee_id");
            $stmt->execute(['employee_id' => $employee_id]);

            // Delete from hrms_users table
            $stmt = $conn->prepare("DELETE FROM hrms_users WHERE id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            $conn->commit();
            header("Location: employees.php");
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>

<?php require '../includes/header.php'; ?>
<div class="manage-employees-header">
    <h1>Manage Employees</h1>
    <a href="employee_add.php" class="add-employee-button">Add Employee</a>
</div>
<!-- <table class="employee-table"> -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Joining Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $employee): ?>
        <tr>
            <td><?php echo $employee['employee_id']; ?></td>
            <td><?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?></td>
            <td><?php echo htmlspecialchars($employee['email']); ?></td>
            <td><?php echo htmlspecialchars($employee['department']); ?></td>
            <td><?php echo $employee['joining_date']; ?></td>
            <td>
                <a href="javascript:void(0);" class="icon-button" onclick="showEditForm(<?php echo htmlspecialchars(json_encode($employee)); ?>)">
                    <img src="../assets/images/edit.png" alt="Edit" title="Edit">
                </a>
                <a href="?delete_id=<?php echo $employee['employee_id']; ?>" class="icon-button" onclick="return confirm('Are you sure you want to delete this employee?')">
                    <img src="../assets/images/delete.png" alt="Delete" title="Delete">
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<!-- Edit Form (Modal or Inline) -->
<div id="editForm" class="edit-employee-form-container" style="display: none;">
    <h2 style="text-align: center; color: #0056D2;">Edit Employee</h2>
    <form class="edit-employee-form" method="POST">
        <input type="hidden" name="employee_id" id="edit_employee_id">
        <div class="form-group">
            <label for="edit_firstname">First Name</label>
            <input type="text" name="firstname" id="edit_firstname" placeholder="Enter First Name" required>
        </div>
        <div class="form-group">
            <label for="edit_lastname">Last Name</label>
            <input type="text" name="lastname" id="edit_lastname" placeholder="Enter Last Name" required>
        </div>
        <div class="form-group">
            <label for="edit_email">Email</label>
            <input type="email" name="email" id="edit_email" placeholder="Enter Email" required>
        </div>
        <div class="form-group">
            <label for="edit_department">Department</label>
            <input type="text" name="department" id="edit_department" placeholder="Enter Department" required>
        </div>
        <div class="form-group">
            <label for="edit_joining_date">Joining Date</label>
            <input type="date" name="joining_date" id="edit_joining_date" required>
        </div>
        <div class="form-group">
            <label for="edit_contact_info">Contact Info</label>
            <input type="text" name="contact_info" id="edit_contact_info" placeholder="Enter Contact Info">
        </div>

        <div class="form-group full-width">
            <button type="submit" name="edit_employee" class="update-button">Update</button>
            <button type="button" class="cancel-button" onclick="hideEditForm()">Cancel</button>
        </div>
    </form>
</div>


<script>
    function showEditForm(employee) {
        document.getElementById('edit_employee_id').value = employee.employee_id;
        document.getElementById('edit_firstname').value = employee.firstname;
        document.getElementById('edit_lastname').value = employee.lastname;
        document.getElementById('edit_email').value = employee.email;
        document.getElementById('edit_department').value = employee.department;
        document.getElementById('edit_joining_date').value = employee.joining_date;
        document.getElementById('edit_contact_info').value = employee.contact_info;
        document.getElementById('editForm').style.display = 'block';
    }

    function hideEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>
<?php require '../includes/footer.php'; ?>
