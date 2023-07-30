<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php
        echo "CDN ".$_SERVER['REQUEST_URI'];
        ?>
    </title>
    <link rel="stylesheet" type="text/css" href="master.css">
</head>
<body>

<div id="content">

    <div class="h1">CDN at teridax.de</div>

    <div id="folder-view-head">
        <div id="last-update-time">placeholder text</div>
        <div id="download-group">
            <div id="download-type">ZIP</div>
            <form method="post" action="">
                <input id="btn-download" type="submit" name="download-zip" value="Download Zip">
            </form>
        </div>
    </div>

    <div id="stats-group">
        <?php
        $file_count = 0;
        // start at -1 so that when counting the top folder (..) we get a total count of 0
        $folder_count = 0;
        $total_size = 0;
        $human_size = "0KB";
        echo '<div id="folder-count">'.$folder_count.' folder</div>
            <div id="file-count">'.$file_count.' files</div>
            <div id="file-size-total">'.$human_size.' </div>';
        ?>
    </div>

    <div id="folder-path">
        <?php
        echo '<b>Path</b>&nbsp;'.$_SERVER['REQUEST_URI'];
        ?>
    </div>
    <div id="folder-view">
        <?php
        require __DIR__ . '/vendor/autoload.php';

        use League\CommonMark\CommonMarkConverter;
        use League\CommonMark\Exception\CommonMarkException;

        include_once 'common.php';

        $dir = get_current_dir();

        // FIXME: https://www.w3docs.com/snippets/php/automatic-download-file.html
        if (!is_dir($dir)) {
            echo $dir;
            $filePath = $dir;
            $fileName = basename($filePath);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);
        }

        $files = scandir($dir);
        usort($files, function ($a, $b) use ($dir) {
            $aIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $a);
            $bIsDir = is_dir($dir . DIRECTORY_SEPARATOR . $b);
            if ($aIsDir && !$bIsDir) {
                return -1;
            } elseif (!$aIsDir && $bIsDir) {
                return 1;
            } else {
                return strcmp($a, $b);
            }
        });
        foreach ($files as $file) {
            // ignore the current directory
            if ($file == '.')
                continue;

            $date = get_file_change_time($file);
            $size = human_filesize($file);

            // compile the files href path. This must be relative to the
            // share folder root
            $target_path = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].DIRECTORY_SEPARATOR.$file;

            if (is_dir($file)) {
                echo '<a class="folder-view-item folder-icon" href="'.$target_path.'">
                            <div class="file-name">' . $file . '</div>
                            <div class="file-size">' . $size . '</div>
                            <div class="file-added">' . $date . '</div>
                        </a>';
            } else {
                echo '<a class="folder-view-item file-icon" href="'.$target_path.'">
                            <div class="file-name">' . $file . '</div>
                            <div class="file-size">' . $size . '</div>
                            <div class="file-added">' . $date . '</div>
                        </a>';

                if (preg_match('/readme(\.(md|txt))?/i', $file)) {
                    if ($file_handle = fopen($file, 'r')) {
                        $filesize = filesize($file);
                        $GLOBALS["description"] = fread($file_handle, $filesize);
                        fclose($file_handle);
                    }
                }
            }
        }
        ?>

    </div>
    <?php
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
    ?>
</div>

<div id="footer">
    <div id="powered-by">Powered by Chare: <a href="https://github.com/Servostar/chare">Source</a></div>
</div>

</body>
</html>
