<?php
// Enable error reporting for detailed debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('include_path', '/var/www/html/theProject/backEnd/config');
error_reporting(E_ALL);

// Include the database connection file
include_once __DIR__ . '/../config/database.php'; 
// Get input data from the AJAX request
$data = json_decode(file_get_contents("php://input"));

// Log the received data (check the PHP error log)
error_log(print_r($data, true));

if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    // Establish database connection
    $database = new Database();
    $db = $database->getConnection();

    // Prepare SQL query
    $query = "INSERT INTO applicants SET name=:name, email=:email, password=:password";
    $stmt = $db->prepare($query);

    // Bind parameters
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":email", $data->email);
    $password_hash = password_hash($data->password, PASSWORD_BCRYPT);  // Hash password for security
    $stmt->bindParam(":password", $password_hash);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(array("message" => "User registered successfully."));
    } else {
        // Log the SQL error if the query fails
        error_log("Failed to execute query: " . print_r($stmt->errorInfo(), true));
        echo json_encode(array("message" => "Unable to register user."));
    }
} else {
    // Log incomplete data error
    error_log("Incomplete data: " . print_r($data, true));
    echo json_encode(array("message" => "Incomplete data."));
}
?>
