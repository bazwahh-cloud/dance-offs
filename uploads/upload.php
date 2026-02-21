<?php

// -----------------------------
// CONFIGURATION
// -----------------------------
$UPLOAD_DIR = __DIR__ . "/tracks/";  
$PUBLIC_BASE_URL = "https://upload.dance-offs.com/uploads/tracks/";

// IMPORTANT (outside PHP):
// In /uploads/tracks/.htaccess, add:
//   php_flag engine off
//   Options -Indexes
// This prevents any uploaded PHP from executing.


// -----------------------------
// ENSURE UPLOAD DIRECTORY EXISTS
// -----------------------------
if (!file_exists($UPLOAD_DIR)) {
    mkdir($UPLOAD_DIR, 0775, true);
}


// -----------------------------
// VALIDATE FILE INPUT
// -----------------------------
if (!isset($_FILES['file'])) {
    http_response_code(400);
    die("No file uploaded");
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    die("Upload error: " . $file['error']);
}


// -----------------------------
// LIMIT FILE SIZE (30MB MAX)
// -----------------------------
$maxSize = 30 * 1024 * 1024; // 30MB in bytes

if ($file['size'] > $maxSize) {
    http_response_code(400);
    die("File too large. Maximum size is 30MB.");
}


// -----------------------------
// VALIDATE FILE TYPE (MP3 ONLY)
// - Use finfo to inspect actual content
// -----------------------------
$finfo = finfo_open(FILEINFO_MIME_TYPE);
if ($finfo === false) {
    http_response_code(500);
    die("Server error: cannot validate file type.");
}

$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

// Common MP3 MIME type
if ($mime !== 'audio/mpeg') {
    http_response_code(400);
    die("Invalid file type. MP3 only.");
}


// -----------------------------
// SANITIZE FILENAME + EXTENSION
// -----------------------------
$originalName = basename($file['name']);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if ($extension !== "mp3") {
    http_response_code(400);
    die("Only MP3 files allowed.");
}

// We don't trust the original name for storage, only for logging.
// Force a safe, unique .mp3 filename.
$finalName = time() . "-" . uniqid() . ".mp3";
$targetPath = $UPLOAD_DIR . $finalName;


// -----------------------------
// MOVE FILE TO TRACKS FOLDER
// -----------------------------
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    die("Failed to save file");
}


// -----------------------------
// RETURN PUBLIC URL
// -----------------------------
echo $PUBLIC_BASE_URL . $finalName;
