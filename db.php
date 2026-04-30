<?php
$host     = 'localhost';
$db_name  = 'student_crud';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die('<div class="alert alert-danger m-3">Connection failed: ' . $conn->connect_error . '</div>');
}
?>
