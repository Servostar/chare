<?php

include_once 'common.php';

function create_file_html($file): void
{
    $filesize = filesize_as_str($file);
    $filename = basename($file);

    $lastAccessTime = get_last_accesstime($file);

    $uri = preg_replace("/\/+/", '/', $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$filename);
    $url = create_link_from_uri($uri);

    if (is_dir($file)) {
        format_file_entry_html($url, $filename, $filesize, $lastAccessTime, "fa fa-solid fa-folder-blank", "accent");
    } else {
        format_file_entry_html($url, $filename, $filesize, $lastAccessTime, "fa fa-regular fa-file", "");
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

function format_file_entry_html($target_path, $filename, $filesize, $editdate, $iconclass, $colorclass): void
{
    echo '<a class="folder-view-item" href="' . $target_path . '">
                            <div class="file-name"><i class="file-icon '.$iconclass." ".$colorclass.'"></i>' . $filename . '</div>
                            <div class="file-added">' . $editdate . '</div>
                            <div class="file-size">' . $filesize . '</div>
                        </a>';
}

$dir = current_dir();
if ($dir === false) {
    return;
}

$GLOBALS["description"] = "NODESCRIPTION";
$GLOBALS["readme"] = "NOREADME";
$GLOBALS["license"] = "NOLICENSE";
$GLOBALS["coc"] = "NOCOC";

$ignore = explode("\n", read_file_or_default("/srv/config/.ignore", "."));

$entries = scandir($dir);
sort($entries);
usort($entries, function ($a, $b) use ($dir) {
    $aIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $a);
    $bIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $b);
    if ($aIsDir == $bIsDir) {
        return strcasecmp($a, $b);
    } else {
        return $aIsDir ? -1 : 1;
    }
});

$is_root = is_root();

foreach ($entries as $entry) {

    if (in_array($entry, $ignore)) {
        continue;
    }

    if ($is_root && $entry == "..") {
        continue;
    }

    $file = current_dir() . DIRECTORY_SEPARATOR . $entry;

    if (preg_match('/^readme(\.md)?$/i', $entry)) {
        $GLOBALS["readme"] = $file;
    }

    if (preg_match('/^license(\.txt)?$/i', $entry)) {
        $GLOBALS["license"] = $file;
    }

    if (preg_match('/^about(\.txt)?$/i', $entry)) {
        $GLOBALS["description"] = $file;
    }

    if (preg_match('/^code_?of_?conduct(\.txt|\.md)?$/i', $entry)) {
        $GLOBALS["coc"] = $file;
    }

    create_file_html($file);
}

if (count($entries) <= 2) {
    echo '<div class="information">no files in here</div>';
    return;
}