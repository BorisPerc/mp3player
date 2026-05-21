<?php
/**
 * Files View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../global.php';

$searchpath = MEDIAROOT;
if (isset($_GET['searchpath'])) {
    $requested_path = $_GET['searchpath'];
    if (is_sub_dir($requested_path, $searchpath)) {
        $searchpath = $requested_path;
    }
}

if (!is_dir($searchpath)) {
    echo "<li>Directory not found</li>";
    exit();
}

$files = @scandir($searchpath);
if ($files === false) {
    echo "<li>Cannot read directory</li>";
    exit();
}

foreach ($files as $file) {
    if ($file === "." || ($file === ".." && realpath($searchpath) === realpath(MEDIAROOT))) {
        continue;
    }

    $full_path = $searchpath . DIRECTORY_SEPARATOR . $file;
    $linkaction = "";
    $imgsrc = "";
    $shorttitle = shortText($file);
    
    if (is_dir($full_path)) {
        $linkaction = "href='#' onclick='ajaxRequest(\"content\",\"library.php?view=files&searchpath=" . urlencode($full_path) . "\");'";
        $imgsrc = "img/dir.svg";
    } elseif (isAudioFile($full_path)) {
        $linkaction = "href='player.php?currentplaylist=dir&track=" . urlencode($full_path) . "'";
        $imgsrc = "img/track.svg";
    } else {
        continue;
    }

    echo "<li>";
    echo "<a $linkaction title='" . htmlspecialchars($file) . "'>";
    echo "<img class='artistimg_libraray unknown' src='$imgsrc'>";
    echo htmlspecialchars($shorttitle);
    echo "</a>";
    echo "</li>";
}
?>