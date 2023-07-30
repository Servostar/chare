<?php

/**
 * Get the path to the directory which contents are to be publicly shared.
 * @return string
 */
function __get_share_path(): string
{
    $env_path = $_ENV['SHARE_PATH'];

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
function get_current_dir(): false|string
{
    $share_path = __get_share_path();
    $rel_path = $share_path.DIRECTORY_SEPARATOR.$_SERVER['REQUEST_URI'];
    $abs_path = realpath($rel_path);

    // check if absolute path could be resolved
    if ($abs_path !== false) {
    }

    return $abs_path;
}

function get_file_change_time($file)
{
    $timestamp = filectime($file);
    $current_time = new DateTime();
    $passed_time = new DateTime(date('Y-m-d H:i:s', $timestamp));
    $interval = $current_time->diff($passed_time);

    if ($interval->y > 0) {
        $nice_interval = $interval->y . " year(s) ago";
    } else if ($interval->days > 0) {
        $nice_interval = $interval->days . " day(s) ago";
    } else if ($interval->h > 0) {
        $nice_interval = $interval->h . " hour(s) ago";
    } else if ($interval->m > 0) {
        $nice_interval = $interval->m . " minute(s) ago";
    } else {
        $nice_interval = "seconds ago";
    }

    return $nice_interval;
}

function human_filesize($file)
{
    $bytes = filesize($file);
    $decimals = 1;
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) $sz = 'KMGT';
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}