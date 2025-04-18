<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "smart_agriculture";
$port = 3307;

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("<script>alert('Database connection failed: " . $conn->connect_error . "'); window.location.href='index.html';</script>");
}

// Check if tables exist, if not create them
function createTablesIfNotExist($conn) {
    // Create users table if it doesn't exist
    $users_table = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        reset_token VARCHAR(255) DEFAULT NULL,
        reset_expires DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // Create crops table if it doesn't exist
    $crops_table = "CREATE TABLE IF NOT EXISTS crops (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        crop_name VARCHAR(100) NOT NULL,
        soil_moisture INT NOT NULL,
        resources_used TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";

    if ($conn->query($users_table) !== TRUE) {
        die("<script>alert('Error creating users table: " . $conn->error . "'); window.location.href='index.html';</script>");
    }

    if ($conn->query($crops_table) !== TRUE) {
        die("<script>alert('Error creating crops table: " . $conn->error . "'); window.location.href='index.html';</script>");
    }
}

// Call the function to create tables if they don't exist
createTablesIfNotExist($conn);
?>

