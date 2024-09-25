<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "main_tupad_database";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if the form data is submitted
if (isset($_POST['table_data']) && isset($_POST['table_name'])) {
    $table_name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']); // Sanitize table name
    $table_data = json_decode($_POST['table_data'], true);

    if (!is_array($table_data) || empty($table_data)) {
        echo json_encode(['error' => 'Invalid or empty table data.']);
        exit();
    }

    // Create a new table with the specified name
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS `$table_name` (
            no INT,
            first_name VARCHAR(255),
            middle_name VARCHAR(255),
            last_name VARCHAR(255),
            extension_name VARCHAR(255),
            birthdate DATE,
            street_zone_no INT,
            brgy VARCHAR(255),
            type_of_id VARCHAR(255),
            id_no INT,
            contact_no INT,
            type_of_beneficiary VARCHAR(255),
            occupation VARCHAR(255),
            sex VARCHAR(10),
            civil_status VARCHAR(50),
            age INT,
            average_monthly_income DECIMAL(10, 2),
            dependent VARCHAR(10),
            employment_interest VARCHAR(10),
            skills_training_needed VARCHAR(255)
        )
    ";
    if (!$conn->query($createTableSQL)) {
        echo json_encode(['error' => 'Table creation failed: ' . $conn->error]);
        exit();
    }

    // Prepare the SQL statement to insert each row
    $stmt = $conn->prepare("
        INSERT INTO `$table_name` (
            no, first_name, middle_name, last_name, extension_name, birthdate, street_zone_no, brgy, type_of_id, id_no, contact_no, type_of_beneficiary, occupation, sex, civil_status, age, average_monthly_income, dependent, employment_interest, skills_training_needed
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    // Loop through each row of the table data
    foreach ($table_data as $row) {
        if (count($row) < 20) { // Check if row has enough data
            continue;
        }

        $stmt->bind_param(
            "issssssssssssssiisss",
            $row[0],  // no
            $row[1],  // first_name
            $row[2],  // middle_name
            $row[3],  // last_name
            $row[4],  // extension_name
            $row[5],  // birthdate
            $row[6],  // street_zone_no
            $row[7],  // brgy
            $row[8],  // type_of_id
            $row[9],  // id_no
            $row[10], // contact_no
            $row[11], // type_of_beneficiary
            $row[12], // occupation
            $row[13], // sex
            $row[14], // civil_status
            $row[15], // age
            $row[16], // avg_monthly_income
            $row[17], // dependent
            $row[18], // employment_interest
            $row[19]  // skills_training_needed
        );

        if (!$stmt->execute()) {
            echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
            exit();
        }
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['success' => 'Table data saved successfully!']);
} else {
    echo json_encode(['error' => 'No data received.']);
}
?>
