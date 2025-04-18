<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address!'); window.location.href='forgot_password.html';</script>";
        exit();
    }
    
    // Check if email exists in database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        echo "<script>alert('Email not found in our records!'); window.location.href='forgot_password.html';</script>";
        exit();
    }
    
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Update user with reset token
    $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $update->bind_param("sss", $token, $expires, $email);
    $update->execute();
    
    // In a real application, you would send an email with the reset link
    // For this example, we'll just display the link
    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
    
    echo "<script>alert('Password reset link has been generated. In a real application, this would be emailed to you. For testing, you can use this link: " . $reset_link . "'); window.location.href='index.html';</script>";
    
    $stmt->close();
    $update->close();
} else {
    // Display the forgot password form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Smart Agriculture</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>Forgot Password</h1>
        <form action="forgot_password.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Reset Password</button>
        </form>
        <p>Remember your password? <a href="index.html">Back to Login</a></p>
    </div>
</body>
</html>
<?php
}
$conn->close();
?>