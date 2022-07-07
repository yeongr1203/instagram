<?php
    function getJson() {
        return json_decode(file_get_contents('php://input'),true);
    }
    // 얘는 에러 터짐_나타내는것_ 로그인
    function getParam($key) {
        return isset($_GET[$key]) ? $_GET[$key] : "";
        // UrlUtils에 들어오는 key에 값이 있었으면 담겨진 value값을 리턴될 것이고, 없었다면 빈칸이 리턴되도록 함.
    }

    function getUrl() {
        return isset($_GET['url']) ? rtrim($_GET['url'], '/') : "";
    }
    function getUrlPaths() {
        $getUrl = getUrl();        
        return $getUrl !== "" ? explode('/', $getUrl) : "";
    }

    function getMethod() {
        // $headers = getallheaders();
        // return $headers['Accept'];
        return $_SERVER['REQUEST_METHOD'];
    }

    function isGetOne() {
        $urlPaths = getUrlPaths();
        if(isset($urlPaths[2])) { //one
            return $urlPaths[2];
        }
        return false;
    }


// function getUrlPaths() {
    //     $getUrl = '';
    //     if (isset($_GET['url'])) {
    //         $getUrl = rtrim($_GET['url'], '/');
    //         $getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
    //     }
    //     return explode('/', $getUrl);
    // }

    // function getUrlPaths() {
    //     $getUrl = getUrl();        
    //     return $getUrl !== "" ? explode('/', $getUrl) : "";
    // }

    // function getMethod() {
    //     $headers = getallheaders();
    //     return $headers['Accept'];
    // }

    // function isGetOne() {
    //     $urlPaths = getUrlPaths();
    //     if(isset($urlPaths[2])) { //one
    //         return $urlPaths[2];
    //     }
    //     return false;
    // }
