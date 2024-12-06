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

// Fetch attendance records for a specific date
$selected_date = $_GET['date'] ?? date('Y-m-d');
$attendance_stmt = $conn->prepare("
    SELECT 
        a.id, 
        a.employee_id, 
        a.status, 
        a.remarks, 
        u.firstname, 
        u.lastname 
    FROM hrms_attendance a
    INNER JOIN hrms_employees e ON a.employee_id = e.id
    INNER JOIN hrms_users u ON e.user_id = u.id
    WHERE a.date = :date
");
$attendance_stmt->execute(['date' => $selected_date]);
$attendance_records = $attendance_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for editing 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_attendance'])) {
    $attendance_id = $_POST['attendance_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'] ?? '';

    // Validate input
    if (empty($status)) {
        die("Error: Status cannot be empty.");
    }

    try {
        $stmt = $conn->prepare("
            UPDATE hrms_attendance 
            SET status = :status, remarks = :remarks 
            WHERE id = :id
        ");
        $stmt->execute([
            'status' => $status,
            'remarks' => $remarks,
            'id' => $attendance_id,
        ]);

        // Refresh the page to reflect changes
        header("Location: admin_attendance.php");
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


?>

<?php require '../includes/header.php'; ?>
<h1>Manage Attendance</h1>

<form method="GET" action="admin_attendance.php" class="attendance-form">
    <div class="form-row">
        <div class="form-group">
            <label for="date">Select Date</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn view-button">View Attendance</button>
        </div>
    </div>
</form>

<!-- Attendance Records -->
<table class="attendance-table">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attendance_records as $record): ?>
        <tr id="row-<?php echo $record['id']; ?>">
            <td><?php echo htmlspecialchars($record['firstname'] . ' ' . $record['lastname']); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($record['status'])); ?></td>
            <td><?php echo htmlspecialchars($record['remarks']); ?></td>
            <td>
                <a href="javascript:void(0);" class="icon-button" onclick="showEditForm(<?php echo htmlspecialchars(json_encode($record)); ?>)">
                    <img src="../assets/images/edit.png" alt="Edit" title="Edit">
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>


</table>

<!-- Edit Form (Modal or Inline) -->
<div id="editForm" class="edit-attendance-form-container" style="display: none;">
    <h2 style="text-align: center; color: #0056D2;">Edit Attendance</h2>
    <form class="edit-attendance-form" method="POST">
        <input type="hidden" name="attendance_id" id="edit_attendance_id">
        <div class="form-group">
            <label for="edit_status">Status</label>
            <select name="status" id="edit_status" required>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="leave">Leave</option>
            </select>
        </div>
        <div class="form-group">
            <label for="edit_remarks">Remarks</label>
            <input type="text" name="remarks" id="edit_remarks" placeholder="Enter Remarks">
        </div>
        <div class="form-group full-width">
            <button type="submit" name="edit_attendance" class="update-button">Update</button>
            <button type="button" class="cancel-button" onclick="hideEditForm()">Cancel</button>
        </div>
    </form>
</div>



<script>
function enableEdit(rowId) {
    const row = document.getElementById(`row-${rowId}`);
    const editButton = row.querySelector('.edit-button');

    const viewElements = row.querySelectorAll('.view-only');
    const editElements = row.querySelectorAll('.edit-mode');
    viewElements.forEach(el => el.style.display = 'none'); // Hide view-only elements
    editElements.forEach(el => el.style.display = 'inline-block'); // Show edit-mode elements (inputs and form)

    const form = row.querySelector('form');
    const statusInput = form.querySelector('input[name="status"]');
    const remarksInput = form.querySelector('input[name="remarks"]');

    // Show form and hide Edit button
    form.style.display = 'inline-block';
    editButton.style.display = 'none';

    // Dynamically update hidden inputs
    const statusSelect = row.querySelector('select[name="status"]');
    const remarksField = row.querySelector('input[name="remarks"]');

    statusSelect.addEventListener('change', () => {
        statusInput.value = statusSelect.value;
    });

    remarksField.addEventListener('input', () => {
        remarksInput.value = remarksField.value;
    });
}

function showEditForm(record) {
    // Populate the edit form with record data
    document.getElementById('edit_attendance_id').value = record.id;
    document.getElementById('edit_status').value = record.status;
    document.getElementById('edit_remarks').value = record.remarks || '';
    
    // Display the edit form
    document.getElementById('editForm').style.display = 'block';
}

function hideEditForm() {
    // Hide the edit form
    document.getElementById('editForm').style.display = 'none';
}

</script>

<?php require '../includes/footer.php'; ?>
