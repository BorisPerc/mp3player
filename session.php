<?php
/**
 * Session Management - PHP 8.2+ Compatible
 */

require_once 'config.php';

// Configure session
$session_options = [
    'lifetime' => SESSION_LENGTH,
    'path' => '/',
    'domain' => '',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
];

session_set_cookie_params($session_options);

// Start session
session_start();

// Check authentication
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>