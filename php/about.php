<?php

function amend_uri($amendment): string
{
    return $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$amendment;
}

$html = '';

global $explorer;

$output = null;
$return_var = null;
exec("/usr/bin/git -C $explorer->dir config --get remote.origin.url 2>&1", $output, $return_var);
$giturl = trim($output[0]);
if ($return_var === 0 && !empty($giturl)) {
    $html .= '<a class="about-info" href="'.$giturl.'">
                <i class="fa fa-brands fa-git-alt fa-width"></i>
                Git Repository
            </a>';
}

if (!empty($explorer->urls["license"])) {
    $html .= '<a class="about-info" href="'.create_link_from_uri(amend_uri($explorer->urls["license"])).'">
                <i class="fa fa-regular fa-copyright fa-width"></i>
                License
            </a>';
}

if (!empty($explorer->urls["readme"])) {
    $html .= '<a class="about-info" href="'.create_link_from_uri(amend_uri($explorer->urls["readme"])).'">
                <i class="fa fa-brands fa-readme fa-width"></i>
                Readme
            </a>';
}

if (!empty($explorer->urls["codeofconduct"])) {
    $html .= '<a class="about-info" href="'.create_link_from_uri(amend_uri($explorer->urls["codeofconduct"])).'">
                <i class="fa fa-regular fa-handshake fa-width"></i>
                Code of Conduct
            </a>';
}

$aboutfile = $explorer->urls["description"];
$description = '<div id="description">'.read_file_or_default($aboutfile, "no description provided").'</div>';

if (!empty($html)) {
    echo '<div id="about"><div class="h2">About</div>'.$description.$html.'</div>';
}