<?php

include_once '../config/database.php';
// Set content type to JSON
header("Content-Type: application/json");

// Check request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Handle GET request for fetching applications
    if (isset($_GET['applicant_id'])) {
        $applicant_id = $_GET['applicant_id'];
        
        // Database connection
        $database = new Database();
        $db = $database->getConnection();

        // Prepare query to fetch applications
        $query = "SELECT courses.name FROM applications JOIN courses ON applications.course_id = courses.id WHERE applications.applicant_id = :applicant_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->execute();

        // Fetch results and send as JSON
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($applications);
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Incomplete data. Please provide applicant_id."));
    }
} elseif ($method == 'POST') {
    // Handle POST request for application submission
    $data = json_decode(file_get_contents("php://input"));

    // Log the incoming data for debugging
    error_log(print_r($data, true));

    // Check the data received
    if (!empty($data->applicant_id) && !empty($data->courses) && is_array($data->courses)) {
        if (count($data->courses) > 3) {
            // Return error response if more than 3 courses are submitted
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "You can only apply for a maximum of 3 courses."));
            exit();
        }

        $database = new Database();
        $db = $database->getConnection();
        
        // Begin a transaction
        $db->beginTransaction();
        
        try {
            // First, let's clear any previous application data for this user
            $deleteQuery = "DELETE FROM applications WHERE applicant_id = :applicant_id";
            $deleteStmt = $db->prepare($deleteQuery);
            $deleteStmt->bindParam(":applicant_id", $data->applicant_id);
            $deleteStmt->execute();
            
            // Insert each selected course into the `applications` table
            foreach ($data->courses as $course_id) {
                $query = "INSERT INTO applications (applicant_id, course_id) VALUES (:applicant_id, :course_id)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":applicant_id", $data->applicant_id);
                $stmt->bindParam(":course_id", $course_id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert course ID: $course_id");
                }
            }
            
            // Commit the transaction
            $db->commit();
            
            // Return success response
            http_response_code(200); // Success
            echo json_encode(array("message" => "Application submitted successfully."));
        } catch (Exception $e) {
            // Rollback the transaction if something failed
            $db->rollBack();
            http_response_code(500); // Internal Server Error
            echo json_encode(array("message" => "Error submitting application: " . $e->getMessage()));
        }
    } else {
        // Log the error for incomplete data
        if (empty($data->applicant_id)) {
            error_log("Applicant ID is missing.");
        }
        if (empty($data->courses)) {
            error_log("Courses array is missing or empty.");
        } else if (!is_array($data->courses)) {
            error_log("Courses is not an array.");
        }
        
        // Return error response for bad request
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Incomplete data. Please provide both applicant_id and courses."));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Method not allowed."));
}
?>
