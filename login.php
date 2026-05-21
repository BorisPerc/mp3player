<?php
/**
 * Login Handler - PHP 8.2+ Compatible
 */

require_once 'config.php';
require_once 'browser.php';
require_once 'global.php';

// Don't require session.php yet - we handle that manually here
ini_set('session.gc_maxlifetime', SESSION_LENGTH);
session_set_cookie_params([
    'lifetime' => SESSION_LENGTH,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once 'database.php';

// Check if setup needed
if (!IsAlreadyEstablished($mysqli)) {
    header('Location: setup.php');
    exit();
}

$info = "";

// Handle login
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (correctCredentials((string)$username, (string)$password)) {
        $_SESSION['username'] = $username;
        $_SESSION['logintype'] = $username;
        header('Location: index.php');
        exit();
    } else {
        $info = "Invalid username or password.";
    }
}

// Handle logout
if ((isset($_GET['logout']) || isset($_POST['logout']))) {
    session_unset();
    session_destroy();
    $info = "Successfully logged out.";
    session_start();
}

// Handle password change
if (isset($_POST['newpassword'])) {
    $newpassword = $_POST['newpassword'] ?? '';
    $username = $_POST['username'] ?? '';
    
    $hashed_password = !empty($newpassword) ? password_hash($newpassword, PASSWORD_DEFAULT) : '';
    
    $identifier = getPasswordIdentifier((string)$username);
    if ($identifier) {
        $sql = "UPDATE setting SET value = ? WHERE identifier = ?";
        $statement = $mysqli->prepare($sql);
        if ($statement) {
            $statement->bind_param('ss', $hashed_password, $identifier);
            if ($statement->execute()) {
                $info = "Password changed successfully.";
            } else {
                $info = "Error changing password.";
            }
            $statement->close();
        }
    } else {
        $info = "Invalid user.";
    }
}

/**
 * Verify user credentials
 */
function correctCredentials(string $username, string $password): bool {
    global $mysqli;
    
    $identifier = getPasswordIdentifier($username);
    if (!$identifier) {
        return false;
    }
    
    $sql = "SELECT value FROM setting WHERE identifier = ? LIMIT 1";
    $statement = $mysqli->prepare($sql);
    
    if (!$statement) {
        return false;
    }
    
    $statement->bind_param('s', $identifier);
    if (!$statement->execute()) {
        $statement->close();
        return false;
    }
    
    $result = $statement->get_result();
    $password_hash = '';
    
    if ($row = $result->fetch_assoc()) {
        $password_hash = $row['value'] ?? '';
    }
    
    $statement->close();
    
    // Allow empty password if no password set
    if (empty($password_hash) && empty($password)) {
        return true;
    }
    
    return password_verify($password, $password_hash);
}

/**
 * Get password identifier from username
 */
function getPasswordIdentifier(string $username): ?string {
    $mapping = [
        '1' => 'password_user',
        '2' => 'password_remoteplayer',
        '3' => 'password_voter',
    ];
    
    return $mapping[$username] ?? null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audio Server - Login</title>
    <meta charset="utf-8"/>
    <script type="text/javascript" src="js/global.js"></script>
    <link href="css/global.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat+Subrayada|Open+Sans+Condensed:300" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui">
    <link rel="icon" type="image/png" href="img/saw.png">
    <link rel="apple-touch-icon" href="img/saw.png">
    <link rel="shortcut icon" href="img/saw.png">
    <link rel="apple-touch-startup-image" href="img/saw.png">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="PSMedia">
</head>
<body>

    <div id="loginlogocontainer">
        <img id="loginlogo" src="img/saw.svg"></img>
    </div>
    <div id="logincontainer">
        <?php if (!isset($_SESSION['username'])) { ?>
        <form method="POST" action="login.php">
            <h1>PSMedia</h1>
            <h2>audio server</h2>
            <div><?php echo htmlspecialchars($info); ?></div>

            <?php if (getBrowser1() != "InternetExplorer") { ?>
            <div id="username" class="inputwithimg">
                <select name="username" autofocus="true" title="select login role">
                    <optgroup label="log in as...">
                        <option value='1'>Local Player</option>
                        <option value='2'>Remote Player</option>
                        <option value='3'>Vote for Songs (Party Mode)</option>
                    </optgroup>
                </select>
                <img src="img/username.svg">
            </div>
            <div id="password" class="inputwithimg">
                <input type="password" name="password" title="enter password"><img src="img/password.svg">
            </div>
            <span id="by">&copy; Audio Server</span>
            <input type="submit" value="Login">
            <?php } else { ?>
                <div>IE is not supported<br>and we're not sorry 'bout that.</div>
            <?php } ?>
        </form>
        <?php } else { ?>
        <form method="POST" action="login.php">
            <h1>PSMedia</h1>
            <h2>audio server</h2>
            <div><?php echo htmlspecialchars($info); ?></div>

            <?php if (isset($_GET['changepassword']) && $_GET['changepassword'] == 1) { ?>
            <div id="username" class="inputwithimg">
                <select name="username" autofocus="true" title="select login role">
                    <optgroup label="change password for...">
                        <option value='1'>Local Player</option>
                        <option value='2'>Remote Player</option>
                        <option value='3'>Vote for Songs (Party Mode)</option>
                    </optgroup>
                </select>
                <img src="img/username.svg">
            </div>
            <div id="password" class="inputwithimg">
                <input type="password" name="newpassword" title="enter new password"><img src="img/password.svg">
            </div>
            <span id="by">&copy; Audio Server</span>
            <input type="submit" value="Change password">
            <?php } else { ?>
            <input type="hidden" name="logout" value="1"></input>
            <span id="by">&copy; Audio Server</span>
            <input type="submit" value="Logout">
            <?php } ?>
        </form>
        <?php } ?>
    </div>

</body>
</html>