<?php

include_once 'common.php';

function create_file_html($file): void
{
    $filesize = filesize_as_str($file);
    $filename = basename($file);

    $lastAccessTime = get_last_accesstime($file);

    $target = $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$filename;
    $url = filter_var($target, FILTER_SANITIZE_URL);
    if (!str_ends_with($url, "/")) {
        $url .= "/";
    }
    $url .= $filename;

    $sanitized_uri = filter_var($url, FILTER_SANITIZE_URL);

    if (is_dir($file)) {
        format_file_entry_html($sanitized_uri, $fileName, $filesize, $lastAccessTime, "fa fa-solid fa-folder");
    } else {
        format_file_entry_html($sanitized_uri, $fileName, $filesize, $lastAccessTime, "fa fa-regular fa-file");
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
    echo '<a class="folder-view-item" href="' . $target_path . '">
                            <div class="file-name"><i class="file-icon '.$iconclass.'"></i>' . $filename . '</div>
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

foreach ($entries as $entry) {

    if (in_array($entry, $ignore)) {
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

    create_file_html($file);
}

if (count($entries) <= 2) {
    echo "<div class='information'>directory is empty</div>";
    return;
}