<?php
$host = 'localhost';
$dbname = 'mkalyanam1';
$username = 'mkalyanam1';
$password = 'mkalyanam1';

// Creating a database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
