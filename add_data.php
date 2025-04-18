<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='index.html';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $crop_name = trim($_POST['crop_name']);
    $soil_moisture = intval($_POST['soil_moisture']);
    $resources_used = trim($_POST['resources_used']);
    

    // Validate input
    if (empty($crop_name) || empty($resources_used)) {
        echo "<script>alert('All fields are required!'); window.location.href='dashboard.php';</script>";
        exit();
    }

    // Insert crop data
    $stmt = $conn->prepare("INSERT INTO crops (user_id, crop_name, soil_moisture, resources_used) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis",$user_id, $crop_name, $soil_moisture, $resources_used);

    if ($stmt->execute()) {
        echo "<script>alert('Crop data added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding data: " . $stmt->error . "'); window.location.href='dashboard.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    // If someone tries to access this page directly
    header("Location: dashboard.php");
    exit();
}
?>

