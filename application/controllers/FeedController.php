<?php
namespace application\controllers;
use Application\libs\application;

class FeedController extends Controller {
    public function index() {
        $this->addAttribute(_JS,["feed/index","https://unpkg.com/swiper@8/swiper-bundle.min.js"]);
        $this->addAttribute(_CSS,["feed/index","https://unpkg.com/swiper@8/swiper-bundle.min.css"]);
        // 여기는 템플릿사용.
        $this->addAttribute(_MAIN, $this->getView("feed/index.php"));
        return "template/t1.php";
    }    

    public function rest() {
        // print "method : ". getMethod()."<br>";
        // switch(getMethod()) {
        //     case _POST:
        //         // 일단 등록하려는 사진의 내용과 위치값이 보여지는 지 보기.
        //         // 그리고 파일이 받아지는 지 확인
        //         // $countfiles = count($_FILES['imgs']['name']);
        //         if(is_array($_FILES)) {     // file이 넘어오는지 확인하는 것.
        //             foreach($_FILES['imgs']['name'] as $key => $value) {
        //                 print "key : {$key}, value: {$value} <br>";
        //             }
        //         }
        //         print "ctnt : " . $_POST["ctnt"]."<br>"; 
        //         print "loaction : " . $_POST["loaction"]."<br>"; 
        // }
        switch(getMethod()) {
            case _POST:
                if(!is_array($_FILES) || !isset($_FILES["imgs"])) {         // 만약 파일이 비어있을때
                    return  ["result" => 0];    // result가 0이 리턴되면 실패!
                }                
                // Q. insertFeed 메소드 호출하고 리턴값을 받은 다음. 결과값을 출력하기
                // $iuser = getIuser();
                $param = [
                    // "iuser" => $iuser,
                    "location" => $_POST["location"],
                    "ctnt" => $_POST["ctnt"],
                    "iuser" => getIuser()
                ];
                $ifeed = $this->model->insFeed($param);
                /* 여러개를 하나로 묶어서 작업해주는거 = 트랜지션*/
                foreach( $_FILES['imgs']['name'] as $key => $originFileNm ) {
                    // // $file_name = explode(".", $_FILES['imgs']['name'][$key]);
                    // $file_name = explode(".", $value);  // 점을 기준으로 파일내용들을 가지고 오면 나눠서 배열로 들고 오는 것.
                    // // 만약 파일명에 abc.ccc.jpg 이렇게 있을 수 있는데, 확장자만 꺼내려면 
                    // $ext = end($file_name);    // 파일명의 마지막을 가져오는것 // 즉, 확장자
                    // // $file_name[count($file_name) -1];  // 확장자.
                    // // 디버깅할 때, 위에 처럼 입력하면 확장자만 꺼낼 수 있음. => 버그가 없는 소스!
                    // /* 사용하고자 하는 배열도 설정이 가능함. */
                    $saveDirectory = _IMG_PATH. "/feed/" . $ifeed;  // "/profile/" 넣을 경로
                    if(!is_dir($saveDirectory)) {   // 만약 없다면! 아래 실행.
                        mkdir($saveDirectory, 0777, true);  // 권한을 주는것.
                    }
                    $tempName = $_FILES['imgs']['tmp_name'][$key];
                    $randomFileNm = getRandomFileNum($originFileNm);
                    // move_uploaded_file($tempName, $saveDirectory."/test.". $ext);
                    // 선생님 수동으로 변경.
                    if(move_uploaded_file($tempName, $saveDirectory."/". $randomFileNm)) {
                        /* 선생님 풀이
                            $paramImg["img"]
                        */
                        $param = [
                            "ifeed" => $ifeed,
                            "img" => $randomFileNm
                        ];
                        // $this->model->insFeedImg($param);
                        $param["img"] = $randomFileNm;
                        $this->model->insFeedImg($param);
                    }
                }
                // $imgCount = count($_FILES["imgs"]["name"]);
                // return [ "result" => 1 ];
                
                $param2 = ["ifeed" => $ifeed ];
                $data = $this->model->selFeedAfterReg($param2);
                $data -> imgList = $this->model->selFeedImgList($param2);
                return $data;

            // ifeed 좋아요
            case _GET:
                $page = 1;
                if(isset($_GET["page"])) {
                    $page = intval($_GET["page"]);
                }
                $starIdx = ($page -1) * _FEED_ITEM_CNT;
                $param = [
                    "starIdx" => $starIdx,
                    "iuser" => getIuser()
                ];
                $list = $this->model->selFeedList($param);
                foreach($list as $item) {
                    // $imgs = $this->model->selFeedImgList($item);
                    // $item->imgList = $imgs;
                    // 한줄 요약
                    $param2 = [ "ifeed" => $item->ifeed ];  // 배열로
                    $item ->imgList = $this->model->selFeedImgList($param2);
                    // $item ->imgList = $this->model->selFeedImgList($item); 1번
                    // cmt (댓글나오게)
                    // $param2 = [ "ifeed" => $item->ifeed ];  //1번 // 배열로
                    $item->cmt = Application::getModel("feedcmt")->selFeedCmt($param2);
                    // localhost/feed/rest?page=1 검색하면 cmt에 추가되고, 내용이 없으면 cmt:false로 나타난다.
                    
                }
                return $list;
        }
    }
    // 피드 등록 및 삭제
    public function fav() {
        $urlPaths = getUrlPaths();   // getUrlPaths()호출하면 방에 있는 값을 불러와서 실행됨. => feed -1번방, fav-2번방,pk값 - 3번방
        if(!isset($urlPaths[2])) {   // 2번방에 없다면 멈춤.
            exit(); 
        }
        $param = [          
            "ifeed" => intval($urlPaths[2]),
            "iuser" => getIuser()
        ];
        // print $urlPaths[2];
        // 어느피드에 누가 좋아요를 했는지...!
        switch(getMethod()) {
            case _POST:
                // 주소값 /feed/fav/8(pk값) (post 날림) => 등록 / /feed/fav/8(pk값) (delete 날림)=>삭제
                // $result = $this->model->insFeedFav($param);
                // return [_RESULT => $result];
                // 한줄요약
                return [_RESULT => $result = $this->model->insFeedFav($param)];
            case _DELETE:
                // $result = $this->model->delFeedFav($param);
                // return [_RESULT => $result];
                return [_RESULT => $result = $this->model->delFeedFav($param)];
        }
    }
}
