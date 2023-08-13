<?php

include_once "common.php";

$path = file_uri();
$directories = explode("/", trim($path));

echo '<a class="directory-segment" id="root-segment" href="/files">Location:</a>';

$url = "/files";

foreach ($directories as $segment) {

    if (empty($segment)) {
        continue;
    }

    $url .= "/" . $segment;

    echo '<span class="directory-separator">/</span><a class="directory-segment" href="'.$url.'">'.$segment.'</a>';
}