<?php
require_once 'Database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Database connection successful.";
} else {
    echo "Database connection failed.";
}
?>
