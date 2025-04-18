<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validate input
    if (empty($username) || empty($password)) {
        echo "<script>alert('Username and password are required!'); window.location.href='index.html';</script>";
        exit();
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a new session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Password is incorrect
            echo "<script>alert('Invalid username or password!'); window.location.href='index.html';</script>";
        }
    } else {
        // Username doesn't exist
        echo "<script>alert('User does not exist. Please register first.'); window.location.href='index.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    // If someone tries to access this page directly
    header("Location: index.html");
    exit();
}
?>

