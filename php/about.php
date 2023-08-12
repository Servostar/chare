<?php

function create_target($path): string
{
    $filename = basename($path);
    $target = $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$filename;
    return filter_var($target, FILTER_SANITIZE_URL);
}

$html = '';

$aboutfile = $GLOBALS["description"];

if ($aboutfile !== "NODESCRIPTION") {

    $fileHandle = fopen($aboutfile, 'r');

    if ($fileHandle !== false) {

        $text = fread($fileHandle, filesize($aboutfile));

        if ($text !== false) {
            fclose($fileHandle);
            $html .= '<div id="description">'.$text.'</div>';
        }
    }
}

if ($GLOBALS["license"] !== "NOLICENSE") {
    $html .= '<a class="about-info license-icon" id="info-license" href="'.create_target($GLOBALS["license"]).'">License</a>';
}

if ($GLOBALS["readme"] !== "NOREADME") {
    $html .= '<a class="about-info readme-icon" id="info-readme" href="'.create_target($GLOBALS["readme"]).'">Readme</a>';
}

if (!empty($html)) {
    echo '<div id="about"><div class="h2">About</div>'.$html.'</div>';
}