<?php

global $dir;
try {
    $dir = current_dir();
} catch (Exception $e) {
    echo '<div class=".error">resolved path is not within public share</div>';
    exit(1);
}

/**
 * Tests if the supplied environment variable ios invalid
 * @param bool|array|string $name
 * @return bool if the environment variable is invalid or not
 */
function is_env_invalid(bool|array|string $name): bool
{
    return empty($name) or is_array($name);
}

/**
 * Get the path to the directory which contents are to be publicly shared.
 * @return string
 */
function __get_share_path(): string
{
    $env_path = getenv('SHARE_PATH');

    // if the path to the directory to share is invalid
    // we will use the default share path
    if (is_env_invalid($env_path)) {
        $env_path = "/var/share";
    }

    return $env_path;
}

/**
 * Tests weather or not the current URI is the root of the share.
 * Examples:
 * - /files/
 * - /files////
 * - /files
 * @return bool
 */
function is_root(): bool
{
    return preg_match("/^\/files\/*$/", $_SERVER['REQUEST_URI']);
}

/**
 * Returns the URI without the /file/ prefix
 * @return string
 */
function file_uri(): string
{
    return preg_replace("/^\/files/", '', $_SERVER['REQUEST_URI']);
}

/**
 * Tries to create the locals server url from global $_SERVER variables
 * @return string
 */
function __local_server_url(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . "://" . $host;
}

/**
 * Create a full http(s) request url for this server with the specified URI.
 * @param $uri string uri to append
 * @return string the full uri
 */
function create_link_from_uri(string $uri): string
{
    $url = __local_server_url();

    $overwriteurl = getenv("OVERWRITE_URL");
    // if the variable is false or an array it is invalid
    if(!is_env_invalid($overwriteurl))
    {
        $url = $overwriteurl;
    }

    return filter_var($url.$uri, FILTER_SANITIZE_URL);
}

/**
 * Get the absolute path that is to be viewed as share.
 * Returns false if the path is invalid or the folder does not exist or
 * the path is outside the directory to share.
 * @throws Exception if the absolute path is not inside the share path
 * @return false|string
 */
function current_dir(): false|string
{
    $share_path = __get_share_path();
    $rel_path = $share_path.DIRECTORY_SEPARATOR.file_uri();
    $abs_path = realpath($rel_path);

    // test if the
    if (str_starts_with(realpath($abs_path), realpath($share_path))) {
        return $abs_path;
    }
    throw new Exception("specified path is not a sub path of share dir");
}

function format_bytes($bytes, $decimals = 2): string
{
    $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
}

function filesize_recursive($file): int
{
    $size = 0;

    if (is_dir($file)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file));

        foreach ($iterator as $rfile) {
            if ($rfile->isFile()) {
                $size += filesize($rfile->getPathname());
            }
        }
    } else {
        $size = filesize($file);
    }
    return $size;
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
    if (empty($file))
        return $default;

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