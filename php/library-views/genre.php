<?php
/**
 * Genre View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../global.php';

$sql = "SELECT DISTINCT genre FROM track WHERE genre IS NOT NULL AND genre != '' ORDER BY genre ASC";
$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $genre = $row['genre'] ?? "";
        if (empty($genre)) continue;
        
        $short_title = shortText($genre);
        $genre_encoded = urlencode($genre);
        
        echo "<li><a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=track&genre=$genre_encoded\");' title='" . htmlspecialchars($genre, ENT_QUOTES) . "'>" . htmlspecialchars($short_title) . "</a></li>";
    }
}
?>