const feedObj = {
    limit: 20,
    itemLength: 0,
    currentPage: 1,
    swiper: null,
    // getFeedUrl, iuser, getFeedList 이 3가지는 follower숫자 수정 이후 작업하면서 추가.
    getFeedUrl: '',
    iuser: 0, 
    setScrollInfinity: function() {
        window.addEventListener('scroll', e => {
            const { // const 안에 있는 아이는 멤버필드에서 내가 사용하려는 변수명.
                scrollTop,
                scrollHeight,
                clientHeight
            } = document.documentElement;   
            /* 
                이 기능은 자바스크립트만 가능. 자바는 안됨. 
                document.socumentElement 를 console로 찍으면 값이 나타남.
                const scrollTop = document.documentElement.scrollTop(scrollTop자리는 멤버필드자리); 하는 것과 동일.
                document.documentElement.멤버필드명 = 변수명 => 값을 나타내려는 멤버필드명과 변수명은 동일해야한다.
            */
           // clientHeight = 세로 스크롤 길이 , scrollHight = 전체높이
           // scrollTop + clientHeight >= scrollHight -5 => 전체에서 -5보다 커지게 되면
           // getFeedList통신할 때 마다 다음페이지가 있는지 없는지 까지 확인.
           if( scrollTop + clientHeight >= scrollHeight -5 && this.itemLength === this.limit ) {
                this.getFeedList();
           }

        }, {passive:true}); // passive 이걸 사용한 이유는 퍼포먼스를 위해서 사용함. 
        // passive 의미는 더 찾아보기. 
    }, 
    getFeedList: function() { 
        this.itemLength = 0; // feed 수 => 인피니티 스크롤하려고
        this.showLoading();            
        const param = {
            page: this.currentPage++,
            iuser: this.iuser
        }
        fetch(this.getFeedUrl + encodeQueryString(param))
        .then(res => res.json())
        .then(list => {              
            this.itemLength = list.length;  // 인피니티 스크롤 생성시 확인   
            this.makeFeedList(list);                
        })
        .catch(e => {
            console.error(e);
            this.hideLoading();
        });
    },
    refreshSwipe: function() {
        if(this.swiper !== null) { this.swiper = null; }
        this.swiper = new Swiper('.swiper', {
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },
            pagination: { el: '.swiper-pagination' },
            allowTouchMove: false,
            direction: 'horizontal',
            loop: false
        });
    },
    loadingElem: document.querySelector('.loading'),
    containerElem: document.querySelector('#container'),
    getFeedCmtList: function(ifeed, divCmtList, spanMoreCmt) {        
        fetch(`/feedcmt/index?ifeed=${ifeed}`)  // fetch에서 ifeed값을 불러옴.
        .then(res=>res.json())  // json으로 실행.
        .then(res => {
            if(res && res.length > 0) { 
                // 만약 댓글 수(길이)가 0 이상이면 실행하지 않음.
                if(spanMoreCmt) { spanMoreCmt.remove();}    
                // 댓글이 0이라면 remove할 필요없고, 댓글이 remove 되는건 1개이상일 경우에 실행하겠다.
                divCmtList.innerHTML = null;    // null을 만들어주고
                res.forEach(item => {   // 더보기를 클릭하면
                    const divCmtItem = this.makeCmtItem(item);  // 그 자리에 한줄씩 뿌려주겠다.
                    divCmtList.appendChild(divCmtItem);
                });
            }
        });
    },

    makeCmtItem: function(item) {
        const divCmtItemContainer = document.createElement('div');
        divCmtItemContainer.className = 'd-flex flex-row align-items-center mb-2';
        const src = '/static/img/profile/' + (item.writerimg ? `${item.iuser}/${item.writerimg}` : 'defaultProfileImg_100.gif');
        divCmtItemContainer.innerHTML = `
            <div class="circleimg h24 w24 me-1 feedcmtuser">
                <img class="profileimg" src="${src}" class="profile w24 pointer">
            </div>
            <div class="d-flex flex-row">
                <div class="pointer me-2 feedcmtuser"> ${item.writer}</div>
                <div>${item.cmt} - <span class="rem0_7">${getDateTimeInfo(item.regdt)}</span> </div>
                <div></div>
            </div>
        `;
        // 댓글 쓴 사람의 프로필로 이동.
        const feedcmtuser = divCmtItemContainer.querySelectorAll('.feedcmtuser');
        feedcmtuser.forEach(el => {
            el.addEventListener('click', e => {
                location.href = `/user/feedwin?iuser=${item.iuser}`;
            });          
        });

        /* 선생님풀이 - 아래 */
        // const img = divCmtItemContainer.querySelectorAll('.feedcmtuser');
        // img.addEventListener('click', e => {
        //     moveToFeedWin(iuser);
        // });
        return divCmtItemContainer;
    }, 
    
    // feed이미지 
    makeFeedList: function(list) {        
        if(list.length !== 0) {
            list.forEach(item => {
                const divItem = this.makeFeedItem(item);
                this.containerElem.appendChild(divItem);
            });
        }
        
        this.refreshSwipe();    
        this.hideLoading();
    },

    // 새 글 등록
    makeFeedItem: function(item) {
        console.log(item);
        const divContainer = document.createElement('div');
        divContainer.className = 'item mt-3 mb-3';
        
        const divTop = document.createElement('div');
        divContainer.appendChild(divTop);

        const regDtInfo = getDateTimeInfo(item.regdt);
        divTop.className = 'd-flex flex-row ps-3 pe-3';
        const writerImg = `<img class="profileimg" src='/static/img/profile/${item.iuser}/${item.mainimg}' 
            onerror='this.error=null;this.src="/static/img/profile/defaultProfileImg_100.gif"'>`;

        divTop.innerHTML = `
            <div class="d-flex flex-column justify-content-center">
                <div class="circleimg h40 w40 pointer feedwin">${writerImg}</div>
            </div>
            <div class="p-3 flex-grow-1">
                <div><span class="pointer feedwin">${item.writer}</span> - ${regDtInfo}</div>
                <div>${item.location === null ? '' : item.location}</div>
            </div>
        `;

        const feedwinList = divTop.querySelectorAll('.feedwin');
        feedwinList.forEach(el => {
            el.addEventListener('click', () => {
                moveToFeedWin(item.iuser);
            });
        });

        const divImgSwiper = document.createElement('div');
        divContainer.appendChild(divImgSwiper);
        divImgSwiper.className = 'swiper item_img';
        divImgSwiper.innerHTML = `
            <div class="swiper-wrapper align-items-center"></div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        `;
        const divSwiperWrapper = divImgSwiper.querySelector('.swiper-wrapper');
                    
        item.imgList.forEach(function(imgObj) {
            const divSwiperSlide = document.createElement('div');
            divSwiperWrapper.appendChild(divSwiperSlide);
            divSwiperSlide.classList.add('swiper-slide');

            const img = document.createElement('img');
            divSwiperSlide.appendChild(img);
            img.className = 'w100p_mw614';
            img.src = `/static/img/feed/${item.ifeed}/${imgObj.img}`;
        });

        const divBtns = document.createElement('div');
        divContainer.appendChild(divBtns);
        divBtns.className = 'favCont p-3 d-flex flex-row';

        /* // 내풀이
            const divFav ... ~ if(item.favCnt > 0)..  까지의 위치
            통신 위에 있어서 작동이 되기 때문에 아래에서 위로 올려줌.
            const divFav = document.createElement('div');
            divContainer.appendChild(divFav);
            divFav.className = 'p-3 d-none';
            const spanFavCnt = document.createElement('span');
            divFav.appendChild(spanFavCnt);
            spanFavCnt.className = 'bold';
            기존에 좋아요가 있다면 출력되어야 하기 때문에 default로 미리 설정함.
            spanFavCnt.innerHTML = `좋아요 ${item.favCnt}개`;
            if(item.favCnt > 0) { divFav.classList.remove('d-none'); }
        */
        const heartIcon = document.createElement('i');
        divBtns.appendChild(heartIcon);
        heartIcon.className = 'fa-heart pointer rem1_5 me-3';
        heartIcon.classList.add(item.isFav === 1 ? 'fas' : 'far');
        heartIcon.addEventListener('click', e => {
            
            let method = 'POST';
            if(item.isFav === 1) { //delete (1은 0으로 바꿔줘야 함)
                method = 'DELETE';
            }

            fetch(`/feed/fav/${item.ifeed}`, {
                'method': method,
            }).then(res => res.json())
            .then(res => {                    
                if(res.result) {
                    item.isFav = 1 - item.isFav; // 0 > 1, 1 > 0
                    if(item.isFav === 0) { // 좋아요 취소
                        /*내풀이
                            item.favCnt = item.favCnt -1;
                            if(item.favCnt >0){
                                spanFavCnt.innerHTML = `좋아요 ${item.favCnt}개`;
                            } else {    // item.favCnt 값이 0일 경우, 
                                divFav.classList.add('d-none');
                                // 좋아요 0개 없애줘야하기 때문에. display를 none이 되도록 해준다.
                            }
                            heartIcon.classList.remove('fas');
                            heartIcon.classList.add('far');
                            */
                           // 선생님 풀이
                        heartIcon.classList.remove('fas');
                        heartIcon.classList.add('far');
                        item.favCnt --;
                        if(item.favCnt === 0){
                            divFav.classList.add('d-none');
                        }
                        
                    } else { // 좋아요 처리
                        /*  // 내풀이
                            heartIcon.classList.remove('far');
                            heartIcon.classList.add('fas');
                            
                            좋아요를 누르기 때문에 +1을 추가 해주고 그 추가된 갯수를 출력.
                            item.favCnt = item.favCnt +1;
                            spanFavCnt.innerHTML = `좋아요 ${item.favCnt}개`;
                            divFav.classList.remove('d-none');
                            숨겨둔 divFav를 d-none을 remove(없애기) 해준다.
                        */
                        //선생님풀이
                        heartIcon.classList.remove('far');
                        heartIcon.classList.add('fas');
                        item.favCnt ++;
                        divFav.classList.remove('d-none');
                    }
                    spanFavCnt.innerHTML = `좋아요 ${item.favCnt}개`;
                    // alert('좋아요를 할 수 없습니다.');   // 내풀이 
                } else {
                    alert('좋아요를 할 수 없습니다.');  // 내풀이 할때 else부분 지우기
                }
            })
            .catch(e => {
                alert('네트워크에 이상이 있습니다.');
            });
        });

        const divDm = document.createElement('div');
        divBtns.appendChild(divDm);
        divDm.className = 'pointer';
        divDm.innerHTML = `<svg aria-label="다이렉트 메시지" class="_8-yf5 " color="#262626" fill="#262626" height="24" role="img" viewBox="0 0 24 24" width="24"><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2" x1="22" x2="9.218" y1="3" y2="10.083"></line><polygon fill="none" points="11.698 20.334 22 3.001 2 3.001 9.218 10.084 11.698 20.334" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></polygon></svg>`;

        //  const divFav ~ if(item.favCnt > 0) 까지 내풀이에서는 
        // 통신 위로 올림...
        const divFav = document.createElement('div');
        divContainer.appendChild(divFav);
        divFav.className = 'p-3 d-none';
        const spanFavCnt = document.createElement('span');
        divFav.appendChild(spanFavCnt);
        spanFavCnt.className = 'bold';
        spanFavCnt.innerHTML = `좋아요 ${item.favCnt}개`;
        if(item.favCnt > 0) { divFav.classList.remove('d-none'); }


        if(item.ctnt !== null && item.ctnt !== '') {
            const divCtnt = document.createElement('div');
            divContainer.appendChild(divCtnt);
            divCtnt.innerText = item.ctnt;
            divCtnt.className = 'itemCtnt p-3';
        }

        const divCmtList = document.createElement('div');
        divContainer.appendChild(divCmtList);
        divCmtList.className = 'ms-3';

        // 댓글 내용        
        const divCmt = document.createElement('div');
        divContainer.appendChild(divCmt);     

        const spanMoreCmt = document.createElement('span');

        if(item.cmt) {
            const divCmtItem = this.makeCmtItem(item.cmt);
            divCmtList.appendChild(divCmtItem);

            if(item.cmt.ismore === 1) {                
                const divMoreCmt = document.createElement('div');
                divCmt.appendChild(divMoreCmt);
                divMoreCmt.className = 'ms-3 mb-3';
    
                divMoreCmt.appendChild(spanMoreCmt);
                spanMoreCmt.className = 'pointer rem0_9 c_lightgray';
                spanMoreCmt.innerText = '댓글 더보기';
                spanMoreCmt.addEventListener('click', e => {
                    // fetch(`/feedcmt/index?ifeed=${item.ifeed}`)
                    // .then(res=>res.json())
                    // .then(res => {
                    //     if(res && res.length > 0) {
                    //         spanMoreCmt.remove();
                    //         divCmtList.innerHTML = null;
                    //         res.forEach(item => {
                    //             const divCmtItem = this.makeCmtItem(item);
                    //             divCmtList.appendChild(divCmtItem);
                    //         });
                    //     }
                    // });   
                    
                    // 상단 패치를 js 맨위에 함수화 시켜 작성 후 바로 아래에 호출해서 바로 사용.
                    this.getFeedCmtList(item.ifeed, divCmtList, spanMoreCmt);           
                
                });
            }
        }
        
        const divCmtForm = document.createElement('div');
        divCmtForm.className = 'd-flex flex-row';     
        divCmt.appendChild(divCmtForm);

        divCmtForm.innerHTML = `
            <input type="text" class="flex-grow-1 my_input back_color p-2" placeholder="댓글을 입력하세요...">
            <button type="button" class="btn btn-outline-primary">등록</button>
        `;
        
        // 댓글 작성 영역!!
                // 댓글 등록
        const inputCmt = divCmtForm.querySelector('input');
        // 댓글 내용 입력하고 엔터만 쳐도 댓글 등록이 됨.
        inputCmt.addEventListener('keyup', e => {
            if(e.key === 'Enter') {
                btnCmtReg.click();
            }
        });

        

        const btnCmtReg = divCmtForm.querySelector('button');
        btnCmtReg.addEventListener('click', e => {
            // enter를 쳤을 때, 이 method를 실행 시키겠다.
            // 빼는 이유는 편하게 사용하려고. enter쳤을때도 실행 되도록!!

            const param = {
                ifeed: item.ifeed,
                cmt: inputCmt.value
            };
            fetch('/feedcmt/index', {
                method: 'POST',
                body: JSON.stringify(param)
            })
            .then(res=>res.json())
            .then(res => {
                // console.log('icmt : ' + res.result);
                if(res.result) {
                    inputCmt.value = '';
                    // 댓글 공간에 댓글 내용 추가
                    // 댓글이 한개 이상인지에 대한 정보도 가져옴.
                    // 댓글이 1개면 1개만 가지고오고 1개이상이면 모두 가져오기.
                    this.getFeedCmtList(param.ifeed, divCmtList, spanMoreCmt);                    
                } else {    // else 영역은 댓글 공백 입력 안되게 처리
                    inputCmt.value = ''; 
                    alert("댓글을 등록 할 수 없습니다.");
                }
            })
        });

        return divContainer;
    },

    showLoading: function() { this.loadingElem.classList.remove('d-none'); },
    hideLoading: function() { this.loadingElem.classList.add('d-none'); }

}

