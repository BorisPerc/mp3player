<?php
require_once('session.php');
require_once('global.php');
session_write_close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Scanning audio files</title>
	<meta charset="utf-8"/>
	<script type="text/javascript" src="js/global.js"></script>
	<link href="css/global.css" rel="stylesheet">
	<link href="css/scan.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

	<div id="loader">
		<img id="imgloader" src="img/saw.svg" />
		<br>
		<div>Scan in progress, please wait...</div>
	</div>

	<div id="scanprogress">
	<?php
	try {
		require_once('php/getID3/getid3/getid3.php');
		$getID3 = new getID3();

		require_once('database.php');
		$THUMB_DIR = THUMB_DIR;
		$MUSIC_DIR = MEDIAROOT;

		// Handle rescan
		if (isset($_GET['rescan']) && $_GET['rescan'] == 1) {
			echo "<b>Cleaning database...</b><br>";
			if (!$mysqli->multi_query(file_get_contents("sql/clean.sql"))) {
				throw new Exception("Database cleanup failed: " . $mysqli->error);
			}
			clearStoredResults($mysqli);

			$files = @glob($THUMB_DIR . '/*');
			if ($files) {
				foreach ($files as $file) {
					if (is_file($file)) @unlink($file);
				}
			}
			echo "<b>Database cleaned.</b><br><br>";
		}

		echo "<b>Scanning: " . htmlspecialchars($MUSIC_DIR) . "</b><br>";

		$counter = 0;
		$fs_perm_warned = false;

		if (!is_dir($THUMB_DIR)) {
			mkdir($THUMB_DIR, 0755, true);
		}

		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($MUSIC_DIR));
		foreach ($it as $file) {
			$file_path = (string)$file;

			if (!isAudioFile($file_path)) {
				continue;
			}

			try {
				// Check if already in database
				$track_id = -1;
				$track_cover = "";
				$sql = "SELECT id, cover FROM track WHERE path = ?";
				$statement = $mysqli->prepare($sql);
				if ($statement) {
					$statement->bind_param('s', $file_path);
					if ($statement->execute()) {
						$result = $statement->get_result();
						if ($row = $result->fetch_assoc()) {
							$track_id = (int)($row['id'] ?? -1);
							$track_cover = $row['cover'] ?? "";
						}
					}
					$statement->close();
				}

				// Analyze file
				$FileInfo = $getID3->analyze($file_path);
				if (method_exists('getid3_lib', 'CopyTagsToComments')) {
					getid3_lib::CopyTagsToComments($FileInfo);
				}

				// Extract metadata safely
				$comments = $FileInfo['comments_html'] ?? [];
				$title = $comments['title'][0] ?? pathinfo($file_path, PATHINFO_FILENAME);
				$album = $comments['album'][0] ?? "Unknown Album";
				$artist = $comments['artist'][0] ?? "Unknown Artist";
				$genre = $comments['genre'][0] ?? "";
				$playtime = (int)($FileInfo['playtime_seconds'] ?? 0);
				$filelength = (int)(@filesize($file_path) ?? 0);

				// Extract track number
				$track_number = 0;
				if (isset($comments['track_number'][0])) {
					$tn = $comments['track_number'][0];
					if (strpos($tn, '/') !== false) {
						$tn = explode('/', $tn)[0];
					}
					$track_number = (int)filter_var($tn, FILTER_SANITIZE_NUMBER_INT);
				}

				// Handle cover
				$cover = null;
				if (isset($FileInfo['comments']['picture'][0]) && is_array($FileInfo['comments']['picture'][0])) {
					if ($track_id == -1 || $track_cover == "") {
						$cover_file = $THUMB_DIR . "/" . findFreeImageNumber() . ".jpg";
					} else {
						$cover_file = $track_cover;
					}

					$pic_data = $FileInfo['comments']['picture'][0]['data'] ?? null;
					if ($pic_data) {
						if (@file_put_contents($cover_file, $pic_data) === false && !$fs_perm_warned) {
							$fs_perm_warned = true;
							echo "<b>WARNING:</b> Cannot write to thumbnail directory.<br>";
						} else {
							$cover = $cover_file;
						}
					}
				}

				// Insert/update track
				$sql = "CALL InsertUpdateTrack(?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$statement = $mysqli->prepare($sql);
				if ($statement) {
					$statement->bind_param(
						'sssssiiis',
						$title, $album, $artist, $file_path, $track_number,
						$cover, $playtime, $filelength, $genre
					);
					if (!$statement->execute()) {
						echo "<b>Error processing:</b> " . htmlspecialchars($file_path) . "<br>";
					}
					$statement->close();
				}

				flush(); ob_flush();
				$counter++;

			} catch (Exception $e) {
				error_log("Scan error: " . $e->getMessage());
				continue;
			}
		}

		// Clean removed tracks
		echo "<br><b>Cleaning removed tracks...</b><br>";
		$sql = "SELECT id, path FROM track";
		$statement = $mysqli->prepare($sql);
		if ($statement) {
			$statement->execute();
			$result = $statement->get_result();
			while ($row = $result->fetch_assoc()) {
				if (!file_exists($row['path'])) {
					$del_stmt = $mysqli->prepare("DELETE FROM track WHERE id = ?");
					if ($del_stmt) {
						$del_stmt->bind_param('i', $row['id']);
						$del_stmt->execute();
						$del_stmt->close();
					}
				}
			}
			$statement->close();
		}

		// Purge orphaned
		echo "<b>Purging orphaned albums/artists...</b><br>";
		$sql = "CALL PurgeAlbumArtist()";
		$statement = $mysqli->prepare($sql);
		if ($statement) {
			$statement->execute();
			$statement->close();
		}

		clearStoredResults($mysqli);

		echo "<br><b style='color:green'>✓ Scan finished!</b><br>";
		echo "<b>Total tracks: $counter</b><br>";
		echo "<a href='player.php' class='styled_link'>Open Player</a>";

	} catch (Exception $e) {
		error_log("Scan failed: " . $e->getMessage());
		echo "<br><b style='color:red'>Error:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
		echo "<a href='player.php' class='styled_link'>Back to Player</a>";
	}

	function findFreeImageNumber(): int {
		$counter = 1;
		while (file_exists(THUMB_DIR . "/" . $counter . ".jpg")) {
			$counter++;
			if ($counter > 100000) break;
		}
		return $counter;
	}
	?>
	</div>

	<script>obj('imgloader').style.animation = "none";</script>

</body>
</html>