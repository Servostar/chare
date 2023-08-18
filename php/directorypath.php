<?php

include_once "common.php";

$path = file_uri();
$directories = explode("/", trim($path));

echo '<a class="directory-segment" id="root-segment" href="/files"><i class="fa fa-solid fa-house"></i></a>';

$url = "/files";
$path_trace = __get_share_path();

foreach ($directories as $segment) {

    if (empty($segment)) {
        continue;
    }

    $path_trace .= "/" . $segment;
    $url .= "/" . $segment;

    if (is_dir($path_trace)) {
        echo '<span class="directory-separator">/</span><a class="directory-segment" href="'.$url.'">'.$segment.'</a>';
    } else {
        echo '<span class="directory-separator">/</span><a class="directory-segment-invalid">'.$segment.'</a>';
    }
}