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

echo '<div id="folder-count"><i class="fa fa-regular fa-folder"></i>&nbsp;'.$folderCount.' folder</div>
    <div id="file-count"><i class="fa fa-regular fa-file"></i>&nbsp;'.$fileCount.' files</div>
    <div id="file-size-total"><i class="fa fa-solid fa-database"></i>&nbsp;'.$size.' </div>';