<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default WAMP password is empty

// First connect to MySQL server without selecting a DB
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Automatically create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS weather_db");
$conn->select_db("weather_db");

// Automatically create subscribers table if it doesn't exist
$tableQuery = "CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableQuery);

// Add password column if it doesn't exist (for new security update)
$checkColumn = $conn->query("SHOW COLUMNS FROM subscribers LIKE 'password'");
if ($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE subscribers ADD COLUMN password VARCHAR(255) DEFAULT NULL AFTER email");
}
?>
