<!DOCTYPE html>
<html lang="en">
<?php include_once "application/views/template/head.php"; ?>
<body class="h-full container-center">
    <div>
        <h1>로그인</h1>
        <!-- err 에러메세지 띄우기 -->
        <div class="err">
            <?php 
            if(isset($_GET["err"])) {
                print "로그인을 할 수 없습니다.";
            }
            ?>
        </div>
        <!-- method를 post방식으로 날릴수있는건 form태그가 있어야 가능함. 나머지 전부 get방식 -->
        <form action="signin" method="post">
            <div><input type="email" name="email" placeholder="email" value="<?=getParam('email')?>" autofocus required></div>
            <!-- getParam('email')은 getParam이라는 UrlUtils에 들어오는 key에 값이 있었으면 담겨진 value값을 리턴될 것이고, 없었다면 빈칸이 리턴되도록 함. -->
            <div><input type="password" name="pw" placeholder="password" required></div>
            <div>
                <input type="submit" value="로그인">
            </div>
        </form>
        <div>
            <a href="signup">회원가입</a>
        </div>
    </div>
</body>
</html>
<!-- 

post 방식은 form태그에서만 사용할 수 있고,
form태그는 method를 get과 post방식 모두 날릴 수 있다.
get방식은 전부, a태그 역시 get방식.

GET 
 : 값이 쿼리스트링으로 전달.

POST (처리 : 등록, 수정, (삭제도 가능하지만 선생님은 get방식으로 처리) )
 : 값이 body에 담겨져서 전달.

 쿼리스트링 시작은 주소 뒷쪽에 ?(물음표)로 실행
 ? 키 = 값 & key값 = value
 => 쿼리스트링내용 순서가 중요하지 않음. 시퀀스가 없음.

// 자바스크립트기준.
 const obj = {"name" : "홍길동"} => 키값 : 벨류
// php 기준.
 $arr = [ "name" => "홍길동" ]; => 키값 : 벨류

-- get과 post방식의 차이점. 
 : get방식(쿼리스트링)은 보낼수있는 양이 정해져 있음.
 : post방식은 무제한.



 자바스크립트에서 자료를 저장하는 방식이 2가지가 있다.
 크게 2가지 => 변수를 사용할 것인가, 컬렉션을 사용할 것인가.
 // 변수사용 => 1개의 값만 저장.
 $a = 10;
 
 // 컬렉션 (배열, 여러개의 값을 저장.)
 $arr = [10,20,30]; =>방 마다 위치가 중요하기 때문에, 즉 순서가 중요하기 때문에
                        시퀀스가 있음.

 - 컬렉션 내 방식 2가지로 나뉨
    : sequence가 있나? 없나?
 - 시퀀스(sequence) = 시퀀스는 순서를 뜻함. => 순서가 있는지 없는지. 방식으로 나뉨.
 - 쿼리스트링 주소는 변경이 되어도 차이가 없음. 그래서 시퀀스가 없다고 함.
 
 
 
 
 - 배열은 2가지 저장방법이있다.
 1. ArrayList -> 배열스타일 => 
 -> 일자형으로 각 ㅁㅁㅁㅁ 이렇게 값들이 옆에 옆에 있다.
 -> 장점 
    : 만들기 쉽다 / 용량이 적다.(속도가 빠름 - select 속도) 
 -> 단점 
    : 많은 데이터양을 가지고 있을 땐, 중간에 값을 수정하기 힘듬.
:: linkedlist 보다 arraylist를 더 많이 사용한다!

 2. LinkedList -> node 스타일
 -> 칸을 두가지 가지고 있는데, 키값/벨류 각 한칸씩 가지고 있음.
 -> 만약 값을 늘리게 된다면 따로 생성되고, 기존 벨류값에 그 늘린 방을 넣어 연결을 시킴.
 -> 로직이 배열보다 복잠. 
 -> 읽을땐 복잡하지만, 많은 양일때 수정하기에 편리함.
 -> 장점
    : 수정에 용이함. (4번이면 수정가능.)
    => 중간에 만약 100을 넣는다고 한다면 첫번째 칸을 만들고 100이라는 값을 넣고 
        앞에 있는 메모리의 주소값을 100이 있는 메모리주소를 넣고, 
        100이 있는 메모리에 뒤에올 메모리 주소값을 value칸에 넣으면 끝이남. 
 -> 단점 
    : 변수에 저장되어 있을것인데 첫번째 메모리 찾아서 들어가면 또 두번째 메모리 찾을 것이고
      그렇게 된다면 여러번 거쳐서 들어가야하므로 많은 용량이 필요하고 
      select 속도가 느림.

-->