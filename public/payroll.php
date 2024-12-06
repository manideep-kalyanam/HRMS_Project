<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
require '../config/db.php';
require '../includes/auth.php';
session_start();

// Ensure the user is an employee
if ($_SESSION['role'] !== 'employee') {
    header("Location: dashboard.php");
    exit;
}

// Fetch payroll details for the logged-in employee
$employee_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT 
        p.basic_salary, 
        p.allowances, 
        p.deductions, 
        p.net_salary 
    FROM hrms_payroll p
    INNER JOIN hrms_employees e ON p.employee_id = e.id
    WHERE e.user_id = :user_id
");
$stmt->execute(['user_id' => $employee_id]);
$payroll = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php require '../includes/header.php'; ?>
<div class="manage-employees-header">
    <h1>My Payroll</h1>
</div>
<div class="manage-employees-header">
    <?php if ($payroll): ?>
        <table>
            <tbody>
                <tr>
                    <td><strong>Basic Salary:</strong></td>
                    <td><?php echo htmlspecialchars(number_format($payroll['basic_salary'], 2)); ?></td>
                </tr>
                <tr>
                    <td><strong>Allowances:</strong></td>
                    <td><?php echo htmlspecialchars(number_format($payroll['allowances'], 2)); ?></td>
                </tr>
                <tr>
                    <td><strong>Deductions:</strong></td>
                    <td><?php echo htmlspecialchars(number_format($payroll['deductions'], 2)); ?></td>
                </tr>
                <tr>
                    <td><strong>Net Salary:</strong></td>
                    <td><?php echo htmlspecialchars(number_format($payroll['net_salary'], 2)); ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payroll details available.</p>
    <?php endif; ?>
</div>

<?php
    $basic_salary = $payroll['basic_salary'];
    $allowances = $payroll['allowances'];
    $deductions = $payroll['deductions'];
    $total_salary = $payroll['net_salary'];
    echo "
    <script>
        const payrollData = {
            totalSalary: $total_salary,
            basicSalary: $basic_salary,
            allowances: $allowances,
            deductions: $deductions
        };
    </script>
    ";
?>

<div style="max-width: 400px; margin: auto;">
    <canvas id="payrollChart"></canvas>
</div>

<?php require '../includes/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('payrollChart').getContext('2d');
        
        const data = {
            labels: ['Basic Salary', 'Allowances', 'Deductions'],
            datasets: [{
                label: 'Salary Breakdown',
                data: [payrollData.basicSalary, payrollData.allowances, payrollData.deductions],
                backgroundColor: ['#0056D2', '#87CEEB', '#FF5252'],
                hoverOffset: 4
            }]
        };

        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Payroll Distribution'
                    }
                }
            }
        };

        new Chart(ctx, config);
    });
</script>

