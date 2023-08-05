<?php

function create_target($path): string
{
    $filename = basename($path);
    $target = $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$filename;
    return filter_var($target, FILTER_SANITIZE_URL);
}

$aboutfile = $GLOBALS["description"];

if ($aboutfile !== "NODESCRIPTION") {

    $fileHandle = fopen($aboutfile, 'r');

    if ($fileHandle !== false) {

        $text = fread($fileHandle, filesize($aboutfile));

        if ($text !== false) {
            fclose($fileHandle);
            echo '<div id="description">'.$text.'</div>';
        }
    }
}

if ($GLOBALS["license"] !== "NOLICENSE") {
    echo '<a class="about-info" id="info-license" href="'.create_target($GLOBALS["license"]).'">License</a>';
}

if ($GLOBALS["readme"] !== "NOREADME") {
    echo '<a class="about-info" id="info-readme" href="'.create_target($GLOBALS["readme"]).'">Readme</a>';
}