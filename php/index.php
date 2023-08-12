<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php echo $_ENV['SERVER_NAME'].': '.$_SERVER['REQUEST_URI'] ?>
    </title>
    <link rel="stylesheet" type="text/css" href="/master.css">
</head>
<body>

<div id="header">
    <div class="h1">CDN at <?php echo getenv('SERVER_NAME') ?>
        <div class="subtitle">Open Source Content Delivery Platform</div>
    </div>
    <div id="links">
        <?php include 'links.php' ?>
    </div>
</div>
<div id="two-columns">
    <div id="content">

        <?php
        // if the folder does not exist, stop generating more
        if ($GLOBALS["request"] === false) {
            echo '<div class="error">directory not found: '.$_SERVER['REQUEST_URI'].'</div>';
            exit;
        }
        ?>

        <div id="folder-view-head">
            <div id="last-update-time"><?php echo $_SERVER['REQUEST_URI'] ?></div>
            <div id="download-group">
                <div id="download-type">ZIP</div>
                <form method="post" action="">
                    <input id="btn-download" type="submit" name="download-zip" value="Download Zip">
                </form>
            </div>
        </div>

        <div id="stats-group">
            <?php include 'statistics.php'; ?>
        </div>

        <div id="folder-path">Content</div>
        <div id="folder-view">
            <?php include 'explorer.php'; ?>
        </div>
        <?php include 'readme.php'; ?>
    </div>
    <?php include 'about.php' ?>
</div>

<div id="footer">
    <div id="powered-by">Powered by Chare: <a href="https://github.com/Servostar/chare">Source</a></div>
</div>

</body>
</html>
