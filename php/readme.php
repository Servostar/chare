
<?php

require __DIR__ . '/vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

function render_readme(): void
{
    global $explorer;

    // if there is no readme file don't render anything
    if (empty($explorer->urls["readme"]))
        return;

    /*
     * Configuration for common mark for converting markdown
     * into html
     */
    $config = [
        'html_input' => 'escape',
        'allow_unsafe_links' => false,
        'max_nesting_level' => 5
    ];

    $fileHandle = fopen($explorer->urls["readme"], 'r');
    if ($fileHandle === false) {
        $content = '<div class="error">Could not open README file</div>';
    } else {
        $size = filesize($explorer->urls["readme"]);

        // if the size is zero or cannot be determined
        // $content will be empty and nothing will be rendered
        if (!empty($size) && $size > 0) {
            $text = fread($fileHandle, $size);

            if ($text === false) {
                $content = '<div class="error">Could not read README</div>';
            } else {
                try {
                    // convert markdown into html
                    $converter = new CommonMarkConverter($config);
                    $content = $converter->convert($text);
                } catch (CommonMarkException) {
                    $content = '<div class="error">Could not render README</div>';
                }
            }
        }
        fclose($fileHandle);
    }

    if (!empty($content)) {
        echo '<div id="readme-title">Readme</div><div id="readme">' . $content . '</div>';
    }
}

render_readme();