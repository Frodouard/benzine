<?php

declare(strict_types=1);

$host = "localhost";
$username = "root";
$password = "";
$database = "inventory_system";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db_fail(string $message): void
{
    http_response_code(500);
    echo "<!DOCTYPE html><html><head><title>Server Error</title></head>"
        . "<body style=\"font-family:Arial,sans-serif;text-align:center;margin-top:80px;\">"
        . "<h1>Database error</h1><p>" . htmlspecialchars($message) . "</p>"
        . "<p><a href=\"javascript:history.back()\">Go back</a></p></body></html>";
    exit;
}

try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    db_fail("Database connection failed. Please try again later.");
}
