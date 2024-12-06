<style>
    input {
        background-color: #E1F5FE;
    }
</style>
<?php
require '../config/db.php';
require '../includes/auth.php';
session_start();

// Ensure the user is an employee
if ($_SESSION['role'] !== 'employee') {
    header("Location: dashboard.php");
    exit;
}

// Fetch user and employee details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT 
        u.firstname, 
        u.lastname, 
        u.email, 
        e.department, 
        e.joining_date, 
        e.contact_info 
    FROM hrms_users u
    INNER JOIN hrms_employees e ON u.id = e.user_id
    WHERE u.id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $contact_info = $_POST['contact_info'];

    try {
        $conn->beginTransaction();

        // Update firstname and lastname in hrms_users
        $stmt = $conn->prepare("
            UPDATE hrms_users 
            SET firstname = :firstname, lastname = :lastname
            WHERE id = :user_id
        ");
        $stmt->execute([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'user_id' => $user_id
        ]);

        // Update contact_info in hrms_employees
        $stmt = $conn->prepare("
            UPDATE hrms_employees 
            SET contact_info = :contact_info
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            'contact_info' => $contact_info,
            'user_id' => $user_id
        ]);

        $conn->commit();
        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        // Roll back changes if something goes wrong
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}

?>

<?php require '../includes/header.php'; ?>
<h1>My Profile</h1>

<form id="profileForm" method="POST">
    <label for="firstname">First Name</label>
    <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($profile['firstname']); ?>" readonly>

    <label for="lastname">Last Name</label>
    <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($profile['lastname']); ?>" readonly>

    <label for="email">Email</label>
    <input type="email" id="email" value="<?php echo htmlspecialchars($profile['email']); ?>" readonly>

    <label for="department">Department</label>
    <input type="text" id="department" value="<?php echo htmlspecialchars($profile['department']); ?>" readonly>

    <label for="joining_date">Joining Date</label>
    <input type="date" id="joining_date" value="<?php echo htmlspecialchars($profile['joining_date']); ?>" readonly>

    <label for="contact_info">Contact</label>
    <input type="text" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($profile['contact_info']); ?>" readonly>

    <button type="button" id="editButton" onclick="enableEditMode()">Edit</button>
    <button type="submit" id="saveButton" name="save_changes" style="display: none;">Save</button>
</form>

<script>
    function enableEditMode() {
        // Enable editable fields
        document.getElementById('firstname').removeAttribute('readonly');
        document.getElementById('lastname').removeAttribute('readonly');
        document.getElementById('contact_info').removeAttribute('readonly');

        // Hide Edit button and show Save button
        document.getElementById('editButton').style.display = 'none';
        document.getElementById('saveButton').style.display = 'inline-block';

        var inputs = document.querySelectorAll('input');
        inputs.forEach(function(input) {
            if (!input.hasAttribute('readonly')) {
                input.style.backgroundColor = 'white';
            }
        });
    }
</script>

<?php require '../includes/footer.php'; ?>
