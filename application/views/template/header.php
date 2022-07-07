<header class="header">
    <div class="container py-3"> <!-- 부트스트랩// ?의 y축에 ?만큼이동 => 1rem 을 뜻함. -->
        <!-- <h1>Header</h1>  작동하는지 확인함. -->
        <div id="globalConst">
            <div class="d-flex flex-row align-items-center">
                <div class="d-inline-flex flex-grow-1 flex-shrink-0">
                    <a href="/feed/index"><img src="/static/img/instagramtxtLogo.png" class="w150"></a>
                </div>
                <div class="d-inline-flex flex-grow-1 flex-shrink-1"></div>
                <div class="d-inline-flex flex-grow-1 flex-shrink-0">   <!-- flex-shrink-0  ?? -->
                    <nav class="d-flex flex-grow-1 flex-row justify-content-end">
                        <div class="d-inline-flex me-3">
                            <a href="#" id="btnNewFeedModal" data-bs-toggle="modal" data-bs-target="#newFeedModal">
                                <svg aria-label="새로운 게시물" class="_8-yf5 " color="#262626" fill="#262626" height="24" role="img" viewBox="0 0 24 24" width="24"><path d="M2 12v3.45c0 2.849.698 4.005 1.606 4.944.94.909 2.098 1.608 4.946 1.608h6.896c2.848 0 4.006-.7 4.946-1.608C21.302 19.455 22 18.3 22 15.45V8.552c0-2.849-.698-4.006-1.606-4.945C19.454 2.7 18.296 2 15.448 2H8.552c-2.848 0-4.006.699-4.946 1.607C2.698 4.547 2 5.703 2 8.552z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="6.545" x2="17.455" y1="12.001" y2="12.001"></line><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12.003" x2="12.003" y1="6.545" y2="17.455"></line></svg>
                            </a>
                        </div>
                        <div class="d-inline-flex me-3">
                            <a href="/dm/index">
                            <svg aria-label="다이렉트 메세지" class="_8-yf5 " color="#262626" fill="#262626" height="24" role="img" viewBox="0 0 24 24" width="24"><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2" x1="22" x2="9.218" y1="3" y2="10.083"></line><polygon fill="none" points="11.698 20.334 22 3.001 2 3.001 9.218 10.084 11.698 20.334" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></polygon></svg>
                            </a>
                        </div>

                        <div class="d-inline-flex dropdown">
                            <a href="#" role="button" id="navDropdownMenuLink" data-bs-toggle="dropdown" aria-expended="false" class="header_profile">
                                <!-- 프로필 이미지 / static폴더에 img / profile 폴더 생성 후 작성. -->
                                <div class="circleimg h30 w30">
                                <!-- <img src="/static/img/profile/<$_SESSION[_LOGINUSER]->iuser?>/$_SESSION[_LOGINUSER]->mainimg?>" onerror="this.onerror=null;this.src='/static/img/profile/defaultProfileImg_100.png'"> -->
                                    <!-- onerror는 on이 붙으면 실행. 에러가 터지면 실행한다!라는 뜻. this -> img태그를 뜻함.  -->
                                    <!-- onerror가 실행이됨. 실행이 될때, this에러값을 null로 변경.   null이 없다면 계속해서 무한루프가 돌기 때문에, null을 줌. -->
                                    <img class="profileimg" src="/static/img/profile/<?=getMainimg()?>" onerror="this.onerror=null; this.src='/static/img/profile/defaultProfileImg_100.gif'">
                                </div>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navDropdownMenuLink">
                                <li>
                                    <a class="dropdown-item" href="/user/feedwin?iuser=<?=getIuser()?>">
                                    <!-- getIuser()를 사용한 건 내 프로필로 가는 것이기 때문. -->
                                        <span><svg aria-label="프로필" class="_8-yf5 " color="#262626" fill="#262626" height="16" role="img" viewBox="0 0 24 24" width="16"><circle cx="12.004" cy="12.004" fill="none" r="10.5" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"></circle><path d="M18.793 20.014a6.08 6.08 0 00-1.778-2.447 3.991 3.991 0 00-2.386-.791H9.38a3.994 3.994 0 00-2.386.791 6.09 6.09 0 00-1.779 2.447" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"></path><circle cx="12.006" cy="9.718" fill="none" r="4.109" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"></circle></svg></span>
                                        <span>프로필</span>
                                    </a>                                
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/user/logout">로그아웃</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- New Feed Create Modal --> <!-- header 새로운게시물과 연결-->
<div class="modal fade" id="newFeedModal" tabindex="-1" aria-labelledby="newFeedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" id="newFeedModalContent">
            <div class="modal-header">
                <h5 class="modal-title" id="newFeedModalLabel">새 게시물 만들기</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="id-modal-body"></div>
        </div>

        <form class="d-none"> <!-- d-none 지우면 등록할때 파일추가가 뜸.-->
            <input type="file" name="imgs" accept="image/*" multiple>
        </form>
    </div>
</div>
<!-- 
    fade 는 스르륵 => 점점 커지는것.
    tabindex 는 탭을 누르면 커서 위치
 -->





<!-- 
d-flex => 디스플레이 플렉스
flex-column => 플렉스방향이 컬럼 (정방향)
felx-md-row => 크로스방향이 세로
align-items-center => 세로방향 (세로를 센터로 주겠다.)

d-inline-flex = 디스플레이 인라인플렉스 줌.
flex-grow-1 => 
flex-shrink-0 => 화면을 줄어들었을때를 말하는것. 0이기 때문에 신경 안씀.



d-flex 
flex-grow-1 =>
flex-coumn => 컬럼방향(가로방향)
flex-md-row =>
justify-content-end =>오른쪽 정렬.

 me-3 => margin 3?
 -->
<!-- 
    비트맵 => 보통은 사진 // 사진은 확대하면 깨짐.
    벡터 (확대해도 깨지지 않음.) => 아이콘.
    svg search icon 검색. => https://www.svgrepo.com/vectors/add/ 들어감
    => add 검색 원하는 것들어가서 파일 다운로드. 열기 내용 복사 붙여넣기함.
-->