<?php

include_once 'common.php';

class Explorer {

    public $dir;
    public $files;
    public $urls;
    public $filecount;
    public $foldercount;
    public $sizetotal;
    public $isdirvalid;

    public function __construct()
    {
        try {
            $this->dir = current_dir();
            $this->isdirvalid = true;
        } catch (Exception) {
            $this->isdirvalid = false;
        }
        $this->urls = array();
        $this->files = array();

        if (!is_dir($this->dir)) {
            $this->isdirvalid = false;
        } else {
            $this->index();
        }
    }

    private function index(): void
    {
        if (!$this->isdirvalid) {
            return;
        }

        $dir = $this->dir;

        $files = scandir($dir);
        usort($files, function ($a, $b) use ($dir) {
            $aIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $a);
            $bIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $b);
            if ($aIsDir == $bIsDir) {
                return strcasecmp($a, $b);
            } else {
                return $aIsDir ? -1 : 1;
            }
        });

        $is_root = is_root();
        $ignore = explode("\n", read_file_or_default("/srv/config/.ignore", "."));
        $urlmatches = array(
            "readme" => '/^readme(\.txt|\.md)?$/i',
            "license" => '/^license(\.txt|\.md)?$/i',
            "description" => '/^about(\.txt)?$/i',
            "codeofconduct" => '/^code_?of_?conduct(\.txt|\.md)?$/i',
        );
        foreach($urlmatches as $name => $regex) {
            $this->urls[$name] = false;
        }

        foreach ($files as $filename) {

            if (in_array($filename, $ignore) || $is_root && $filename == "..") {
                continue;
            }

            $filepath = $this->dir . DIRECTORY_SEPARATOR . $filename;

            if (is_dir($filepath)) {
                $this->filecount ++;
            } else {
                $this->foldercount ++;
            }
            $this->sizetotal += filesize_recursive($filepath);

            foreach($urlmatches as $name => $regex) {
                if (preg_match($regex, $filename)) {
                    $this->urls[$name] = $filename;
                }
            }

            $this->files[] = $filepath;
        }
    }

    public function generate_file_list(): void
    {
        foreach ($this->files as $file) {
            create_file_html($file);
        }

        if (count($this->files) == 0 || count($this->files) == 1 && $this->files[0] === '..') {
            echo '<div class="information">no files in here</div>';
        }
    }
}

function create_file_html($file): void
{
    $filesize = format_bytes(filesize_recursive($file));
    $filename = basename($file);

    $lastAccessTime = get_last_accesstime($file);

    $uri = preg_replace("/\/+/", '/', $_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$filename);
    $url = create_link_from_uri($uri);

    $iconclass = "fa fa-regular fa-file";
    $colorclass = "";

    if ($filename === "..") {
        $iconclass = "fa fa-solid fa-arrow-up-from-bracket";
        $colorclass = "accent";
    } else if (is_dir($file)) {
        $iconclass = "fa fa-solid fa-folder-blank";
        $colorclass = "accent";
    }

    format_file_entry_html($url, $filename, $filesize, $lastAccessTime, $iconclass, $colorclass);
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
                            <i class="file-icon '.$iconclass." ".$colorclass.'"></i>
                            <div class="file-name">' . $filename . '</div>
                            <div class="file-added">' . $editdate . '</div>
                            <div class="file-size">' . $filesize . '</div>
                        </a>';
}
