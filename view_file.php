<?php
session_start();

// Protect access
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['file'])) {
    die('File not specified.');
}

$file = $_GET['file'];
$fullPath = realpath($file);

// Security check: ensure file is in uploads folder
if (strpos($fullPath, realpath('uploads/')) !== 0 || !file_exists($fullPath)) {
    die('Invalid file.');
}

// Determine MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $fullPath);
finfo_close($finfo);

// Serve file in-browser
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
readfile($fullPath);
exit();