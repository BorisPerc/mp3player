<?php
/**
 * Recently Added View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../global.php';

$sql = "SELECT tr.id, tr.track_number, tr.title, al.title as album_title, ar.title as artist_title "
     . "FROM track tr "
     . "INNER JOIN album al ON tr.album_id = al.id "
     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
     . "ORDER BY tr.inserted DESC LIMIT 500";

$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $track_id = (int)($row['id'] ?? 0);
        $track_number = (int)($row['track_number'] ?? 0);
        $title = htmlspecialchars($row['title'] ?? '');
        $album_title = htmlspecialchars($row['album_title'] ?? '');
        $artist_title = htmlspecialchars($row['artist_title'] ?? '');
        
        if (isset($_GET['addtoplaylist'])) {
            $playlist_id = (int)($_GET['addtoplaylist'] ?? 0);
            $linkaction = "onclick='ajaxRequest(\"notification\",\"playlistedit.php?title=$track_id&addtoplaylist=$playlist_id\"); clearNotification();'";
        } else {
            $linkaction = "href='player.php?currentplaylist=recentlyadded&track=$track_id'";
        }
        
        $short_title = shortText($title);
        $full_title = "$title\n$album_title\n$artist_title";
        
        echo "<li><a $linkaction title=\"" . htmlspecialchars($full_title, ENT_QUOTES) . "\">" . $short_title . "</a></li>";
    }
}
?>