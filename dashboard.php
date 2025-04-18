<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user's crop data
$stmt = $conn->prepare("SELECT crop_name, soil_moisture, resources_used FROM crops WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Agriculture Management</title>
    <link rel="stylesheet" href="dash.css">
    <!--<style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('images/farm-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .hidden {
            display: none;
        }

        h1 {
            color: #2d572c;
            font-size: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        input,
        button {
            width: 100%;
            padding: 0.8rem;
            margin: 0.8rem 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: 0.3s ease;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table, th, td {
            border: 1px solid #ddd;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>-->
</head>

<body>
    <div id="dashboard" class="form-container">
        <h1>Dashboard</h1>
        <h2>Welcome, <span id="username"><?php echo htmlspecialchars($username); ?></span></h2>
        <form action="add_data.php" method="POST">
            <input type="text" name="crop_name" placeholder="Crop Name" required>
            <input type="number" name="soil_moisture" placeholder="Soil Moisture (%)" required>
            <input type="text" name="resources_used" placeholder="Resources Used" required>
            <button type="submit">Add Data</button>
        </form>
        <h3>Your Data</h3>
        <table>
            <tr>
                <th>Crop Name</th>
                <th>Soil Moisture</th>
                <th>Resources Used</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['crop_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['soil_moisture']) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['resources_used']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No data found. Add your first crop above!</td></tr>";
            }
            ?>
        </table>
        <button onclick="logout()" style="margin-top: 20px;">Logout</button>
    </div>

    <script>
        function logout() {
            window.location.href = 'logout.php';
        }
    </script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>