<?php
/**
 * Router - PHP 8.2+ Compatible
 */

require_once 'config.php';
require_once 'session.php';

$logintype = $_SESSION['logintype'] ?? 1;

switch ((string)$logintype) {
    case '1':
        header('Location: player.php');
        break;
    case '2':
        header('Location: remoteplayer.php');
        break;
    case '3':
        header('Location: vote.php');
        break;
    default:
        header('Location: player.php');
}
exit();
?>