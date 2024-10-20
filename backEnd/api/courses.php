<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM courses";
$stmt = $db->prepare($query);
$stmt->execute();

$courses = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
