<?php
/**
 * Global Functions and Constants - PHP 8.2+ Compatible
 */

require_once 'config.php';

function is_sub_dir(string $path, string $parent_folder): bool {
    $path = realpath($path) ?: $path;
    $parent_folder = realpath($parent_folder) ?: $parent_folder;
    return strpos($path, $parent_folder) === 0;
}

$CVERSION = trim((string)@file_get_contents("CURRENTVERSION.txt")) ?: "1.0.0";
$NVERSION = "";

function isUpdateAvail(): bool {
    global $CVERSION, $NVERSION;
    
    $current_time = time();
    $last_check = $_SESSION['last_update_check'] ?? 0;
    
    if ($current_time - $last_check > 3600) {
        $version_file = "https://raw.githubusercontent.com/BorisPerc/mp3player/refs/heads/main/CURRENTVERSION.txt";
        $NVERSION = trim((string)@file_get_contents($version_file)) ?: $CVERSION;
        
        $update = version_compare($CVERSION, $NVERSION, "<");
        $_SESSION['last_update_check'] = $current_time;
        $_SESSION['last_update_result'] = $update;
    } else {
        $update = $_SESSION['last_update_result'] ?? false;
    }
    
    return (bool)$update;
}

function clearStoredResults($mysqli): void {
    while ($mysqli->more_results() && $mysqli->next_result()) {
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    }
}

function IsAlreadyEstablished($mysqli): bool {
    $statement = $mysqli->prepare("SHOW TABLES LIKE 'setting';");
    if (!$statement) {
        return false;
    }
    $statement->execute();
    $statement->store_result();
    $result = $statement->num_rows > 0;
    $statement->close();
    return $result;
}

function isAudioFile(string $filename): bool {
    $mime_types = [
        'mp3' => 'audio/mpeg',
        'mp4' => 'audio/mp4',
        'm4a' => 'audio/mp4',
        'ogg' => 'audio/ogg',
        'm4v' => 'audio/mp4',
    ];
    
    $tmp = explode('.', $filename);
    $ext = strtolower(end($tmp) ?: '');
    
    return isset($mime_types[$ext]);
}

function shortText(string $longtext, int $maxlength = 21): string {
    return strlen($longtext) > $maxlength ? substr($longtext, 0, $maxlength) . "..." : $longtext;
}

function startsWith(string $haystack, string $needle): bool {
    $length = strlen($needle);
    return $length === 0 || substr($haystack, 0, $length) === $needle;
}

function endsWith(string $haystack, string $needle): bool {
    $length = strlen($needle);
    return $length === 0 || substr($haystack, -$length) === $needle;
}
?>