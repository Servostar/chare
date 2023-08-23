<?php

function amend_uri($amendment): string
{
    return $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$amendment;
}

$html = '';

$dir = current_dir();

$giturl = exec("git -C $dir config --get remote.origin.url 2>&1");
if (!empty($giturl)) {
    $html .= '<a class="about-info" href="'.$giturl.'">
                <i class="fa fa-brands fa-git-alt fa-width"></i>
                Git Repository
            </a>';
}

if (!empty($GLOBALS["license"])) {
    $html .= '<a class="about-info" href="'.create_link_from_uri(amend_uri($GLOBALS["license"])).'">
                <i class="fa fa-regular fa-copyright fa-width"></i>
                License
            </a>';
}

if (!empty($GLOBALS["readme"])) {
    $html .= '<a class="about-info" href="'.create_link_from_uri(amend_uri($GLOBALS["readme"])).'">
                <i class="fa fa-brands fa-readme fa-width"></i>
                Readme
            </a>';
}

if (!empty($GLOBALS["coc"])) {
    $html .= '<a class="about-info" href="'.create_link_from_uri(amend_uri($GLOBALS["coc"])).'">
                <i class="fa fa-regular fa-handshake fa-width"></i>
                Code of Conduct
            </a>';
}

$description = false;
$aboutfile = $GLOBALS["description"];
if (!empty($aboutfile)) {

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