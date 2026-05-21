<?php
/**
 * Session Variables Handler - PHP 8.2+ Compatible
 */

require_once 'config.php';

session_start();

if (isset($_SESSION['username'])) {
    // Set session variable
    if (isset($_GET['set']) && isset($_GET['value'])) {
        $key = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['set']);
        $value = $_GET['value'];
        
        if (in_array($key, ['volume', 'theme', 'language'])) {
            $_SESSION[$key] = $value;
        }
        exit();
    }
    
    // Get session variable
    if (isset($_GET['get']) && isset($_GET['value'])) {
        $key = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['value']);
        
        if (isset($_SESSION[$key])) {
            echo $_SESSION[$key];
        }
        exit();
    }
}
?>