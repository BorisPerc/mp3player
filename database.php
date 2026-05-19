<?php
/**
 * Database Connection - PHP 8.2+ Compatible
 */

require_once 'config.php';

try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    if ($mysqli->connect_errno) {
        throw new Exception("Failed to connect to database: " . $mysqli->connect_error);
    }
    
    $mysqli->set_charset("utf8mb4");
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("<h2>Database Connection Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>