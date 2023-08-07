<?php

include_once 'common.php';

$dir = current_dir();

$GLOBALS["request"] = $dir;

// if a file and not a directory was requested, download the file and terminate
// if a directory was requested, build a html file as response
if (!file_exists($dir) || is_dir($dir)) {
    include_once 'index.php';
} else {
    // Define header information
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($dir) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($dir));
    // Clear output buffer
    flush();
    // Read the file
    readfile($dir);
}
