<div id="lData" data-toiuser="<?=$this->data->iuser?>"></div>
<div class="d-flex flex-column align-items-center">
    <div class="size_box_100"></div>
    <div class="w100p_mw614">
        <div class="d-flex flex-row">            
            <div class="d-flex flex-column justify-content-center me-3">
                <div class="circleimg h150 w150 pointer feedwin" >
                    <img class="profileimg" data-bs-toggle="modal" data-bs-target="#newProfileModal" src='/static/img/profile/<?=$this->data->iuser?>/<?=$this->data->mainimg?>' onerror='this.error=null;this.src="/static/img/profile/defaultProfileImg_100.gif"'>
                </div>
            </div>
            <div class="flex-grow-1 d-flex flex-column justify-content-evenly">
                <div><?=$this->data->email?>   
                 <!-- 
                if( $this->data->iuser === getIuser()) { ?>
                        <button type="button" id="btnModProfile" class="btn btn-outline-secondary">프로필 수정</button>
                 } else {                         
                        if ( $this->data->meyou) { ?>
                            <button type="button" id="btnFollow" data-follow="1" class="btn btn-outline-secondary">팔로우 취소</button>
                         } else if ($this->data->youme) { ?>
                                <button type="button" id="btnFollow" data-follow="0" class="btn btn-primary">맞팔로우</button>
                         } else { ?>
                                <button type="button" id="btnFollow" data-follow="0" class="btn btn-primary">팔로우</button>
                         } ?>
                 } ?>                    
                -->
                    <!-- PHP 형식으로 작성. //  -->
                    <?php if($this->data->iuser === getIuser()) {
                            echo '<button type="button" id="btnModProfile" class="btn btn-outline-secondary">프로필 수정</button>';
                        } else {    // 나도 안하고 너도 안했을 경우에만 팔로우
                            $data_follow = 0;
                            $cls = "btn-primary";
                            $txt = "팔로우";
                            // if($this->data->meyou === 1 && $this->data->youme === 1) {
                            //     $data_follow = 1;
                            //     $cls = "btn-outline-secondary";
                            //     $txt = "팔로잉";
                            //     echo "<button type='button' id='btnFollow' data-follow='{$data_follow}' class='btn {$cls}'>{$txt}</button>";
                            // }
                            
                            // 팔로우 아닌 경우에만 값이 바뀌도록 함.
                            if($this->data->meyou === 1) {  // 내가 팔로우 했다면, 무조건 팔로우 취소만 나오게 함.
                                $data_follow = 1;
                                $cls = "btn-outline-secondary";
                                $txt = "팔로우 취소";


                            } else if($this->data->youme === 1 && $this->data->meyou === 0) {  // 그게 아니라면 나만 안했다면 맞팔로우
                                $txt = "맞팔로우";
                            }
                            echo "<button type='button' id='btnFollow' data-follow='{$data_follow}' class='btn {$cls}'>{$txt}</button>";

                        }             
                    ?>
                </div>
                <div class="d-flex flex-row">
                    <div class="flex-grow-1 me-3">게시물 <span><?=$this->data->feedCnt?></span> </div>
                    <div class="flex-grow-1 me-3">팔로워 <span id="spanCntFollower" ><?=$this->data->followerCnt?></span> </div>
                    <div class="flex-grow-1">팔로우 <span><?=$this->data->followCnt?></span> </div>
                </div>
                <div class="bold"><?=$this->data->nm?></div>
                <div><?=$this->data->cmt?></div>
            </div>
        </div>
        <div id="container"></div>
    </div>
    <div class="loading d-none"><img src="/static/img/loading.gif"></div>
</div>
<!-- 프로필 변경창 -->
<!-- php 작성한 것은 로그인유저 피드에서만 모달창 뜨게 & 기본 프로필일 때 현재 사진 삭제 안뜨게   -->
<?php if($this->data->iuser === getIuser()) { ?>
<div class="modal fade" id="newProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                    <h5 class="modal-title bold">프로필 사진 바꾸기</h5>
                </div>
                <div class="_modal_item">
                    <span class="c_primary-button bold pointer">사진 업로드</span>
                </div>
                <?php if(isset($this->data->mainimg)) { ?>
                <div class="_modal_item">
                    <span id="btnDelCurrentProfilePic" class="c_error-or-destructive bold pointer">현재 사진 삭제</span>
                </div>
                <?php } ?>
                <div class="_modal_item">
                    <span class="pointer" id="btnProfileImgModalClose" data-bs-dismiss="modal">취소</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>