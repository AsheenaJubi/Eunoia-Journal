<?php
// EUNOIA database connection.
// Update these values for your local XAMPP/MySQL setup.
$host = 'localhost';
$dbname = 'eunoia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
       "mysql:host={$host};port=3307;dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed. Please check config/database.php and make sure MySQL is running.");
}
?>