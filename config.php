<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$username = "root";
$password = "";
$database = "library_system";

try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check the credentials in config.php and make sure the database '" . htmlspecialchars($database) . "' exists (import database.sql).");
}

?>
