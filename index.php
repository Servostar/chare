<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="master.css">
</head>
<body>

<div class="h1">CDN at teridax.de</div>

<div id="folder-view-head">
    <div id="last-update-time">last updated</div>
    <div id="download-group">
        <div id="download-type">ZIP</div>
        <button id="btn-download">Download</button>
    </div>
</div>

<div id="folder-path">
    <?php
    echo $_SERVER['REQUEST_URI'];
    ?>
</div>
<div id="folder-view">
    <?php
    require __DIR__ . '/vendor/autoload.php';
    use League\CommonMark\CommonMarkConverter;
    use League\CommonMark\Exception\CommonMarkException;

    function get_file_change_time($file) {
        $timestamp = filectime($file);
        $current_time = new DateTime();
        $passed_time = new DateTime(date('Y-m-d H:i:s', $timestamp));
        $interval = $current_time->diff($passed_time);

        if ($interval->y > 0) {
            $nice_interval = $interval->y." year(s) ago";
        } else if ($interval->days > 0) {
            $nice_interval = $interval->days." day(s) ago";
        } else if ($interval->h > 0) {
            $nice_interval = $interval->h." hour(s) ago";
        } else if ($interval->m > 0) {
            $nice_interval = $interval->m." minute(s) ago";
        } else {
            $nice_interval = "seconds ago";
        }

        return $nice_interval;
    }

    $dir = '.';
    $files = scandir($dir);
    usort($files, function($a, $b) use ($dir) {
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

        if (is_dir($file)) {
            echo '<a class="folder-view-item folder-icon" href="#!">
                            <div class="file-name">'.$file.'</div>
                            <div class="file-added">'.$date.'</div>
                        </a>';
        } else {
            echo '<a class="folder-view-item file-icon" href="#!">
                            <div class="file-name">'.$file.'</div>
                            <div class="file-added">'.$date.'</div>
                        </a>';

            if (preg_match('/readme(\.(md|txt))?/i', $file)) {
                if ($file_handle = fopen($file, 'r')) {
                    $filesize = filesize($file);
                    $GLOBALS["description"]= fread($file_handle, $filesize);
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
                echo '<div id="readme-title">README/description</div><div id="readme">'.$markdown.'</div>';
            } catch (CommonMarkException $e) {
                print("Unable to render markdown: ". $e);
            }
        }
    ?>


</body>
</html>
