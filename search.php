<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "mysql.railway.internal";
$username = "root";
$password = "uObMekHbYpJIGxyhCHZqtHyQqaoUaSnn";
$dbname = "railway"; // Ensure this is the correct database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname, MYSQLPORT);

// Initialize response array
$response = [];

// Check if the connection was successful
if ($conn->connect_error) {
    $response['error'] = 'Connection failed: ' . $conn->connect_error;
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get the search term from the request
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Check if search term is empty
if (empty($search)) {
    $response['error'] = 'Search term is empty.';
    header('Content-Type: application/json');
    echo json_encode($response);
    $conn->close();
    exit();
}

// Prepare to collect results from all tables
$allResults = [];

// Get all table names
$tablesResult = $conn->query("SHOW TABLES");
if ($tablesResult) {
    while ($tableRow = $tablesResult->fetch_array()) {
        $tableName = $tableRow[0];

        // Check if table has the necessary columns before querying
        $columnsResult = $conn->query("SHOW COLUMNS FROM `$tableName`");
        $columns = [];
        if ($columnsResult) {
            while ($columnRow = $columnsResult->fetch_assoc()) {
                $columns[] = $columnRow['Field'];
            }
        } else {
            $response['error'] = 'Failed to retrieve columns for table ' . $tableName . ': ' . $conn->error;
            header('Content-Type: application/json');
            echo json_encode($response);
            $conn->close();
            exit();
        }

        // Check if required columns are present
        $requiredColumns = [
            'no', 'first_name', 'middle_name', 'last_name', 'extension_name',
            'birthdate', 'street_zone_no', 'brgy', 'type_of_id',
            'id_no', 'contact_no', 'type_of_beneficiary', 'occupation',
            'sex', 'civil_status', 'age', 'average_monthly_income',
            'dependent', 'employment_interest', 'skills_training_needed'
        ];

        $hasRequiredColumns = !array_diff($requiredColumns, $columns);

        if ($hasRequiredColumns) {
            // Prepare the SQL query to search in the current table
            $sql = "SELECT * FROM `$tableName` WHERE
                first_name LIKE '%$search%' OR
                middle_name LIKE '%$search%' OR
                last_name LIKE '%$search%' OR
                extension_name LIKE '%$search%' OR
                birthdate LIKE '%$search%' OR
                street_zone_no LIKE '%$search%' OR
                brgy LIKE '%$search%' OR
                type_of_id LIKE '%$search%' OR
                id_no LIKE '%$search%' OR
                contact_no LIKE '%$search%' OR
                type_of_beneficiary LIKE '%$search%' OR
                occupation LIKE '%$search%' OR
                sex LIKE '%$search%' OR
                civil_status LIKE '%$search%' OR
                age LIKE '%$search%' OR
                average_monthly_income LIKE '%$search%' OR
                dependent LIKE '%$search%' OR
                employment_interest LIKE '%$search%' OR
                skills_training_needed LIKE '%$search%'";

            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $allResults[] = $row;
                }
            } else {
                $response['error'] = 'Query failed: ' . $conn->error;
                header('Content-Type: application/json');
                echo json_encode($response);
                $conn->close();
                exit();
            }
        }
    }
} else {
    $response['error'] = 'Failed to retrieve tables: ' . $conn->error;
    header('Content-Type: application/json');
    echo json_encode($response);
    $conn->close();
    exit();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($allResults);
?>
