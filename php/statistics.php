<?php

include_once 'common.php';

global $explorer;

$size = format_bytes($explorer->sizetotal);

echo '<div id="folder-count"><i class="fa fa-regular fa-folder spacing-right"></i>'.$explorer->foldercount.' folder</div>
    <div id="file-count"><i class="fa fa-regular fa-file spacing-right"></i>'.$explorer->filecount.' files</div>
    <div id="file-size-total"><i class="fa fa-solid fa-database spacing-right"></i>'.$size.' </div>';