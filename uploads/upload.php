<?php

// -----------------------------
// CONFIGURATION
// -----------------------------
$UPLOAD_DIR = __DIR__ . "/tracks/";  
$PUBLIC_BASE_URL = "https://upload.dance-offs.com/uploads/tracks/";


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
// VALIDATE FILE TYPE (MP3 ONLY)
// -----------------------------
$allowedTypes = ['audio/mpeg', 'audio/mp3', 'application/octet-stream'];

if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    die("Invalid file type. MP3 only.");
}


// -----------------------------
// SANITIZE FILENAME
// -----------------------------
$originalName = basename($file['name']);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if ($extension !== "mp3") {
    http_response_code(400);
    die("Only MP3 files allowed.");
}

// Create a unique filename
$finalName = time() . "-" . preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $originalName);
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