// 원래자리 요기
function moveToFeedWin(iuser) {
    location.href = `/user/feedwin?iuser=${iuser}`;
}


(function() {
    const btnNewFeedModal = document.querySelector('#btnNewFeedModal');
    if(btnNewFeedModal) {
        const modal = document.querySelector('#newFeedModal');
        const body =  modal.querySelector('#id-modal-body');
        const frmElem = modal.querySelector('form');
        const btnClose = modal.querySelector('.btn-close');
        //이미지 값이 변하면
        frmElem.imgs.addEventListener('change', function(e) {
            console.log(`length: ${e.target.files.length}`);
            if(e.target.files.length > 0) {
                body.innerHTML = `
                    <div>
                        <div class="d-flex flex-md-row">
                            <div class="flex-grow-1 h-full"><img id="id-img" class="w300"></div>
                            <div class="ms-1 w250 d-flex flex-column">                
                                <textarea placeholder="문구 입력..." class="flex-grow-1 p-1"></textarea>
                                <input type="text" placeholder="위치" class="mt-1 p-1">
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary">공유하기</button>
                    </div>
                `;
                const imgElem = body.querySelector('#id-img');

                const imgSource = e.target.files[0];
                const reader = new FileReader();
                reader.readAsDataURL(imgSource);
                reader.onload = function() {
                    imgElem.src = reader.result;
                };

                const shareBtnElem = body.querySelector('button');
                shareBtnElem.addEventListener('click', function() {
                    const files = frmElem.imgs.files;

                    const fData = new FormData();
                    for(let i=0; i<files.length; i++) {
                        fData.append('imgs[]', files[i]);
                    }
                    fData.append('ctnt', body.querySelector('textarea').value);
                    fData.append('location', body.querySelector('input[type=text]').value);

                    fetch('/feed/rest', {
                        method: 'post',
                        body: fData                       
                    }).then(res => res.json())
                        .then(myJson => {
                           console.log(myJson);

                           if(myJson) {                                
                                btnClose.click();

                                // 남의 feedWin에서 새 글 등록 -> 메인 feed에서만 보임.
                               const lData = document.querySelector('#lData');
                               const gData = document.querySelector('#gData');
                                if(lData && lData.dataset.toiuser !== gData.dataset.loginiuser ) {
                                    return;
                                }

                                // 남의 feedWin이라면 화면에 등록!
                                const feedItem = feedObj.makeFeedItem(myJson);
                                feedObj.containerElem.prepend(feedItem);
                                feedObj.refreshSwipe();
                                window.scrollTo(0,0);
                                // scrollTo => 사용이유? 새 피드 등록하면 맨위로 이동되도록 하는 것.
                           }
                        });
                        
                });
            }
        });

        btnNewFeedModal.addEventListener('click', function() {
            const selFromComBtn = document.createElement('button');
            selFromComBtn.type = 'button';
            selFromComBtn.className = 'btn btn-primary';
            selFromComBtn.innerText = '컴퓨터에서 선택';            
            selFromComBtn.addEventListener('click', function() {
                frmElem.imgs.click();
            });
            body.innerHTML = null;
            body.appendChild(selFromComBtn);
        });
    }

})();