// const url = new URL(location.href);

if(feedObj) {
    const url = new URL(location.href);
    feedObj.iuser  = parseInt(url.searchParams.get('iuser'));
    feedObj.getFeedUrl = '/user/feed';
    feedObj.getFeedList();
}
/*  
// common_feed.js 에 추가 하고 지움.
function getFeedList() {
    if(!feedObj) { return; }    // 
    feedObj.showLoading();            
    const param = {
        page: feedObj.currentPage++,
        iuser : url.searchParams.get('iuser')
        // index.js 랑 iuser 이 것 빼고는 비슷함.
    }
    fetch('/user/feed' + encodeQueryString(param))
    .then(res => res.json())
    .then(list => {                
        feedObj.makeFeedList(list);                
    })
    .catch(e => {
        console.error(e);
        feedObj.hideLoading();
    });
}
getFeedList();
*/


(function() {
    const spanCntFollower = document.querySelector('#spanCntFollower'); // 팔로우 할때 접근할 수 있는 주소값 담기.

    const lData = document.querySelector('#lData');

    // 현재 프로필사진 
    const btnDelCurrentProfilePic = document.querySelector('#btnDelCurrentProfilePic');
    const btnProfileImgModalClose = document.querySelector('#btnProfileImgModalClose');


    const btnFollow = document.querySelector('#btnFollow');
    if(btnFollow) { // 타인의 feed에 왔다는 것.
        btnFollow.addEventListener('click', function() {
            const param = {
                toiuser: parseInt(lData.dataset.toiuser)
            };
            console.log(param);
            // console.log('이벤트 클릭');  // 처음 먼저 찍어서 값 나오는지 확인.
            const follow = btnFollow.dataset.follow;
            console.log('follow :' + follow);
            const followUrl = '/user/follow';
            switch(follow) {
                case '1':   // 팔로우 취소
                    // fetch(followUrl);
                    fetch(followUrl + encodeQueryString(param),{method: 'DELETE'})
                        // get, delete는 encodeQueryString(queryString)으로 날림.
                    .then(res => res.json())
                    .then(res => {
                        // console.log('res : ' + res);
                        if('res:'+res){
                            // 팔로워 바로(실시간) 숫자 변경. == 선생님 풀이
                            const cntFollowerVal = ~~(spanCntFollower.innerText);   //~~은 정수 = parseInt와 같음.
                            spanCntFollower.innerText = cntFollowerVal -1;

                            btnFollow.dataset.follow = '0';
                            btnFollow.classList.remove('btn-outline-secondary');
                            btnFollow.classList.add('btn-primary');
                            if(btnFollow.dataset.youme === '1'){                                
                                btnFollow.innerText = '맞팔로우';
                            }
                            btnFollow.innerText = '팔로우';

                            // 팔로워 실시간 수정   // 내 풀이
                            // const feedUserfollow = document.querySelector('#feedUserfollow');
                            // const cnt = feedUserfollow.innerHTML;
                            // feedUserfollow.innerHTML = parseInt(cnt) -1;
                        }
                    });
                    break;
                case '0':   // 팔로우 등록
                    fetch(followUrl, {
                        method: 'POST',
                        body: JSON.stringify(param)
                        // put, post stringify를 주로 사용.
                    })
                    .then(res=> res.json())
                    .then(res => {
                        if(res.result){
                            // 팔로워 바로(실시간) 숫자 변경. == 선생님 풀이
                            const cntFollowerVal = ~~(spanCntFollower.innerText);   //~~은 정수 = parseInt와 같음.
                            spanCntFollower.innerText = cntFollowerVal +1;

                            btnFollow.dataset.follow = '1';
                            btnFollow.classList.remove('btn-primary');
                            btnFollow.classList.add('btn-outline-secondary');
                            if(btnFollow.dataset.youme === '1' && btnFollow.dataset.meyou === '0'){
                                btnFollow.innerText = '맞팔로우';
                            }                            
                            btnFollow.innerText = '팔로우 취소';

                            // 팔로우 숫자 변경되는 것 -- 취소 // 내풀이
                            // const feedUserfollow = document.querySelector('#feedUserfollow');
                            // const cnt = feedUserfollow.innerHTML;
                            // feedUserfollow.innerHTML = parseInt(cnt) +1;
                        }
                    });
                    break;
            }
        });
    }

    if(btnDelCurrentProfilePic) {
        btnDelCurrentProfilePic.addEventListener('click', e => {
            fetch('/user/profile', {method: 'DELETE'})
            .then(res => res.json())
            .then(res => {
                if(res.result) {
                    const profileImgList = document.querySelectorAll('.profileimg');
                    profileImgList.forEach(item => {
                        item.src = '/static/img/profile/defaultProfileImg_100.gif';
                    });
                }
                btnProfileImgModalClose.click();
            })
        });
    }
})();