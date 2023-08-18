<?php

function create_target($path): string
{
    $filename = basename($path);
    $target = $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$filename;
    return filter_var($target, FILTER_SANITIZE_URL);
}

$html = '';

$aboutfile = $GLOBALS["description"];
$dir = current_dir();

$giturl = exec("git -C $dir config --get remote.origin.url 2>&1");
if (!empty($giturl)) {
    $html .= '<a class="about-info" href="'.$giturl.'">
                <i class="fa fa-brands fa-git-alt fa-width"></i>
                Git Repository
            </a>';
}

if ($GLOBALS["license"] !== "NOLICENSE") {
    $html .= '<a class="about-info" href="'.create_target($GLOBALS["license"]).'">
                <i class="fa fa-regular fa-copyright fa-width"></i>
                License
            </a>';
}

if ($GLOBALS["readme"] !== "NOREADME") {
    $html .= '<a class="about-info" href="'.create_target($GLOBALS["readme"]).'">
                <i class="fa fa-brands fa-readme fa-width"></i>
                Readme
            </a>';
}

if ($GLOBALS["coc"] !== "NOCOC") {
    $html .= '<a class="about-info" href="'.create_target($GLOBALS["coc"]).'">
                <i class="fa fa-regular fa-handshake fa-width"></i>
                Code of Conduct
            </a>';
}

$description = false;
if ($aboutfile !== "NODESCRIPTION") {

    $fileHandle = fopen($aboutfile, 'r');

    if ($fileHandle !== false) {

        $text = fread($fileHandle, filesize($aboutfile));

        if ($text !== false) {
            fclose($fileHandle);
            $description = '<div id="description">'.$text.'</div>';
        }
    }
}

if (!empty($html)) {
    if (empty($description)) {
        $description = '<div class="no-description" id="description">no description provided</div>';
    }

    echo '<div id="about"><div class="h2">About</div>'.$description.$html.'</div>';
}