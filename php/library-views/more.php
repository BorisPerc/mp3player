<?php
/**
 * More Options View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../global.php';
?>

<li class='option tracklisting'>
    <a href='#' onclick="ajaxRequest('content','library.php?view=genre');"><img src='img/track.svg'>Genres</a>
</li>
<li class='option tracklisting'>
    <a href='#' onclick="ajaxRequest('content','library.php?view=recentlyadded');"><img src='img/recent.svg'>Recently added tracks</a>
</li>
<li class='option tracklisting'>
    <a href='#' onclick="ajaxRequest('content','library.php?view=stats');"><img src='img/menu.svg'>Statistics</a>
</li>