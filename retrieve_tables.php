<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "main_tupad_database";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Prepare the SQL query to retrieve all table names
$sql = "SHOW TABLES FROM `$dbname`";
$result = $conn->query($sql);

$tables = [];

if ($result) {
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
} else {
    $tables = ['error' => 'Query failed: ' . $conn->error];
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($tables);
?>
