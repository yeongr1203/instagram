<!DOCTYPE html>
<html lang="en">
<?php include_once "application/views/template/head.php"; ?>
<body>
    <div>
        <h1>회원가입</h1>

        <form action="signup" method="post">
            <!-- -->
            <div><input type="email" name="email" placeholder="email" autofocus required></div>
            <div><input type="password" name="pw" placeholder="password" required></div>
            <div><input type="text" name="nm" placeholder="name" required></div>
            <div>
                <input type="submit" value="회원가입">
            </div>
        </form>
    </div>
</body>
</html>
<!-- 

이 파일이 signup인데, 폼으로 날리는 곳도 signup!!
 : 파일주소 입력해서 여는 것(이 파일)은 겟방식(메소드)
 : 포스트 방식의 메소드
 : 유저컨트롤러에서 겟방식으로 하는지 포스트방식으로 하는지 분류할 것이고. 
  : 유틸스 안에 겟메소드에서 보면 겟방식으로 표현되어있다.

 -->