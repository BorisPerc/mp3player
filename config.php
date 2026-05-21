<?php
/**
 * Configuration File for MP3 Player
 * PHP 8.2+ Compatible Version
 */

// ============ DATABASE CONFIGURATION ============
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'PASSWORD');
define('DB_NAME', 'DATABASE');

// ============ MEDIA CONFIGURATION ============
// Base music directory - can be overridden here
define('MEDIAROOT', 'music');

// Uncomment and set your custom music directory if needed
// define('MEDIAROOT', '/media/audio/mp3/deephouse/');

// ============ APPLICATION SETTINGS ============
define('ALLOW_DOWNLOADS', true);
define('LOG_DOWNLOADS', true);
define('THUMB_DIR', 'music_thumb');

// ============ SESSION CONFIGURATION ============
$session_length_seconds = 60 * 60 * 24 * 7; // 7 days
define('SESSION_LENGTH', $session_length_seconds);

// ============ ERROR HANDLING FOR PHP 8.2+ ============
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
?>