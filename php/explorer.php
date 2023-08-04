<?php

include_once 'common.php';

function create_file_html($file): void
{
    $filesize = filesize_as_str($file);
    $fileName = basename($file);

    $lastAccessTime = fileatime($file);
    $lastAccessTime = date('Y-m-d H:i:s', $lastAccessTime);

    if(is_dir($file)) {
        format_file_entry_html($file, $fileName, $filesize, $lastAccessTime, "folder-icon");
    } else {
        format_file_entry_html($file, $fileName, $filesize, $lastAccessTime, "file-icon");
    }
}

function format_file_entry_html($target_path, $filename, $filesize, $editdate, $iconclass): void
{
    echo '<a class="folder-view-item '.$iconclass.'" href="'.$target_path.'">
                            <div class="file-name">' . $filename . '</div>
                            <div class="file-size">' . $filesize . '</div>
                            <div class="file-added">' . $editdate . '</div>
                        </a>';
}

$dir = current_dir();
if ($dir === false) {
    return;
}

$entries = scandir($dir);
sort($entries);
usort($entries, function($a, $b) use ($dir) {
    $aIsDir = is_dir($dir . '/' . $a);
    $bIsDir = is_dir($dir . '/' . $b);
    if ($aIsDir == $bIsDir) {
        return strcasecmp($a, $b);
    } else {
        return $aIsDir ? -1 : 1;
    }
});
foreach ($entries as $entry) {

    if (basename($entry) == "README.md") {
        $fileHandle = fopen($entry, 'r');
        $GLOBALS["description"] = fread($fileHandle, filesize($entry));
        fclose($fileHandle);
    }

    create_file_html($entry);
}