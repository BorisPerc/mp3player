<?php
/**
 * Playlist View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../global.php';

// Handle new playlist
if (isset($_GET['new_playlist_name'])) {
    $playlist_name = $_GET['new_playlist_name'] ?? '';
    $sql = "INSERT INTO playlist (title, type) VALUES (?, 0)";
    $statement = $mysqli->prepare($sql);
    if ($statement) {
        $statement->bind_param('s', $playlist_name);
        $statement->execute();
        $statement->close();
    }
}

// Handle remove playlist
if (isset($_GET['remove_playlist'])) {
    $playlist_id = (int)($_GET['remove_playlist'] ?? 0);
    if ($playlist_id > 0) {
        $sql = "DELETE FROM playlist WHERE id = ?";
        $statement = $mysqli->prepare($sql);
        if ($statement) {
            $statement->bind_param('i', $playlist_id);
            $statement->execute();
            $statement->close();
        }
    }
}

echo "<div class='playlistOptions'>";
echo "<button id='btnPlaylistNew' class='roundButton roundButtonSmall btnAdd btnPadding' onclick='createPlaylist();' title='Create a new playlist'>Create new playlist</button>";
echo "</div><br>";

$sql = "SELECT id, title FROM playlist ORDER BY title ASC";
$result = $mysqli->query($sql);
$counter = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $playlist_id = (int)$row['id'];
        $playlist_title = htmlspecialchars($row['title'] ?? '');
        
        echo "<li>";
        echo "<a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=$playlist_id\");'>";
        echo "<img class='cover_libraray unknown' src='img/currentplaylist.svg'>";
        echo $playlist_title;
        echo "</a>";
        echo "</li>";
        $counter++;
    }
}

if ($counter == 0) {
    echo "<li>You don't have any playlists.</li>";
}
?>