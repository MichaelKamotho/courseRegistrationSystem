<?php
session_start();
include_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT id, password FROM applicants WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $data->email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $password_hash = $row['password'];

        if (password_verify($data->password, $password_hash)) {
            echo json_encode(array("message" => "Login successful", "id" => $id));
        } else {
            echo json_encode(array("message" => "Invalid password."));
        }
    } else {
        echo json_encode(array("message" => "User not found."));
    }
} else {
    echo json_encode(array("message" => "Incomplete data."));
}
?>
