<?php
/**
 * Album View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../global.php';

$artistfilter = "";

if (isset($_GET['artist'])) {
    $artist_id = (int)($_GET['artist'] ?? 0);
    $artistfilter = "WHERE artist_id = " . $artist_id;

    // Create artist header
    $sql = "SELECT id, title FROM artist WHERE id = ? LIMIT 1";
    $statement = $mysqli->prepare($sql);
    if ($statement) {
        $statement->bind_param('i', $artist_id);
        $statement->execute();
        $result = $statement->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $currentartist = $row['title'] ?? "";
            $currentartistid = (int)$row['id'];

            echo "<li class='tracklisting'>";
            echo "<a href='#' title=\"back to artists\" onclick=\"ajaxRequest('content','library.php?view=artist" . htmlspecialchars($addtoplaylistparameter) . "','$currentartistid');\">";
            echo "<span class='track_number albumdescription'>&lt;</span>";
            echo "<span class='albumdescription'><span class='track_number albumdescription'>" . htmlspecialchars($currentartist) . "</span></span>";
            echo "</a>";
            echo "</li>";

            // All tracks link
            echo "<li>";
            echo "<a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=track&artist=$currentartistid" . htmlspecialchars($addtoplaylistparameter) . "\");'>";
            echo "<img class='cover_libraray' src='img/track.svg'>";
            echo "All tracks from this artist";
            echo "</a>";
            echo "</li>";
        }
        $statement->close();
    }
}

// Get albums
$sql = "SELECT al.id, al.title, ar.title as artist_title "
     . "FROM album al "
     . "INNER JOIN artist ar ON ar.id = al.artist_id "
     . "$artistfilter ORDER BY al.title ASC";

$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Get cover from first track
        $cover = "img/album.svg";
        $cover_class = "cover_libraray unknown";
        
        $sql2 = "SELECT cover FROM track WHERE album_id = ? LIMIT 1";
        $stmt2 = $mysqli->prepare($sql2);
        if ($stmt2) {
            $album_id = (int)$row['id'];
            $stmt2->bind_param('i', $album_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            if ($row2 = $result2->fetch_assoc()) {
                $cover_val = $row2['cover'] ?? "";
                if (!empty($cover_val) && file_exists($cover_val)) {
                    $cover = $cover_val;
                    $cover_class = "cover_libraray";
                }
            }
            $stmt2->close();
        }

        $title = htmlspecialchars($row['title'] . "\n" . $row['artist_title'], ENT_QUOTES);
        $album_id = (int)$row['id'];
        $short_title = shortText($row['title']);

        echo "<li>";
        echo "<a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=track&album=$album_id" . htmlspecialchars($addtoplaylistparameter) . "\");' title='$title'>";
        echo "<img class='$cover_class' src='" . htmlspecialchars($cover) . "'>";
        echo htmlspecialchars($short_title);
        echo "</a>";
        echo "</li>";
    }
}
?>