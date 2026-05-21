<?php
/**
 * Get Cover Art - PHP 8.2+ Compatible
 */

require_once 'config.php';
require_once 'session.php';
require_once 'global.php';
require_once 'database.php';

if (isset($_GET['title'])) {
    $track_id = (int)($_GET['title'] ?? 0);
    
    if ($track_id <= 0) {
        echo "<!-- invalid track id -->";
        exit();
    }
    
    $sql = "SELECT cover FROM track WHERE id = ? LIMIT 1";
    $statement = $mysqli->prepare($sql);
    
    if (!$statement) {
        echo "<!-- database error -->";
        exit();
    }
    
    $statement->bind_param('i', $track_id);
    if (!$statement->execute()) {
        echo "<!-- execution error -->";
        $statement->close();
        exit();
    }
    
    $result = $statement->get_result();
    $cover = "";
    
    if ($row = $result->fetch_assoc()) {
        $cover = $row['cover'] ?? "";
    }
    
    $statement->close();
    
    if (!empty($cover) && file_exists($cover)) {
        echo "<img src=\"" . htmlspecialchars($cover) . "\">";
    } else {
        echo "<!-- no cover -->";
    }
} else {
    echo "<!-- no track id -->";
}
?>