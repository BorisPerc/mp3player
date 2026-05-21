<?php
/**
 * Statistics View - PHP 8.2+ Compatible
 */

if (!isset($view)) {
    die("Access this page using library.php with the view parameter!");
}

require_once __DIR__ . '/../../global.php';
require_once __DIR__ . '/../../database.php';

$stats = [
    'tracks' => 0,
    'tracks_cover' => 0,
    'tracks_playtime' => 0,
    'tracks_length' => 0,
    'albums' => 0,
    'artists' => 0,
    'playlists' => 0,
];

$sql = "SELECT 'tracks' AS key_name, COUNT(*) AS value FROM track "
     . "UNION SELECT 'tracks_cover', COUNT(cover) FROM track WHERE cover IS NOT NULL "
     . "UNION SELECT 'tracks_playtime', SUM(duration) FROM track "
     . "UNION SELECT 'tracks_length', SUM(length) FROM track "
     . "UNION SELECT 'albums', COUNT(*) FROM album "
     . "UNION SELECT 'artists', COUNT(*) FROM artist "
     . "UNION SELECT 'playlists', COUNT(*) FROM playlist";

$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $key = $row['key_name'] ?? '';
        $value = (int)($row['value'] ?? 0);
        if (isset($stats[$key])) {
            $stats[$key] = $value;
        }
    }
}

function formatBytesDecimal(int $bytes, int $precision = 2): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1000));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1000, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function formatBytesBinary(int $bytes, int $precision = 2): string {
    $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function formatSeconds(int $seconds): string {
    $days = intdiv($seconds, 86400);
    $hours = intdiv($seconds % 86400, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    $secs = $seconds % 60;
    return "$days days, $hours hours, $minutes minutes, $secs seconds";
}
?>

<table class="statistics">
    <tr>
        <th>Library Size:</th>
        <td>
            <?php echo formatBytesBinary($stats['tracks_length']) . ' / ' . formatBytesDecimal($stats['tracks_length']); ?>
            <br>
            <?php echo '~ ' . number_format((int)ceil($stats['tracks_length'] / 1440000), 0, ',', '.') . ' floppy disks (3,5")'; ?>
            <br>
            <?php echo '(' . number_format($stats['tracks_length'], 0, ',', '.') . ' bytes total)'; ?>
        </td>
    </tr>
    <tr>
        <th>Overall Playtime:</th>
        <td>
            <?php echo formatSeconds($stats['tracks_playtime']); ?>
            <br>
            <?php echo '~ ' . number_format((int)ceil($stats['tracks_playtime'] / 60 / 120), 0, ',', '.') . ' compact cassettes (C120)'; ?>
            <br>
            <?php echo '(' . number_format($stats['tracks_playtime'], 0, ',', '.') . ' seconds total)'; ?>
        </td>
    </tr>
    <tr>
        <th>Tracks:</th>
        <td><?php echo number_format($stats['tracks']); ?></td>
    </tr>
    <tr>
        <th>↳ with cover art:</th>
        <td><?php echo number_format($stats['tracks_cover']); ?></td>
    </tr>
    <tr>
        <th>Albums:</th>
        <td><?php echo number_format($stats['albums']); ?></td>
    </tr>
    <tr>
        <th>Artists:</th>
        <td><?php echo number_format($stats['artists']); ?></td>
    </tr>
    <tr>
        <th>Playlists:</th>
        <td><?php echo number_format($stats['playlists']); ?></td>
    </tr>
</table>