<?php
/**
 * Playlist Content View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../global.php';

$playlist_id = (int)($_GET['playlist'] ?? 0);

if ($playlist_id <= 0) {
    die("Invalid playlist ID");
}

// Handle remove track
if (isset($_GET['remove_playlist_track'])) {
    $track_id = (int)($_GET['remove_playlist_track'] ?? 0);
    if ($track_id > 0) {
        $sql = "DELETE FROM playlist_track WHERE id = ?";
        $statement = $mysqli->prepare($sql);
        if ($statement) {
            $statement->bind_param('i', $track_id);
            $statement->execute();
            $statement->close();
        }
    }
}

// Handle move up
if (isset($_GET['moveup_playlist_track'])) {
    $track_id = (int)($_GET['moveup_playlist_track'] ?? 0);
    if ($track_id > 0) {
        $sql = "CALL MoveTrackInPlaylist(?, -1)";
        $statement = $mysqli->prepare($sql);
        if ($statement) {
            $statement->bind_param('i', $track_id);
            $statement->execute();
            $statement->close();
        }
    }
}

// Handle move down
if (isset($_GET['movedown_playlist_track'])) {
    $track_id = (int)($_GET['movedown_playlist_track'] ?? 0);
    if ($track_id > 0) {
        $sql = "CALL MoveTrackInPlaylist(?, 1)";
        $statement = $mysqli->prepare($sql);
        if ($statement) {
            $statement->bind_param('i', $track_id);
            $statement->execute();
            $statement->close();
        }
    }
}

// Get playlist name
$playlistname = "";
$sql = "SELECT title FROM playlist WHERE id = ? LIMIT 1";
$statement = $mysqli->prepare($sql);
if ($statement) {
    $statement->bind_param('i', $playlist_id);
    $statement->execute();
    $result = $statement->get_result();
    if ($row = $result->fetch_assoc()) {
        $playlistname = $row['title'] ?? "";
    }
    $statement->close();
}

echo "<div class='playlistOptions'>";
echo "<img class='cover_libraray unknown' src='img/currentplaylist.svg'><span class='playlist_title'>" . htmlspecialchars($playlistname) . "</span>";
echo "<button id='btnRemovePlaylist' class='roundButton roundButtonSmall btnTrash right btnMarginLeft' onclick='removePlaylist($playlist_id);' title='Remove this playlist'></button>";
echo "<button id='btnMoveTrackDown' class='roundButton roundButtonSmall btnMoveDown right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=$playlist_id&action=movedown\");' title='Move track(s) down'></button>";
echo "<button id='btnMoveTrackUp' class='roundButton roundButtonSmall btnMoveUp right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=$playlist_id&action=moveup\");' title='Move track(s) up'></button>";
echo "<button id='btnRemovePlaylistTrack' class='roundButton roundButtonSmall btnRemove right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=$playlist_id&action=remove\");' title='Remove track(s)'></button>";
echo "<button id='btnAddTrack' class='roundButton roundButtonSmall btnAdd right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=artist&addtoplaylist=$playlist_id\");' title='Add track(s)'></button>";
echo "</div><br>";

$action = $_GET['action'] ?? '';
if ($action === "remove") {
    echo "<div class='infobox error inlistinfo'>Please click on a track to remove it.</div>";
} elseif ($action === "moveup") {
    echo "<div class='infobox inlistinfo'>Please click on a track to move it up.</div>";
} elseif ($action === "movedown") {
    echo "<div class='infobox inlistinfo'>Please click on a track to move it down.</div>";
}

$sql = "SELECT @curRow := @curRow + 1 AS rank, pt.sequence, pt.track_id, pt.id, t.title "
     . "FROM playlist_track pt "
     . "INNER JOIN track t ON pt.track_id = t.id "
     . "JOIN (SELECT @curRow := 0) r "
     . "WHERE pt.playlist_id = ? "
     . "ORDER BY pt.sequence";

$statement = $mysqli->prepare($sql);
if ($statement) {
    $statement->bind_param('i', $playlist_id);
    $statement->execute();
    $result = $statement->get_result();
    $counter = 0;
    
    while ($row = $result->fetch_assoc()) {
        $pt_id = (int)$row['id'];
        $track_id = (int)$row['track_id'];
        $rank = (int)$row['rank'];
        $title = htmlspecialchars($row['title'] ?? '');
        
        if ($action === "remove") {
            $linkaction = "href='#' onclick='removeTrackFromPlaylist($playlist_id, $pt_id);'";
        } elseif ($action === "moveup") {
            $linkaction = "href='#' onclick='moveTrackUpPlaylist($playlist_id, $pt_id);'";
        } elseif ($action === "movedown") {
            $linkaction = "href='#' onclick='moveTrackDownPlaylist($playlist_id, $pt_id);'";
        } else {
            $linkaction = "href='player.php?currentplaylist=playlist&playlist=$playlist_id&track=$track_id'";
        }
        
        echo "<li><a class='first' $linkaction><span class='track_number'>$rank</span> $title</a></li>";
        $counter++;
    }
    
    $statement->close();
    
    if ($counter == 0) {
        echo "<li>This playlist contains no tracks.</li>";
    }
}
?>