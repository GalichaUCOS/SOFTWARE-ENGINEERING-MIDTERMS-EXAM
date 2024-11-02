<?php
$host = 'localhost';
$port = '3306'; // MySQL default port
$dbname = 'threetwo'; // Your database name
$username = 'root'; // Default username for XAMPP
$password = ''; // Default password is empty for XAMPP

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
