<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");
require_once('global.php');

?>

<li class='option tracklisting'>
	<div class='about'>
		<p>
			<img id='aboutLogo' src='img/mp3player.png'>
			<b>mp3Player html multimedia server (library and player) <?php echo $CVERSION; ?></b>
			<br>Licensed under the terms of the <a href='#' onclick="ajaxRequest('content','library.php?view=license');">GPLv2</a>
			<br><a href="https://github.com/BorisPerc/mp3player" target="_blank">Fork me on GitHub</a>
			<br>&copy; 2019 Audio Server
		</p>
		<p>
			This program uses the <a href='http://getid3.sourceforge.net/' target='blank'>getid3()</a> library v1.9.23
			<br>&copy; 2019 James Heinrich (License: <a href='#' onclick="ajaxRequest('content','library.php?view=license');">GPLv2</a>)
		</p>
	</div>
</li>

<li class='option tracklisting'>
	<a href='#' onclick="ajaxRequest('content','library.php?view=upload');"><img src='img/upload.svg'>📤 Upload and import tracks</a>
</li>
<li class='option tracklisting'>
	<a href='scan.php' target='_blank' onclick='return confirm("This will scan the >music< directory inside your media directory for new or changed tracks. Depending on the amount of tracks, this could take some time.");'><img src='img/search.svg'>🔎 Scan filesystem for tracks</a>
</li>
<li class='option tracklisting'>
	<a href='scan.php?rescan=1' target='_blank' onclick='return confirm("This will truncate your music database and completely rescan the >music< directory inside your media directory. Depending on the amount of tracks, this could take some time. You should only use this scan method, if you encounter problems with your database.");'><img src='img/search.svg'>💻 Completely rescan filesystem for tracks</a>
</li>
<li class='option tracklisting'>
	<a href='login.php?changepassword=1'><img src='img/password.svg'>🛅 Change password</a>
</li>
<li class='option tracklisting'>
	<a href='login.php?logout=1' onclick=''><img src='img/logout.svg'>🛃 Log out</a>
</li>
