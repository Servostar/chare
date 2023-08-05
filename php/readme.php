
<?php

require __DIR__ . '/vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

$readmefile = $GLOBALS["readme"];

if (is_null($readmefile) || $readmefile == "NOREADME") {
    return;
}

$fileHandle = fopen($readmefile, 'r');
if ($fileHandle === false) {
    echo '<div class="error">Could not open README</div>';
    return;
}

$text = fread($fileHandle, filesize($readmefile));
if ($text === false) {
    echo '<div class="error">Could not read README</div>';
    return;
}
fclose($fileHandle);

$converter = new CommonMarkConverter(['html_input' => 'escape', 'allow_unsafe_links' => false]);
try {
    $markdown = $converter->convert($text);
    echo '<div id="readme-title">Readme</div><div id="readme">' . $markdown . '</div>';
} catch (CommonMarkException $e) {
    echo '<div class="error">Could not render README</div>';
}