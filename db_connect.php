<?php
/* Simple MySQLi connection for ReWear */
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sapa_rewear';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
