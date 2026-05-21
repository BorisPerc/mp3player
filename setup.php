<?php
/**
 * Setup Handler - PHP 8.2+ Compatible
 */

require_once 'config.php';
require_once 'global.php';
require_once 'database.php';

$showsetupbtn = false;
$info = "";

if (IsAlreadyEstablished($mysqli)) {
    $info = "Your Audio database is<br>already ready to <a href='login.php' class='styled_link'>use</a>.";
} else {
    $showsetupbtn = true;
    $info = "<b>Database setup ahead</b><br><div>Click the button below to <br>create the required tables.</div><br>";

    if (isset($_POST['action']) && $_POST['action'] == "setup") {
        try {
            // Drop old tables
            $clean_sql = @file_get_contents("sql/clean.sql");
            if ($clean_sql && !$mysqli->multi_query($clean_sql)) {
                throw new Exception("Error dropping tables: " . $mysqli->error);
            }
            clearStoredResults($mysqli);

            // Create new tables
            $tables_sql = @file_get_contents("sql/tables.sql");
            if (!$tables_sql) {
                throw new Exception("Cannot read sql/tables.sql");
            }
            if (!$mysqli->multi_query($tables_sql)) {
                throw new Exception("Error creating tables: " . $mysqli->error);
            }
            clearStoredResults($mysqli);

            // Create procedures
            $procedures = [
                'sql/InsertUpdateTrack.sql' => 'InsertUpdateTrack',
                'sql/PurgeAlbumArtist.sql' => 'PurgeAlbumArtist',
                'sql/MoveTrackInPlaylist.sql' => 'MoveTrackInPlaylist',
            ];

            foreach ($procedures as $file => $name) {
                $sql = @file_get_contents($file);
                if (!$sql) {
                    throw new Exception("Cannot read $file");
                }
                if (!$mysqli->query($sql)) {
                    throw new Exception("Error creating procedure $name: " . $mysqli->error);
                }
                clearStoredResults($mysqli);
            }

            $showsetupbtn = false;
            $info = "<b>Setup finished.</b><br>You can now <a href='login.php' class='styled_link'>log in</a> without<br>a password. After that, go to<br><i>options</i> and scan for tracks.";

        } catch (Exception $e) {
            $info = "<b style='color:red'>Setup Error:</b><br>" . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audio Server - Setup</title>
    <meta charset="utf-8"/>
    <script type="text/javascript" src="js/global.js"></script>
    <link href="css/global.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat+Subrayada|Open+Sans+Condensed:300" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui">
    <link rel="icon" type="image/png" href="img/piramid.png">
</head>
<body>

    <div id="loginlogocontainer">
        <img id="loginlogo" src="img/saw.svg"></img>
    </div>
    <div id="logincontainer">
        <form method="POST" action="setup.php">
            <h1>PSMedia</h1>
            <h2>audio server</h2>
            <div><?php echo $info; ?></div>
            <?php if ($showsetupbtn) { ?>
            <input type="hidden" name="action" value="setup">
            <input type="submit" value="Set up database">
            <?php } ?>
        </form>
    </div>

</body>
</html>