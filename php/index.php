<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php echo getenv("OVERWRITE_SERVER_NAME").': '.$_SERVER['REQUEST_URI'] ?>
    </title>
    <link rel="stylesheet" type="text/css" href="/master.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>
    <script src="/clipboard.js" type="text/javascript"></script>
</head>
<body>

<div id="header">
    <div class="h1">CDN at <?php echo getenv('OVERWRITE_SERVER_NAME') ?>
        <div class="subtitle">Open Source Content Delivery Platform</div>
    </div>
    <div id="directory-path">
        <?php include_once "directorypath.php" ;?>
    </div>
    <div id="links">
        <?php include_once 'links.php' ?>
    </div>
</div>
<div id="two-columns">
    <div id="content">

        <?php
        include_once "common.php";
        include_once "explorer.php";

        global $explorer;

        // if the folder does not exist, stop generating more
        if (!$explorer->isdirvalid) {
            echo '<div class="error">directory not found: '.$_SERVER['REQUEST_URI'].'</div>';
            exit;
        }

        if (count($explorer->files) != 0 && (count($explorer->files) != 1 || $explorer->files[0] !== '..')) {
            echo '<div id="folder-view-head">
                    <div id="download-zip-group">
                        <div id="download-zip-type">ZIP</div>
                        <form method="post" action="">
                            <input id="btn-zip-download" type="submit" name="download-zip" value="Download Zip">
                        </form>
                    </div>
                    <div id="download-zip-group" class="align-right">
                    <div id="download-zip-type">HTTPS</div>
                    <div id="https-download-link">
                        '. create_link_from_uri($_SERVER['REQUEST_URI']) .'
                    </div>
                    <button id="copy-to-clipboard" onclick="copyLinkToClipboard()">
                        <i class="fa fa-regular fa-clipboard"></i>
                    </button>
                </div>
    
            </div>';
        }
        ?>

        <?php include_once 'statistics.php'; ?>

        <div id="folder-path">Content</div>
        <div id="folder-view">
            <?php
                global $explorer;
                $explorer->generate_file_list();
            ?>
        </div>
        <?php include_once 'readme.php'; ?>
    </div>
    <?php include_once 'about.php' ?>
</div>

<div id="footer">
    <div id="powered-by">Powered by Chare: <a href="https://github.com/Servostar/chare">Source</a></div>
</div>
</body>
</html>
