<?php
/**
 * Artist View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../global.php';

$sql = "SELECT ar.id, ar.title, "
     . "(SELECT cover FROM track WHERE track.artist_id=ar.id AND track.cover IS NOT NULL LIMIT 1) AS cover, "
     . "(SELECT COUNT(id) FROM album WHERE album.artist_id=ar.id) AS album_count, "
     . "(SELECT id FROM album WHERE album.artist_id=ar.id LIMIT 1) AS first_album_id "
     . "FROM artist ar "
     . "ORDER BY ar.title ASC";

$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cover = !empty($row['cover']) && file_exists($row['cover']) ? $row['cover'] : "img/artist.svg";
        $cover_class = !empty($row['cover']) && file_exists($row['cover']) ? "artistimg_libraray" : "artistimg_libraray unknown";
        
        $artist_id = (int)$row['id'];
        $album_count = (int)($row['album_count'] ?? 0);
        $first_album_id = (int)($row['first_album_id'] ?? 0);
        $artist_title = htmlspecialchars($row['title'], ENT_QUOTES);
        $short_title = shortText($row['title']);

        if ($album_count == 1) {
            $linkaction = "onclick='ajaxRequest(\"content\",\"library.php?view=track&album=$first_album_id" . htmlspecialchars($addtoplaylistparameter) . "\");'";
        } else {
            $linkaction = "onclick='ajaxRequest(\"content\",\"library.php?view=album&artist=$artist_id" . htmlspecialchars($addtoplaylistparameter) . "\");'";
        }

        echo "<li>";
        echo "<a href='#' id='element_$artist_id' $linkaction title='$artist_title'>";
        echo "<img class='$cover_class' src='" . htmlspecialchars($cover) . "'>";
        echo htmlspecialchars($short_title);
        echo "</a>";
        echo "</li>";
    }
}
?>