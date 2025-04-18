<?php
echo "<h2>MySQL Connection Test</h2>";

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$port = 3307;

try {
    // Try to create a connection
    $conn = new mysqli($servername, $username, $password, "", $port);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    echo "<p style='color:green;'>MySQL Connection Successful!</p>";

    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE 'smart_agriculture'");

    if ($result->num_rows > 0) {
        echo "<p style='color:green;'>Database 'smart_agriculture' exists!</p>";

        // Select the database
        $conn->select_db("smart_agriculture");

        // Check if tables exist
        $tables = array("users", "crops");
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<p style='color:green;'>Table '$table' exists!</p>";
            } else {
                echo "<p style='color:orange;'>Table '$table' does not exist yet.</p>";
            }
        }
    } else {
        echo "<p style='color:orange;'>Database 'smart_agriculture' does not exist yet.</p>";
    }

    $conn->close();
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<p><a href="index.html">Back to Home</a></p>
