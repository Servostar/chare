<?php

include_once 'common.php';

$dir = current_dir();
if ($dir === false) {
    return;
}

$files = glob($dir . '/*');
if ($files === false) {
    return;
}

$size = filesize_as_str($dir);

$fileCount = 0;
$folderCount = 0;
foreach($files as $file) {
    if(is_dir($file)) {
        $folderCount ++;
    } else {
        $fileCount ++;
    }
}

echo '<div id="folder-count">'.$folderCount.' folder</div>
    <div id="file-count">'.$fileCount.' files</div>
    <div id="file-size-total">'.$size.' </div>';