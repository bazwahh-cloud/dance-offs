<?php

// -----------------------------
// CONFIGURATION
// -----------------------------
$SECRET = "DjUpload-81!LnA1#-9b4e";  // Replace with your real secret token
$UPLOAD_DIR = __DIR__ . "/tracks/";  // Folder where files will be stored
$PUBLIC_BASE_URL = "https://dance-offs.com/uploads/tracks/"; // Public URL prefix


// -----------------------------
// SECURITY CHECK
// -----------------------------
if (!isset($_POST['token']) || $_POST['token'] !== $SECRET) {
    http_response_code(403);
    die("Forbidden");
}


// -----------------------------
// VALIDATE INPUT
// -----------------------------
if (!isset($_POST['filename']) || !isset($_POST['data'])) {
    http_response_code(400);
    die("Missing fields");
}

$filename = basename($_POST['filename']); // Prevent directory traversal


// -----------------------------
// ENSURE UPLOAD DIRECTORY EXISTS
// -----------------------------
if (!file_exists($UPLOAD_DIR)) {
    mkdir($UPLOAD_DIR, 0775, true);
}


// -----------------------------
// DECODE BASE64 FILE DATA
// -----------------------------
$data = base64_decode($_POST['data']);

if ($data === false) {
    http_response_code(400);
    die("Invalid Base64 data");
}


// -----------------------------
// WRITE FILE TO SERVER
// -----------------------------
$filepath = $UPLOAD_DIR . $filename;

if (file_put_contents($filepath, $data) === false) {
    http_response_code(500);
    die("Failed to write file");
}


// -----------------------------
// RETURN PUBLIC URL
// -----------------------------
$publicUrl = $PUBLIC_BASE_URL . $filename;

echo $publicUrl;
