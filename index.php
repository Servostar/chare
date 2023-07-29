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

<div id="folder-path">/path/to/folder</div>
<div id="folder-view">
    <?php
    require __DIR__ . '/vendor/autoload.php';
    use League\CommonMark\CommonMarkConverter;
    use League\CommonMark\Exception\CommonMarkException;

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

        if (is_dir($file)) {
            echo '<a class="folder-view-item folder-icon" href="#!">
                            <div class="file-name">'.$file.'</div>
                            <div class="file-added">date added</div>
                        </a>';
        } else {
            echo '<a class="folder-view-item file-icon" href="#!">
                            <div class="file-name">'.$file.'</div>
                            <div class="file-added">date added</div>
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
