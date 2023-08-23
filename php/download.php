<?php

include_once 'common.php';

$dir = current_dir();

$GLOBALS["request"] = $dir;

function downloadZIP()
{
    $dirPath = current_dir(); // Replace with the path to your directory

    // Create a new ZipArchive object
    $zip = new ZipArchive();

    // Create a new zip file with a random name
    $zipName = tempnam(sys_get_temp_dir(), 'zip');
    $zip->open($zipName, ZipArchive::CREATE);

    // Add all files in the directory to the zip file
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($dirPath) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Close the zip file
    $zip->close();

    // Prompt the user to download the zip file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($dirPath) . '"');
    header('Content-Length: ' . filesize($zipName));
    readfile($zipName);

    // Delete the temporary zip file
    unlink($zipName);
}

if (!file_exists($dir) || is_dir($dir)) {
    if(isset($_POST['download-zip'])) {
        downloadZIP();
    }
    include_once 'index.php';

} else {
    $ctype = mime_content_type($dir);

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
