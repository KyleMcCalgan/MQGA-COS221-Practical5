<?php
function getDbConnection() {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $name = $_ENV['DB_NAME'] ?? die('DB_NAME not set.');
    $user = $_ENV['DB_USER'] ?? die('DB_USER not set.');
    $pass = $_ENV['DB_PASS'] ?? null;

    try {
        $conn = new mysqli($host, $user, $pass, $name);
        if ($conn->connect_error) die("DB Connect Failed: " . $conn->connect_error);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("DB Connection Exception: " . $e->getMessage());
    }
}
?>