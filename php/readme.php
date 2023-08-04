
<?php

require __DIR__ . '/vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

$text = $GLOBALS["description"];
if (!is_null($text)) {
    $converter = new CommonMarkConverter(['html_input' => 'escape', 'allow_unsafe_links' => false]);
    try {
        $markdown = $converter->convert($text);
        echo '<div id="readme-title">README/description</div><div id="readme">' . $markdown . '</div>';
    } catch (CommonMarkException $e) {
        print("Unable to render markdown: " . $e);
    }
}