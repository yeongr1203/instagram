<?php
namespace application\controllers;

class Controller {   
    protected $ctx; 
    protected $model;
    // 모델에  static을 붙일 수 없는 이유는, feedModel과 userModel이 있다. 값을 하나만 받을 수 있기 때문에 static을 붙일 수 없음.
    // 멤버필드에서 메소드를 사용한다. // 스태틱을 붙인 애를 사용하거나 파마리터로 받는 애라면 스태틱을 붙여도 된다.
    private static $needLoginUrlArr = [     // 로그인해야만 들어갈 수 있는 영역.
        "feed",
        "user/feedwin"
     ];

    // 권한부분.
    // [] 안에 없으면 권한 부분이 없습니다. 라고 뜸. 권한 예시.
    // 1차 주소값이 board면 로그인이 필요.
    // 2차 주소값까지 되어 있어야만 로그인해야만 작업할 수 있도록 하는 것.


    public function __construct($action, $model) {    
        if(!isset($_SESSION)) {
            session_start();
        }    
        $urlPaths = getUrl();
        foreach(static::$needLoginUrlArr as $url) {
            if(strpos( $urlPaths, $url) === 0 && !isset($_SESSION[_LOGINUSER]) ) {
                /* Q. 로그아웃 상태 = /feed/index 주소값이동시 "권한이 없습니다. 나타나는데 메세지 대신 /user/signin으로 주소값이동되게 처리 */
                // echo "권한이 없습니다.";
                // exit();
                $this->getView("redirect:/user/signin");    // 24, 26 없이 이것만 작성해도 됨.

                // 내풀이
                // header("Location:/user/signin");    // 로그인페이지로 이동하도록 설정.
            }
        }

        $this->model = $model;
        $view = $this->$action();   // 메소드 호출.
        // 나 자신의 feedMethod를 담아 호출.
        if(empty($view) && gettype($view) === "string") {          // 비어있으면 에러터지고, 아니면 지나쳐서 아래 if문 실행.
            // gettype($view) === "string" 넣은 이유는? 피드 page=2번으로 날렸음. 근데 list변수에 배열이 들어갔는데 빈 공간으로 들어갈 줄 알았는데, 에러가 터짐.
            // 그래서 보통에러발생이 나타나는게 스트링이 빈칸일때 나타나는 것이라, 추가한 것.
            echo "Controller 에러 발생";
            exit();
        }

        // 문자열 => 화면 응답
        // 객체,배열 => 제이슨이 응답
        if(gettype($view) === "string") {
            require_once $this->getView($view);
        } else if(gettype($view) === "object" || gettype($view) === "array") {
            header("Content-Type:application/json");
            echo json_encode($view);    // encode라는 함수를 사용해서 제이슨 인코드한것.
            // 인코드 코드를 쓰면 인코드라는 네임규칙을 사용함.
        }        
    }

    protected function getModel() {

    }
    
    protected function addAttribute($key, $val) {
        $this->$key = $val;
    }

    protected function getView($view) {
        if(strpos($view, "redirect:") === 0) {
            header("Location: " . substr($view, 9));
            exit();
        }
        return _VIEW . $view;
    }

    // session
    protected function flash($name = '', $val = '') {
        if(!empty($name)) { //공백이 아니면
            if(!empty($val)) {
                $_SESSION[$name] = $val;
            } else if(empty($val) && !empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
        }
    }
}

