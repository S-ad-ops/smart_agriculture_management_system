<?php
session_start();
include 'db_connect.php';

// Ensure token is provided
$token = isset($_GET['token']) ? $_GET['token'] : null;
if (!$token) {
    echo "<script>alert('Error: Reset token is missing.'); window.location.href='index.html';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($password) || empty($confirm_password)) {
        echo "<script>alert('Error: Both password fields are required.'); window.location.href='reset_password.php?token=" . $token . "';</script>";
        exit();
    }
    
    if ($password !== $confirm_password) {
        echo "<script>alert('Error: Passwords do not match.'); window.location.href='reset_password.php?token=" . $token . "';</script>";
        exit();
    }
    
    if (strlen($password) < 6) {
        echo "<script>alert('Error: Password must be at least 6 characters long.'); window.location.href='reset_password.php?token=" . $token . "';</script>";
        exit();
    }

    // Verify token is valid and not expired
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        echo "<script>alert('Invalid or expired reset token.'); window.location.href='index.html';</script>";
        exit();
    }

    // Hash new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password and remove reset token
    $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
    $update->bind_param("ss", $hashed_password, $token);

    if ($update->execute()) {
        echo "<script>alert('Password updated successfully. You can now login with your new password.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error updating password.'); window.location.href='index.html';</script>";
    }
    exit();
} else {
    // Verify token is valid and not expired
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "<script>alert('Invalid or expired reset token.'); window.location.href='index.html';</script>";
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Smart Agriculture</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>Reset Password</h1>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" name="password" placeholder="New Password" required minlength="6">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
<?php
}
$conn->close();
?>

