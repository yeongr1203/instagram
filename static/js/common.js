
// 쿼리스트링 만들때, 내가 일일히 풀어 쓰지 않고 이것 하라로 끝!
function encodeQueryString(params) {
    const keys = Object.keys(params);   
    // params에 들어온 키값들만 객체(=자바스크립트에서는 배열)로 받아서 keys에 배열로 넣어줌.
    return keys.length  // keys // 아래 조건식 삼항식!! true는 ? 안이 실행.
            ? "?" + keys.map(key => // {}호 없이 바로 작성한다면 리턴의 의미가 있다.
                    encodeURIComponent(key) + "=" +
                    encodeURIComponent(params[key])
                ).join("&") // 호출후 바로 .(점) 사용했다는 것은 뭔가를 리턴을 해줬다는 것!
            : "";
}
// 특수기호, 한글만 "" 쌍따옴표사용.

function getDateTimeInfo(dt) {  // dt => 문자열 시간정보를 보내주면 now..에서 현재시간과 비교해주고. 아래 if문을 돌려 처리해줌.
    const nowDt = new Date();
    const targetDt = new Date(dt);

    const nowDtSec = parseInt(nowDt.getTime() * 0.001);
    const targetDtSec = parseInt(targetDt.getTime() * 0.001);    // / 1000 원래는 나누기 1000이었는데, 곱하기가 더 빠르기 때문에 곱셈으로 변경.
                        // 1000으로 곱하는 이유는 밀리센컨드가 보기 힘들어서 곱해서 초단위로 변경해주는것.

    const diffSec = nowDtSec - targetDtSec;
    if(diffSec < 120) {
        return '1분 전';
    } else if(diffSec < 3600) { //분 단위 (60 * 60)
        return `${parseInt(diffSec / 60)}분 전`;
    } else if(diffSec < 86400) { //시간 단위 (60 * 60 * 24)
        return `${parseInt(diffSec / 3600)}시간 전`;
    } else if(diffSec < 2592000) { //일 단위 (60 * 60 * 24 * 30)
        return `${parseInt(diffSec / 86400)}일 전`;
    }
    return targetDt.toLocaleString();
}