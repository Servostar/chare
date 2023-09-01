<?php

include_once 'common.php';

global $explorer;

if (count($explorer->files) != 0 && (count($explorer->files) != 1 || $explorer->files[0] !== '..')) {
    $size = format_bytes($explorer->sizetotal);

    echo '<div id="stats-group">
            <div id="folder-count"><i class="fa fa-regular fa-folder spacing-right"></i>'.$explorer->foldercount.' folder</div>
            <div id="file-count"><i class="fa fa-regular fa-file spacing-right"></i>'.$explorer->filecount.' files</div>
            <div id="file-size-total"><i class="fa fa-solid fa-database spacing-right"></i>'.$size.'</div>
        </div>';
}