<?php
require '../config/db.php';

if (isset($_POST['add_employee'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $joining_date = $_POST['joining_date'];
    $contact_info = $_POST['contact_info'];

    // Generate temporary password
    $temporary_password = password_hash('Temp@123', PASSWORD_DEFAULT);

    try {
        // Insert user into hrms_users table
        $conn->beginTransaction();
        $stmt = $conn->prepare("INSERT INTO hrms_users (firstname, lastname, email, password, role) VALUES (:firstname, :lastname, :email, :password, 'employee')");
        $stmt->execute(['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'password' => $temporary_password]);

        $user_id = $conn->lastInsertId();

        // Insert employee into hrms_employees table
        $stmt = $conn->prepare("INSERT INTO hrms_employees (user_id, department, joining_date, contact_info) VALUES (:user_id, :department, :joining_date, :contact_info)");
        $stmt->execute(['user_id' => $user_id, 'department' => $department, 'joining_date' => $joining_date, 'contact_info' => $contact_info]);

        $conn->commit();
        header("Location: ../public/employees.php");
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}

?>
