<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php
        echo "CDN ".substr( $_SERVER['REQUEST_URI'], 1 );
        ?>
    </title>
    <link rel="stylesheet" type="text/css" href="master.css">
</head>
<body>

<div id="content">

    <div class="h1">CDN at teridax.de</div>

    <div id="folder-view-head">
        <div id="last-update-time">last updated</div>
        <div id="download-group">
            <div id="download-type">ZIP</div>
            <form method="post" action="">
                <input id="btn-download" type="submit" name="download-zip" value="Download Zip">
            </form>
        </div>
    </div>

    <?php
    if(isset($_POST['download-zip'])) {
        // Create a new ZipArchive object
        $zip = new ZipArchive();

        // Specify the name of the zip file to be created
        $zip_file = 'archive.zip';

        // Open the zip file for writing
        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Get the current directory
            $current_directory = getcwd();

            // Create a recursive directory iterator to iterate through all files and directories in the current directory
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($current_directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            // Add each file to the zip archive
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $path = $file->getRealPath();
                    $relative_path = substr($path, strlen($current_directory) + 1);
                    $zip->addFile($path, $relative_path);
                }
            }

            // Close the zip archive
            $zip->close();

            // Prompt the user to download the zip file
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zip_file . '"');
            header('Content-Length: ' . filesize($zip_file));
            readfile($zip_file);

            // Delete the zip file after it has been downloaded
            unlink($zip_file);

            exit;
        } else {
            echo "Error creating archive.";
        }
    }
    ?>

    <div id="folder-path">
        <?php
        echo '<b>Path</b>&nbsp;'.substr( $_SERVER['REQUEST_URI'], 1 );
        ?>
    </div>
    <div id="folder-view">
        <?php
        require __DIR__ . '/vendor/autoload.php';

        use League\CommonMark\CommonMarkConverter;
        use League\CommonMark\Exception\CommonMarkException;

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

        $dir = '.';
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

            if (is_dir($file)) {
                echo '<a class="folder-view-item folder-icon" href="./'.$file.'">
                            <div class="file-name">' . $file . '</div>
                            <div class="file-size">' . $size . '</div>
                            <div class="file-added">' . $date . '</div>
                        </a>';
            } else {
                echo '<a class="folder-view-item file-icon" href="./'.$file.'">
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
