<?php

/**
 * Get the path to the directory which contents are to be publicly shared.
 * @return string
 */
function __get_share_path(): string
{
    $env_path = getenv('SHARE_PATH');

    // if the path to the directory to share is invalid
    // we will use the default share path
    if (empty($env_path)) {
        $env_path = "/var/share";
    }

    return $env_path;
}

/**
 * Get the absolute path that is to be viewed as share.
 * Returns false if the path is invalid or the folder does not exist or
 * the path is outside the directory to share.
 * @return false|string
 */
function current_dir(): false|string
{
    $share_path = __get_share_path();
    $rel_path = $share_path.DIRECTORY_SEPARATOR.preg_replace("/^\/files/", '', $_SERVER['REQUEST_URI']);

    return realpath($rel_path);
}

function format_bytes($bytes, $decimals = 2): string
{
    $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
}

function filesize_as_str($file): string
{
    $size = filesize($file);
    if ($size !== false) {
        return format_bytes($size);
    }
    return "unknown";
}

function pretty_datetime_diff($past): string
{
    $now = new DateTime();
    $diff = $now->diff(new DateTime($past));

    if ($diff->y > 0) {
        return $diff->y." year(s) ago";
    }
    if ($diff->m > 0) {
        return $diff->m." month(s) ago";
    }
    if ($diff->d > 0) {
        return $diff->d." day(s) ago";
    }
    if ($diff->i > 0) {
        return $diff->i." minutes(s) ago";
    }
    return "seconds ago";
}

function read_file_or_default($file, $default): string
{
    $text = $default;
    $fileHandle = fopen($file, 'r');
    if ($fileHandle !== false) {
        $text = fread($fileHandle, filesize($file));
        if ($text === false) {
            $text = $default;
        }
        fclose($fileHandle);
    }
    return $text;
}