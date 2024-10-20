<?php
include_once '../config/database.php';

// Set content type to JSON
header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

$query = "SELECT name, description FROM courses";
$stmt = $db->prepare($query);
$stmt->execute();

$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($courses);
?>
