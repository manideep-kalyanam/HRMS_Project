<?php
require '../config/db.php';
require '../includes/auth.php';
session_start();

// Ensure the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Fetch all employees
$stmt = $conn->query("
    SELECT 
        e.id AS employee_id, 
        u.firstname, 
        u.lastname 
    FROM hrms_employees e
    INNER JOIN hrms_users u ON e.user_id = u.id
");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch payroll records
$payroll_stmt = $conn->query("
    SELECT 
        p.*, 
        u.firstname, 
        u.lastname 
    FROM hrms_payroll p
    INNER JOIN hrms_employees e ON p.employee_id = e.id
    INNER JOIN hrms_users u ON e.user_id = u.id
");
$payroll_records = $payroll_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_payroll'])) {
    $payroll_id = $_POST['payroll_id'];
    $basic_salary = $_POST['basic_salary'];
    $allowances = $_POST['allowances'];
    $deductions = $_POST['deductions'];

    try {
        $stmt = $conn->prepare("
            UPDATE hrms_payroll 
            SET basic_salary = :basic_salary, 
                allowances = :allowances, 
                deductions = :deductions
            WHERE id = :id
        ");
        $stmt->execute([
            'basic_salary' => $basic_salary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'id' => $payroll_id,
        ]);

        header("Location: admin_payroll.php");
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payroll'])) {
    $employee_id = $_POST['employee_id'];
    $basic_salary = $_POST['basic_salary'];
    $allowances = $_POST['allowances'];
    $deductions = $_POST['deductions'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO hrms_payroll (employee_id, basic_salary, allowances, deductions)
            VALUES (:employee_id, :basic_salary, :allowances, :deductions)
        ");
        $stmt->execute([
            'employee_id' => $employee_id,
            'basic_salary' => $basic_salary,
            'allowances' => $allowances,
            'deductions' => $deductions,
        ]);

        // Redirect to the page to refresh the data
        header("Location: admin_payroll.php");
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


?>



<?php require '../includes/header.php'; ?>

<div class="manage-employees-header">
    <h1>Manage Payroll</h1>
    <button onclick="showAddPayroll()" class="add-employee-button" style="border:none !important;">Add Payroll</button>
</div>

<table>
    <thead>
        <tr>
            <th>Employee</th>
            <th>Basic Salary</th>
            <th>Allowances</th>
            <th>Deductions</th>
            <th>Net Salary</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($payroll_records as $record): ?>
        <tr id="row-<?php echo $record['id']; ?>">
            <td><?php echo htmlspecialchars($record['firstname'] . ' ' . $record['lastname']); ?></td>
            <td><?php echo htmlspecialchars(number_format($record['basic_salary'], 2)); ?></td>
            <td><?php echo htmlspecialchars(number_format($record['allowances'], 2)); ?></td>
            <td><?php echo htmlspecialchars(number_format($record['deductions'], 2)); ?></td>
            <td><?php echo htmlspecialchars(number_format($record['net_salary'], 2)); ?></td>
            <td>
                <a href="javascript:void(0);" class="icon-button" onclick="showEditForm(<?php echo htmlspecialchars(json_encode($record)); ?>)">
                    <img src="../assets/images/edit.png" alt="Edit" title="Edit">
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="editForm" class="edit-payroll-form-container" style="display: none;">
    <h2 style="text-align: center; color: #0056D2;">Edit Payroll</h2>
    <form class="edit-payroll-form" method="POST">
        <input type="hidden" name="payroll_id" id="edit_payroll_id">
        <div class="form-group">
            <label for="edit_basic_salary">Basic Salary:</label>
            <input type="number" id="edit_basic_salary" name="basic_salary" required>
        </div>
        <div class="form-group">
            <label for="edit_allowances">Allowances:</label>
            <input type="number" id="edit_allowances" name="allowances">
        </div>
        <div class="form-group">
            <label for="edit_deductions">Deductions:</label>
            <input type="number" id="edit_deductions" name="deductions">
        </div>
        <div class="form-group full-width">
            <button type="submit" name="edit_payroll" class="update-button">Update</button>
            <button type="button" class="cancel-button" onclick="hideEditForm()">Cancel</button>
        </div>
    </form>
</div>

<form id="addPayrollSection" method="POST" class="edit-payroll-form hidden">
    <h3 style="color: #0056D2; text-align: center; margin-bottom: 20px">Add Payroll</h3>
    <div class="form-group">
        <label for="employee_id">Employee:</label>
        <select id="employee_id" name="employee_id" required>
            <option value="">-- Select Employee --</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?php echo $employee['employee_id']; ?>">
                    <?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="basic_salary">Basic Salary:</label>
        <input type="number" id="basic_salary" name="basic_salary" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="allowances">Allowances:</label>
        <input type="number" id="allowances" name="allowances" step="0.01">
    </div>
    <div class="form-group">
        <label for="deductions">Deductions:</label>
        <input type="number" id="deductions" name="deductions" step="0.01">
    </div>
    <div class="form-group full-width">
        <button type="submit" name="add_payroll" class="update-button">Add Payroll</button>
        <button type="button" class="cancel-button" onclick="hideAddForm()">Cancel</button>
    </div>
</form>

<?php require '../includes/footer.php'; ?>

<script>
    function showEditForm(record) {
        document.getElementById('edit_payroll_id').value = record.id;
        document.getElementById('edit_basic_salary').value = record.basic_salary;
        document.getElementById('edit_allowances').value = record.allowances;
        document.getElementById('edit_deductions').value = record.deductions;
        document.getElementById('editForm').style.display = 'block';
        hideAddForm();
    }

    function showAddPayroll() {
        hideEditForm();
        document.getElementById('addPayrollSection').style.display = 'block';
    }

    function hideEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }

    function hideAddForm() {
        document.getElementById('addPayrollSection').style.display = 'none';
    }

</script>


