<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='index.html';</script>";
        exit();
    }
    
    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long!'); window.location.href='index.html';</script>";
        exit();
    }
    
    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email!'); window.location.href='index.html';</script>";
        exit();
    }
    
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username or Email already exists!'); window.location.href='index.html';</script>";
        exit();
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        // Registration successful, but DO NOT set session
        // Instead, redirect to login page with success message
        echo "<script>
            alert('Registration successful! Please login with your credentials.');
            window.location.href='index.html';
        </script>";
        exit();
    } else {
        echo "<script>alert('Registration failed: " . $stmt->error . "'); window.location.href='index.html';</script>";
    }
    
    $stmt->close();
}

$conn->close();
?>

