<?php
/**
 * Browser Detection - PHP 8.2+ Compatible
 */

function getOS1(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $os_map = [
        'Android' => 'Android',
        'BlackBerry' => 'BlackBerry',
        'iPhone' => 'iPhone',
        'iPad' => 'iPhone',
        'Palm' => 'Palm',
        'Linux' => 'Linux',
        'Macintosh' => 'Macintosh',
        'Windows' => 'Windows',
    ];
    
    foreach ($os_map as $search => $result) {
        if (strpos($ua, $search) !== false) {
            return $result;
        }
    }
    
    return 'Unknown';
}

function getBrowser1(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    if (strpos($ua, 'Chrome') !== false) {
        return 'Chrome';
    } elseif (strpos($ua, 'Firefox') !== false) {
        return 'Firefox';
    } elseif (strpos($ua, 'MSIE') !== false) {
        return 'InternetExplorer';
    } elseif (preg_match("/\bOpera\b/i", $ua)) {
        return 'Opera';
    } elseif (strpos($ua, 'Safari') !== false) {
        return 'Safari';
    }
    
    return 'Unknown';
}
?>