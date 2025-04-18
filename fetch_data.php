<?php
// This file is included in the dashboard to fetch and display crop data
// Check if session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<tr><td colspan='4'>Please log in to view your data</td></tr>";
    exit();
}

// Include database connection if not already included
if (!isset($conn)) {
    include 'db_connect.php';
}

$user_id = $_SESSION['user_id'];

// Fetch user's crop data
$stmt = $conn->prepare("SELECT crop_name, soil_moisture, resources_used, created_at FROM crops WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['crop_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['soil_moisture']) . "%</td>";
        echo "<td>" . htmlspecialchars($row['resources_used']) . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No data found</td></tr>";
}

// Don't close the connection here as it might be used by the including file
?>

