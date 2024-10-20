<?php
include_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"));
var_dump($data)
if (!empty($data->applicant_id) && !empty($data->courses)) {
    $database = new Database();
    $db = $database->getConnection();

    foreach ($data->courses as $course_id) {
        $query = "INSERT INTO applications SET applicant_id=:applicant_id, course_id=:course_id";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":applicant_id", $data->applicant_id);
        $stmt->bindParam(":course_id", $course_id);

        $stmt->execute();
    }

    echo json_encode(array("message" => "Application submitted successfully."));
} else {
    echo json_encode(array("message" => "Incomplete data."));
}
?>
