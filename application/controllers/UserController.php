<?php
namespace application\controllers;

// require_once "application/utils/UrlUtils.php";
use application\libs\Application;

class UserController extends Controller {   // aplication 파일에 controller를 먼저 객체화 시켰음.
    // 로그인
    public function signin() {        
        switch(getMethod()) {
            case _GET:  // get방식일 경우 이렇게 처리가 될 것이다.
                return "user/signin.php";
            case _POST:
                // 아이디, 비밀번호가 하나라도 없거나 틀리면 => user/signin 리다이렉트
                // 아이디 비밀번호가 맞다면  /feed/index
                
                // 내 풀이
                // $param = [
                //     "email" => $_POST["email"],
                //     "pw" => $_POST["pw"]
                // ];
                // $result = $this->model->selUser($param);
                // if($result === false || !password_verify($param["pw"],$result->pw)) {
                //     return "user/signin.php";
                // }

                // 선생님풀이 
                $email = $_POST["email"];
                $pw = $_POST["pw"];
                $param = [ "email" => $email ];
                $dbUser = $this->model->selUser($param);    // selUser에 이 param을 보내줌.
                if(!$dbUser || !password_verify($pw,$dbUser->pw)) { // false라면 실행.
                    return "redirect:signin?mail={$email}&err"; // 쿼리스트링에 보낸 이메일주소&err 이라고 나타남.
                }  
                $dbUser->pw = null;     // 비밀번호를 굳이 들고 있지 않아도 되기 때문에.
                $dbUser->regdt = null;  // 해지. -> 메모리사용을 줄이기위해서 사용. // 이렇게 해도 되고 안해도 됨.
                $this->flash(_LOGINUSER,$dbUser);
                return "redirect:/feed/index";   // true일 때 사용.
                // 기존은 문자열 리턴, 컨트롤러에 보면     
        }        
    }   // 혹시나 리턴이나 객체로 리턴을 했다면, json실행.
    /* 
        sesson을 사용하는 이유? 스코프가 길다. 
        로그인 할때도 사용하지만, 
        이 화면에서 다른 화면으로 데이터 전달할 때도 사용할 수 있음. 
        화면을 끄지않으면 세션에 박힌 것을 사용할 수 있다.
    */

    //  회원가입
    public function signup() {  
        // print getMethod();

        // 호출이 2번 이루어 지기 때문에 switch가 더 효율적.
        // if(getMethod() === _GET) {
        //     return "user/signup.php";
        // } else if (getMethod() === _POST) {
        //     return "redirect:signin";
        // }
        switch(getMethod()) {
            case _GET:
                return "user/signup.php";
            case _POST:
                // i_user에 인서트하기.
                // 비밀번호는 암호화.
                $insPw = password_hash($_POST["pw"], PASSWORD_BCRYPT);
                $param = [
                    "email" => $_POST["email"],
                    "pw" => $insPw,
                    "nm" => $_POST["nm"]
                ];                
                $this->model->insUser($param);
                return "redirect:signin";
        }
    }

    // 아래는 처음에 선생님 테스트용.
    // public function signup() {
    //     $method = getMethod();
    //     switch($method) {
    //         case _GET:
    //             return;     
    //         case _POST:
    //             return;
    //     }
    // }

    // 로그아웃
    public function logout(){
        $this->flash(_LOGINUSER);
        return "redirect:/user/signin";
    }

    public function feedwin() {     // 여기 역시 로그인해야만 들어갈 수 있는 곳
        $iuser = isset($_GET["iuser"]) ? intval($_GET["iuser"]) : 0;
        // $param = [ "iuser" => $iuser ];
        $param = [ "feediuser" => $iuser , "loginiuser" => getIuser()];
        $this->addAttribute(_DATA, $this->model->selUserProfile($param));   // 프로필
        $this->addAttribute(_JS,[ "user/feedwin","https://unpkg.com/swiper@8/swiper-bundle.min.js"]);
        $this->addAttribute(_CSS,["feed/index","user/feedwin","https://unpkg.com/swiper@8/swiper-bundle.min.css"]);
        // 여기는 템플릿사용.
        $this->addAttribute(_MAIN, $this->getView("user/feedwin.php"));
        return "template/t1.php";
    }
    
    // feed
    public function feed() {
        if(getMethod()=== _GET) {
            $page = 1;
                if(isset($_GET["page"])) {
                    $page = intval($_GET["page"]);
                }
                $starIdx = ($page -1) * _FEED_ITEM_CNT;
                $param = [
                    "starIdx" => $starIdx,
                    "toiuser" => $_GET["iuser"],
                    "loginiuser" => getIuser()
                ];
            $list = $this->model->selFeedList($param);
            foreach($list as $item) {
                $param2 = ["ifeed" => $item->ifeed];
                $item->imgList = Application::getModel("feed")->selFeedImgList($param2);
                $item->cmt = Application::getModel("feedcmt")->selFeedCmt($param2);
                // $item->imgList = $this->model->selFeedImg($item);
                // $item->imgList = Application::getModel("feed")->selFeedImgList($item);
                // 스태틱 사용하면 안될때? 
                // 객체 생성 했을 때, 다른 값을 넣으려하면 절때 스테틱을 사용하면 안됨.
                // 사용하려는 객체에 스태틱이 안붙은 멤버필드를 메소드에 사용해야한다면 스태틱을 붙이면 안됨. 그것 빼고는 붙일 수 있음.
            }
            return $list;
        }
    }

    //follow
    public function follow() {
        // 필요한 값 => 팔로우 할꺼야, 취소 할꺼야.
        // 즉, post, delete만 사용할 것.
        $param = [
            'fromiuser' => getIuser(),
            // 'toiuser' => $_POST["toiuser"]
            // 'toiuser' =>$_REQUEST["toiuser"] // json 사용으로 지움.
            // 굳이 포스트방식 겟방식 나눌 필요 없이 REQUEST로 작업.
            // JAVA는 request로 사용하면 넘어가질 않음. 
        ];
        switch(getMethod()) {
            // 어떤 것을 주면 팔로우 취소 할것인가.? fromiuser(나= 세션값으로 처리) 와 toiuser(상대방)
            // 보낼때 toIuser로 보낼 것.          
            case _POST: // 팔로우 처리
                // $infollow = $this->model->insFollow($param);
                // return [_RESULT => 1];
                $json = getJson();
                $param["toiuser"] = $json["toiuser"];
                // 선생님풀이
                return [_RESULT => $this->model->insFollow($param)];
                
            case _DELETE:   // 팔로우 취소
                // $this->model->delFollow($param);
                // return [_RESULT => 1];
                $param["toiuser"] = $_GET["toiuser"];
                //선생님 풀이
                return [_RESULT => $this->model->delFollow($param)];
        }
    }

    public function profile() { 
        // 삭제 = delete처리
        switch(getMethod()) {
            case _DELETE:
                $loginUser = getLoginUser();    // 여기 저장된 값 중에 메인 이미지값을 뜻함. 왜냐면 실제로 그 값을 지워야 하니깐.
                if($loginUser) {
                    $path = "static/img/profile/{$loginUser->iuser}/{$loginUser->mainimg}";
                    if(file_exists($path) && unlink($path)) {
                        $param = ["iuser"=>$loginUser->iuser, "delMainImg" => 1 ];
                        if($this->model->updUser($param)) {
                            $loginUser->mainimg = null;
                            return [_RESULT => 1 ];
                        }
                    }
                }
                return [_RESULT => 0];
        }
    }

}