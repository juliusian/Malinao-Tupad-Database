<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "mysql.railway.internal";
$username = "root";
$password = "uObMekHbYpJIGxyhCHZqtHyQqaoUaSnn";
$dbname = "railway";
$port = "3306";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the table name from the request
$table_name = $_GET['table'] ?? '';

if (empty($table_name)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No table specified']);
    exit();
}

// Sanitize table name to prevent SQL injection
$table_name = preg_replace('/[^a-zA-Z0-9_]/', '', $table_name);

// Check if the table exists
$table_exists_query = "SHOW TABLES LIKE '$table_name'";
$table_exists_result = $conn->query($table_exists_query);

if ($table_exists_result->num_rows == 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => "Table '$table_name' does not exist"]);
    exit();
}

// Prepare the SQL query to retrieve all rows from the specified table
$sql = "SELECT * FROM `$table_name`";
$result = $conn->query($sql);

$rows = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    exit();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($rows);
?>
