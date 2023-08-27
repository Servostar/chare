<?php

include_once 'common.php';
include_once 'explorer.php';

function downloadZIP()
{
    // current absolute path to directory
    global $explorer;

    // Create a new ZipArchive object
    $zip = new ZipArchive();

    // Create a new zip file with a random name
    $zipName = tempnam(sys_get_temp_dir(), 'zip');
    $zip->open($zipName, ZipArchive::CREATE);

    // Add all files in the directory to the zip file
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($explorer->dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($explorer->dir) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Close the zip file
    $zip->close();

    // Prompt the user to download the zip file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($explorer->dir) . '"');
    header('Content-Length: ' . filesize($zipName));
    readfile($zipName);

    // Delete the temporary zip file
    unlink($zipName);
}

// current absolute path to directory
global $explorer;
$explorer = new Explorer();

if (!file_exists($explorer->dir) || is_dir($explorer->dir)) {
    if (isset($_POST['download-zip'])) {
        downloadZIP();
    }

    include_once 'index.php';

} else {
    $filename = basename($explorer->dir);
    $ctype = mime_content_type($explorer->dir);

    // required for IE, otherwise Content-disposition is ignored
    if(ini_get('zlib.output_compression'))
        ini_set('zlib.output_compression', 'Off');

    // Define header information
    header('Content-Description: File Transfer');
    header('Content-Type: '.$ctype);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($explorer->dir));
    // Clear output buffer
    flush();
    // Read the file
    readfile($explorer->dir);
    exit();
}
