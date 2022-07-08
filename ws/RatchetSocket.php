<?php
namespace ws;
 
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use application\libs\Application;
//use application\models\DmModel;

class RatchetSocket implements MessageComponentInterface {  
    // implements = extends 와 같은 뜻 ( 상속하다와 같다. 뒤에는 상속 받고자 하는 클래스(추상이든 뭐든) => extends, 인터페이스가 implements )
    // class 성격과 interface 성격을 둘다 가지고 있는 것 = 추상 class
    // 추상 class = > 추상 메소드를 하나라도 가지고 있으면 무조건 추상class로 만들어야함.
    // 추상class를 안가지고 있어도 만들수 있음. 앞에 fa붙이면?
    // 추상클래스는 객체화가 될 수 없다!!! => 부모

    // 인터페이스 => 내용이 있는 클래스를 만들 수 없다.
    // 인터페이스 => 객체를 담을 수는 없지만, 변수로 객체를 담을 수 있음.
    /* 
        interface Mylnter {
                    public sum(int a, int b);
        }

        class Uselnter implements Mylnter {
            public int sum(int a, int b){
                return a+b;
            }
        }

        Mylnter u = new Userlnter();  // 
        => 타입이 중요함. u를 객체화 했고, 
        => 부모로서의 활동도 함. 

    */
    // 인터페이스에 만약 추상메소드가 4개가 있는데, 명만 있고 내용은 없을 때, 
    /*
    public function onOpen(ConnectionInterface $conn) => 선언부
    {
        내용
    } => 이부분은 구현. 
    즉, 내용부분에 값이 없는것.
    안에 내용이 없더라도 선언은 있어야 함.
    그래야 에러가 터지지 않음. 
    느슨한 연결이라고 해서 저객체와 이객체와 느슨하게 연결하는 역할, 즉 규격을 만드는것.
    => 모니터와 연결하는 선 => 연결하는 것 콘센트. = 규격.
    인터페이스 설계할 때, 활용도 중 한가지는
    sum을 만들어 달랬는데, sam으로 오타가 났다면 실행이 안됨.

    소켓통신에서 필요한 것? 
    
    */
    protected $clients;
 
    public function __construct() {
        // clients 변수에 접속 클라이언트들을 담을 객체 생성
        $this->clients = new \SplObjectStorage;
        // 접속한 클라이언트 정보가 담기는데, 그 정보를 담기 위한 것 => \SplObjectStorage

    }
 
    // 클라이언트 접속
    public function onOpen(ConnectionInterface $conn) {     
        // onOpen 에서 앞에 on이 붙었을 때는 이벤트가 걸려져 있음. 
        // $conn 접속하는 사람의 정보가 담긴 객체
        // open은 뭐냐면, 내서버로 접속하게 되면서  

        // clients 객체에 클라이언트 추가
        $this->clients->attach($conn);
        $conn->send($conn->resourceId);

        echo "New connection! ({$conn->resourceId}) / Clients Count : {$this->clients->count()}\n";
    }
 
    //메세지 전송, $from 인자값은 메세지를 보낸 클라이언트의 정보, $msg인자값은 보낸 메세지
    public function onMessage(ConnectionInterface $from, $msg) {    
        // $from 보낸 애의 정보, $msg는 보낸 애의 메세지
        $data = json_decode($msg);
        print_r($data);
        switch($data->type) {
            case "dm":
                $param = [
                    "idm" => $data->idm,
                    "loginiuser" => $data->iuser,
                    "msg" => $data->msg
                ];
                $model = Application::getModel("dm");
                $model->insDmMsg($param);
                $model = null;
                print "dm send end!!";
                break;
        }        
        
        
        foreach ($this->clients as $client) {    
            // 여기 저장된 모든 클라이언트에게  $client->send($msg); $msg 메세지를 보내겠다라는 뜻. => 전체 브로드캐스로 함.
            // 원래는 이렇게 하면 안됨.
            // 지금 전수로 보내는 것이기 때문에 남이 볼 수도 있고 나한테 다시 또 보내기도 함.
            
            //메세지 전송
            // print "send!!!\n";
            // print $msg . "\n";
            $client->send($msg);
        }
    }
 
    //클라이언트 접속 종료
    public function onClose(ConnectionInterface $conn) {
        // clients 객체에 클라이언트 접속정보 제거
        $this->clients->detach($conn);
 
        echo "Connection {$conn->resourceId} has disconnected\n";
    }
 
    // 에러가 터지면 접속 종료 시킴.
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
 
        $conn->close();
    }
}