<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'smart_agriculture');

// Check for database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle different request types
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is trying to log in
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.html");
            exit();
        } else {
            echo "Invalid credentials.";
        }
    }

    // Check if user is trying to register
    if (isset($_POST['register_username']) && isset($_POST['register_password'])) {
        $username = $_POST['register_username'];
        $password = password_hash($_POST['register_password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            header("Location: index.html");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Check if user is trying to add farmer data
    if (isset($_POST['crop_name'])) {
        if (!isset($_SESSION['user_id'])) {
            die("Error: User not logged in.");
        }

        $user_id = $_SESSION['user_id'];
        $crop_name = $_POST['crop_name'];
        $soil_moisture = $_POST['soil_moisture'];
        $weather_report = $_POST['weather_report'];
        $instructions = $_POST['instructions'];
        $farmer_name = $_POST['farmer_name'];

        $field_image = NULL;
        $crop_disease_image = NULL;

        if (isset($_FILES["field_image"]) && $_FILES["field_image"]["error"] == 0) {
            $field_image = "uploads/" . basename($_FILES["field_image"]["name"]);
            move_uploaded_file($_FILES["field_image"]["tmp_name"], $field_image);
        }

        if (isset($_FILES["crop_disease_image"]) && $_FILES["crop_disease_image"]["error"] == 0) {
            $crop_disease_image = "uploads/" . basename($_FILES["crop_disease_image"]["name"]);
            move_uploaded_file($_FILES["crop_disease_image"]["tmp_name"], $crop_disease_image);
        }

        $stmt = $conn->prepare("INSERT INTO farmer_data (user_id, farmer_name, crop_name, soil_moisture, field_image, weather_report, instructions, crop_disease_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $farmer_name, $crop_name, $soil_moisture, $field_image, $weather_report, $instructions, $crop_disease_image);
        if ($stmt->execute()) {
            echo "Data added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Update farmer data
    if (isset($_POST['id']) && isset($_POST['farmer_name'])) {
        $id = $_POST['id'];
        $farmer_name = $_POST['farmer_name'];
        $crop_name = $_POST['crop_name'];
        $soil_moisture = $_POST['soil_moisture'];
        $weather_report = $_POST['weather_report'];
        $instructions = $_POST['instructions'];

        $stmt = $conn->prepare("UPDATE farmer_data SET farmer_name=?, crop_name=?, soil_moisture=?, weather_report=?, instructions=? WHERE id=?");
        $stmt->bind_param("ssissi", $farmer_name, $crop_name, $soil_moisture, $weather_report, $instructions, $id);
        
        if ($stmt->execute()) {
            echo "Data updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Handle DELETE requests correctly
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"), $delete_data);
    if (isset($delete_data['id'])) {
        $id = $delete_data['id'];
        $stmt = $conn->prepare("DELETE FROM farmer_data WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "Data deleted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

$conn->close();
?>
