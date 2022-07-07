<?php
// ??
function delFile($path) {
    unlink($path);
}

function getRandomFileNum($fileName) {  // 랜덤파일
    // 랜덤파일명 = gen_uuid_v4
    // 결과값 => de4fd857-fa96-4c15-84e2-0bc0292acb0a.jpg 
    return gen_uuid_v4().".".getExt($fileName);
}

function getExt($fileName) {
    return pathinfo($fileName, PATHINFO_EXTENSION); // 안정적이고 빠름.
    /*  Q. 확장자명 가지고 오기.         
        : .(점_기준_) 주소를 가져오고, 거기에서 마지막애 = 확장자. 그것을 리턴
        return end(explode(".", $fileName)); // 내 풀이
        선생님풀이
        return substr($fileName, strrpos($fileName, "."));
    */
}

function gen_uuid_v4() { 
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x'
        , mt_rand(0, 0xffff)
        , mt_rand(0, 0xffff)
        , mt_rand(0, 0xffff)
        , mt_rand(0, 0x0fff) | 0x4000
        , mt_rand(0, 0x3fff) | 0x8000
        , mt_rand(0, 0xffff)
        , mt_rand(0, 0xffff)
        , mt_rand(0, 0xffff) 
    ); 
}
