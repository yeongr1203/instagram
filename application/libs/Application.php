<?php
// 여기는 객체화 시켜주기때문에 인클루드할 것들을 여기서 작업하면 
namespace application\libs;

require_once "application/utils/UrlUtils.php";
require_once "application/utils/SessionUtils.php";
require_once "application/utils/FileUtils.php";


class Application{
    
    public $controller;
    public $action;
    private static $modelList = [];     // static 붙은 순간 바로 메모리에 올라감. new와 달리 쓰는 만큼이 아니라 딱 한 번만 사용.
    // new는 쓰는 만큼 계속 생성. 
    // 객체생성과는 의미 없고, 스코프 한개. 최초 딱한번 등록되고 그 값이 계속 유지. 
    // Application 안에 넣었는데, staticr 객체 생성과는 별개이고,
    // Application 이라는 클래스 명으로 접근하기 위해서 안에 넣음.

    public function __construct() {       // 생성자이므로 객체 생성이 되면 무조건 실행됨.   
        $urlPaths = getUrlPaths();
        $controller = isset($urlPaths[0]) && $urlPaths[0] != '' ? $urlPaths[0] : 'board';
        $action = isset($urlPaths[1]) && $urlPaths[1] != '' ? $urlPaths[1] : 'index';

        if (!file_exists('application/controllers/'. $controller .'Controller.php')) {
            echo "해당 컨트롤러가 존재하지 않습니다.";
            exit();
        }

        // // 자동으로 model을 객체화 되도록 작업. 
        // // => 그래서 new model 할 필요 없음. this안에 있음. 
        // if(!in_array($controller, static::$modelList)) {
        //     $modelName = 'application\models\\' . $controller . 'model';
        //     static::$modelList[$controller] = new $modelName();
        // }

        $controllerName = 'application\controllers\\' . $controller . 'controller';               
        // $model = static::$modelList[$controller];
        $model = $this->getModel($controller);  // => static::$modelList[$controller] = new $modelName(); 이걸 겟모델로 작성.
        // controller = feed를 말하고, 그 주소값이 담겨서 model에 넘어가고.
        new $controllerName($action, $model);   // feed model의 주소값을 $model에 넣어주는 것.
        // controller에서 생성자함수에 각 매개변수가 대입이됨. 그래서 생성자함수가 자동실행이 되어
        // 작동이 되면서 , feedController가 객체화가 됨.
        // $model 여기에 feed일 땐, feed / user일때는 user로 값을 불러옴.
    }

    // feedCotroller에서 feedModel을 사용하고 싶을 수 있기 때문에,
    // 외부 내부에서도 사용할 수 있도록 public으로 작성.
    // 스태틱 메소드 이기 때문에 외부에서 접근이 가능함. =>static이 없는 메소드는 instance메소드
    // static은 클래스로 접근해서 호출하면 바로 접근할 수 있다.
    public static function getModel($key) {
        if(!in_array($key, static::$modelList)) {
            $modelName = 'application\models\\' . $key . 'model';
            static::$modelList[$key] = new $modelName();
            // 내가 사용하려는 모델(userModel 또는 feedModel) 중 하나라도 없으면 객체화=만들어서 주고.
            // modelList를 객체화해서 주기 때문에 에러가 터지지 않음.
        }
        // 객체화해서 빼서 주기. => 있으면 주소값 담에서 돌려주고
        return static::$modelList[$key];
    }
}
