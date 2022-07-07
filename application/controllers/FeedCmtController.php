<?php
namespace application\controllers;

class FeedCmtController extends Controller {

    public function index() {
        switch(getMethod()) {
            case _POST:
                $json = getJson();
                // 공백만 있는 댓글 및 아무것도 없는 댓글 insert 안되게 하는 코드
                // if문에서 return 빼고 모두 포함 
                if(preg_replace('/\s+/','', $json["cmt"])) {
                    $json["iuser"] = getIuser();
                    return [_RESULT => $this->model->insFeedCmt($json)];
                } else {
                    return [_RESULT => 0];
                }
            // feed 더보기
            case _GET:
                $ifeed = isset($_GET["ifeed"]) ? intval($_GET["ifeed"]) : 0;
                $param = [ "ifeed" => $ifeed ];
                return $this->model->selFeedCmtList($param);
        }

    }
}