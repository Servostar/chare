<?php

include_once 'common.php';

$dir = current_dir();

$GLOBALS["request"] = $dir;

if (!file_exists($dir) || is_dir($dir)) {
    include_once 'index.php';
} else {
    $ctype = "text/plain"; //mime_content_type($dir);

    // required for IE, otherwise Content-disposition is ignored
    if(ini_get('zlib.output_compression'))
        ini_set('zlib.output_compression', 'Off');

    $filename = basename($dir);

    // Define header information
    header('Content-Description: File Transfer');
    header('Content-Type: '.$ctype);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($dir));
    // Clear output buffer
    flush();
    // Read the file
    readfile($dir);
    exit();
}
