<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>instagram</title>
    <title><?= isset($this->title) ? $this->title : _SERVICE_NM ?></title>
    <link rel="stylesheet" href="/static/css/common.css">
    <link rel="icon" type="image/png" sizes="16x16" href="/static/img/favicon-16x16.png">
    <?php 
        if(isset($this->css)) {
            foreach($this->css as $item) {
                $href = strpos($item, "http") === 0 ? $item : "/static/css/{$item}.css";
                // stringposition = strpos , http로 문장을 시작한다. 
                // http로 시작하면 $item을 시작, 안하면 /static/css/{$item}.css 실행.
                echo "<link rel='stylesheet' href='{$href}'>
                ";
            }
        }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script defer src="/static/js/common.js"></script>
    <script defer src="/static/js/feed/common_feed.js"></script>

    <?php
        // javascript 파일이 import 됨.
        if(isset($this->js)) {
            foreach($this->js as $item) {
                $src = strpos($item, "http") === 0 ? $item : "/static/js/{$item}.js";
                echo "<script defer src='{$src}'></script>
                ";
            }
        }
    ?>
</head>
<?php 
    if(isset($_SESSION[_LOGINUSER])) {
        echo '<div id="gData" data-loginiuser="<?=getIuser()?>"></div>';
    }
    ?>