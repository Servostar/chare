<?php

include_once 'common.php';

function create_file_html($file): void
{
    $filesize = filesize_as_str($file);
    $fileName = basename($file);

    $lastAccessTime = get_last_accesstime($file);

    $target = $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$fileName;
    $sanitized_uri = filter_var($target, FILTER_SANITIZE_URL);

    if(is_dir($file)) {
        format_file_entry_html($sanitized_uri, $fileName, $filesize, $lastAccessTime, "folder-icon");
    } else {
        format_file_entry_html($sanitized_uri, $fileName, $filesize, $lastAccessTime, "file-icon");
    }
}

function get_last_accesstime($file): string
{
    $lastAccessTime = filemtime($file);
    if ($lastAccessTime === false || $lastAccessTime === 0) {
        $lastAccessTime = filectime($file);
        if ($lastAccessTime === false) {
            return "unknown";
        }
    }
    $lastAccessTime = date('Y-m-d H:i:s', $lastAccessTime);
    return pretty_datetime_diff($lastAccessTime);
}

function format_file_entry_html($target_path, $filename, $filesize, $editdate, $iconclass): void
{
    echo '<a class="folder-view-item '.$iconclass.'" href="'.$target_path.'">
                            <div class="file-name">' . $filename . '</div>
                            <div class="file-size">' . $filesize . '</div>
                            <div class="file-added">' . $editdate . '</div>
                        </a>';
}

$dir = current_dir();
if ($dir === false) {
    return;
}

$GLOBALS["description"] = "NODESCRIPTION";
$GLOBALS["readme"] = "NOREADME";
$GLOBALS["license"] = "NOLICENSE";

$entries = scandir($dir);
sort($entries);
usort($entries, function($a, $b) use ($dir) {
    $aIsDir = is_dir($dir . '/' . $a);
    $bIsDir = is_dir($dir . '/' . $b);
    if ($aIsDir == $bIsDir) {
        return strcasecmp($a, $b);
    } else {
        return $aIsDir ? -1 : 1;
    }
});
foreach ($entries as $entry) {

    if ($entry == '.') {
        continue;
    }

    $file = current_dir().DIRECTORY_SEPARATOR.$entry;

    if (preg_match('/^readme(\.md)?$/i', $entry)) {
        $GLOBALS["readme"] = $file;
    }

    if (preg_match('/^license(\.txt)?$/i', $entry)) {
        $GLOBALS["license"] = $file;
    }

    if (preg_match('/^about(\.txt)?$/i', $entry)) {
        $GLOBALS["description"] = $file;
    }

    create_file_html($file);
}