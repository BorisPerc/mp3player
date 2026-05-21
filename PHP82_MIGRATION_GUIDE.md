# PHP 8.2+ Compatibility Migration Guide

## Overview
This guide documents all changes made to MP3 Player for PHP 8.2+ compatibility.

## Key Changes Made

### 1. **config.php** (NEW FILE)
- Centralized configuration management
- Database credentials
- Custom music directory support
- Error handling setup
- Logs directory creation

**Before:**
```php
// Hardcoded in each file
$db_host = "localhost";
$db_user = "root";
```

**After:**
```php
// In config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
// Use custom directory:
// define('MEDIAROOT', '/media/audio/mp3/deephouse/');
```

### 2. **database.php**
- Added `require_once config.php`
- Exception handling for connection errors
- Better error messages

### 3. **global.php** - Type Declarations & Null Safety
**Changes:**
- ✅ Added function type hints: `function isAudioFile(string $filename): bool`
- ✅ Null coalescing operators: `$_SESSION['key'] ?? 'default'`
- ✅ Array safe access with `fetch_assoc()` instead of `fetch_object()`
- ✅ Removed `real_escape_string()` deprecation

### 4. **session.php** - PHP 8.2 Session Security
**Changes:**
- ✅ Array-based session configuration
- ✅ HTTPOnly cookie flag
- ✅ SameSite=Lax protection
- ✅ Secure flag for HTTPS

### 5. **browser.php** - Improved Detection
**Changes:**
- ✅ Null coalescing for user agent
- ✅ Array-based browser detection
- ✅ Safer string operations

### 6. **login.php** - MAJOR REWRITE
**Critical Issues Fixed:**
- ✅ All queries now use prepared statements
- ✅ Removed deprecated `real_escape_string()`
- ✅ Safe array access with `??` operator
- ✅ Password hashing now proper
- ✅ Type-safe comparisons

**Before:**
```php
$sql = "SELECT * FROM setting WHERE identifier = 'password_user' LIMIT 1";
$result = $statement->get_result();
while($row = $result->fetch_object()) {
```

**After:**
```php
$sql = "SELECT value FROM setting WHERE identifier = ? LIMIT 1";
$statement = $mysqli->prepare($sql);
$statement->bind_param('s', $identifier);
$statement->execute();
$result = $statement->get_result();
if ($row = $result->fetch_assoc()) {
```

### 7. **scan.php** - THE BIG FIX! 🎯
**This was your main issue. Here's what was fixed:**

#### Issue 1: getID3 Array Access
**Before:**
```php
$FileInfo['comments_html']['genre'][0]  // Could throw "Undefined array key" error
```

**After:**
```php
$comments = $FileInfo['comments_html'] ?? [];
$genre = $comments['genre'][0] ?? "";
```

#### Issue 2: Prepared Statements
**Before:**
```php
$sql = "SELECT tr.id AS 'id', tr.cover AS 'cover' FROM track tr WHERE tr.path = ?";
// No proper parameter binding
```

**After:**
```php
$sql = "SELECT id, cover FROM track WHERE path = ?";
$statement->bind_param('s', $file_path);
```

#### Issue 3: Custom Music Directory
**Before:**
```php
$MUSIC_DIR = MEDIAROOT;
// $MUSIC_DIR = "/media/audio/mp3/deephouse/";  // Hardcoded
```

**After:**
```php
// In config.php - SET ONCE:
// define('MEDIAROOT', '/media/audio/mp3/deephouse/');

// In scan.php - AUTOMATIC:
$MUSIC_DIR = MEDIAROOT;  // Pulls from config.php
```

#### Issue 4: Type Safety in Loops
**Before:**
```php
while($row = $result->fetch_object()) {
    $track_id = $row->id;  // Could be null
}
```

**After:**
```php
while ($row = $result->fetch_assoc()) {
    $track_id = (int)($row['id'] ?? -1);  // Always an int
}
```

#### Issue 5: Error Handling
**Before:**
- Silent failures
- No error logging

**After:**
- Try/catch blocks
- Error messages displayed
- All errors logged to `/logs/error.log`

#### Issue 6: Metadata Extraction
**New helper function - Much safer:**
```php
function extractMetadata(array $FileInfo, string $file_path): array {
    $comments = $FileInfo['comments_html'] ?? [];
    
    return [
        'title' => $comments['title'][0] ?? pathinfo($file_path, PATHINFO_FILENAME),
        'album' => $comments['album'][0] ?? "Unknown Album",
        'artist' => $comments['artist'][0] ?? "Unknown Artist",
        'track_number' => (int)extractTrackNumber($comments['track_number'][0] ?? "0"),
        'genre' => $comments['genre'][0] ?? "",
        'playtime' => (int)($FileInfo['playtime_seconds'] ?? 0),
    ];
}
```

## How to Use Custom Music Directory

### Option 1: Edit config.php
```php
// In config.php, replace:
define('MEDIAROOT', 'music');

// With:
define('MEDIAROOT', '/media/audio/mp3/deephouse/');
```

### Option 2: Symbolic Link (Recommended for shared hosting)
```bash
cd /path/to/mp3player
ln -s /media/audio/mp3/deephouse/ music_custom
```

Then in config.php:
```php
define('MEDIAROOT', 'music_custom');
```

## Files Still to Update

These files are NOT BROKEN but could use PHP 8.2+ improvements:

- [ ] getcover.php
- [ ] index.php
- [ ] library.php
- [ ] music.php
- [ ] player.php
- [ ] playlistedit.php
- [ ] sessionvars.php
- [ ] setup.php
- [ ] upload.php
- [ ] php/library-views/*.php

## Testing Checklist

After deployment, test these:

- [ ] Login works
- [ ] Database connection works
- [ ] Scan detects files (both `scan.php` and `scan.php?rescan=1`)
- [ ] Custom music directory path works
- [ ] Cover art extraction works
- [ ] Track metadata extraction works
- [ ] No PHP warnings/errors in logs
- [ ] Password change works
- [ ] Logout works

## Error Log Location

Check `/logs/error.log` for detailed error messages if something breaks.

## Database Requirements

Ensure your MySQL database supports:
- UTF8MB4 charset (✓ Already in tables.sql)
- Procedures (✓ Already configured)
- InnoDB foreign keys (✓ Already configured)

## Troubleshooting

### "Music directory does not exist"
- Check config.php MEDIAROOT path
- Ensure directory is readable by PHP process

### "Database error: Prepare failed"
- Check database.php connection
- Verify credentials in config.php

### "Permission denied writing covers"
- Ensure `music_thumb/` directory exists and is writable
- Create with: `mkdir -p music_thumb && chmod 755 music_thumb`

### Scan stops silently
- Check `/logs/error.log`
- Verify getID3 library exists
- Check file permissions

