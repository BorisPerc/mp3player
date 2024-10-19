<?php
	/* please replace the following values with the credentials to your mysql server */
	$db_host = "localhost";
	$db_user = "player";
	$db_password = "player";
	$db_databasename = "player";

	/* ===== DO NOT TOUCH THE FOLLOWING CODE ===== */
	$mysqli = new mysqli($db_host, $db_user, $db_password, $db_databasename);
	if ($mysqli->connect_errno) {
		die("Failed to connect to database server: " . $mysqli->connect_error);
	}
	$mysqli->set_charset("utf8");
?>
