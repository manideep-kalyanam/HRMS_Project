<?php
require '../config/db.php';
require '../includes/auth.php';
session_start();

// Ensure the user is an employee
if ($_SESSION['role'] !== 'employee') {
    header("Location: dashboard.php");
    exit;
}

$employee_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');

// Fetch attendance history
$stmt = $conn->prepare("
    SELECT date, status, remarks 
    FROM hrms_attendance 
    WHERE employee_id = (SELECT id FROM hrms_employees WHERE user_id = :user_id)
    ORDER BY date DESC
");
$stmt->execute(['user_id' => $employee_id]);
$attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if attendance is already marked for today
$check_stmt = $conn->prepare("
    SELECT status, remarks 
    FROM hrms_attendance 
    WHERE employee_id = (SELECT id FROM hrms_employees WHERE user_id = :user_id) AND date = :date
");
$check_stmt->execute(['user_id' => $employee_id, 'date' => $current_date]);
$today_record = $check_stmt->fetch(PDO::FETCH_ASSOC);

// Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO hrms_attendance (employee_id, date, status, remarks)
            VALUES ((SELECT id FROM hrms_employees WHERE user_id = :user_id), :date, :status, :remarks)
            ON DUPLICATE KEY UPDATE status = :status, remarks = :remarks
        ");
        $stmt->execute([
            'user_id' => $employee_id,
            'date' => $current_date,
            'status' => $status,
            'remarks' => $remarks
        ]);

        // Refresh the page to show updated data
        header("Location: attendance.php");
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<?php require '../includes/header.php'; ?>
<div class="manage-employees-header">
    <h1>My Attendance</h1>
</div>
<div class="manage-employees-header">
    <!-- Attendance marking -->
    <?php if (!$today_record): ?>
            <form method="POST" class="attendance-form">
                <label for="status">Mark Attendance:</label>
                <select id="status" name="status" required>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="leave">Leave</option>
                </select>
                <label for="remarks">Remarks (optional):</label>
                <input type="text" id="remarks" name="remarks">
                <button type="submit">Submit</button>
            </form>
    <?php else: ?>
        <div class="today-attendance">
            <p>Attendance for today (<strong><?php echo $current_date; ?></strong>) is already marked:</p>
            <p>Status: <strong><?php echo ucfirst($today_record['status']); ?></strong></p>
            <p>Remarks: <strong><?php echo htmlspecialchars($today_record['remarks']); ?></strong></p>
        </div>
    <?php endif; ?>
</div>

<!-- Attendance history -->
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Status</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attendance_records as $record): ?>
        <tr>
            <td><?php echo htmlspecialchars($record['date']); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($record['status'])); ?></td>
            <td><?php echo htmlspecialchars($record['remarks']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require '../includes/footer.php'; ?>
