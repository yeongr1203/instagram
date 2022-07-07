<?php
namespace application\models;
use PDO;
// $pdo->lastInsert();

class FeedModel extends Model {
    public function insFeed(&$param) {
        $sql = 
        "   INSERT INTO t_feed
            (location, ctnt, iuser)
            VALUES
            (:location, :ctnt, :iuser)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":location", $param["location"]);
        $stmt->bindValue(":ctnt", $param["ctnt"]);
        $stmt->bindValue(":iuser", $param["iuser"]);
        $stmt->execute();
        return intval($this->pdo->lastInsertId());
    }

    public function insFeedImg(&$param) {
        // 이미지 갯수만큼 t_FeedImg안에 확장자를 뺀 랜덤이미지 넣기
        $sql = 
        "   INSERT INTO t_feed_img
            (ifeed, img)
            VALUES
            (:ifeed, :img)        
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":ifeed",$param["ifeed"]);
        $stmt->bindValue(":img",$param["img"]);
        $stmt->execute();
        return $stmt -> rowCount();
        // insert 확인용 -> 영향을 미친 레코드 수.
    }
    // ---------------------------- Feed -------------------------------

    // 피드 -> 좋아요
    public function selFeedList(&$param) {
        $sql = 
        "   SELECT A.ifeed, A.location, A.ctnt, A.iuser, A.regdt
            , C.nm AS writer, C.mainimg -- 글쓴이 mainimg
            , IFNULL(E.cnt, 0) AS favCnt
            , IF(F.ifeed IS NULL, 0, 1) AS isFav
            FROM t_feed A
            INNER JOIN t_user C
            ON A.iuser = C.iuser
            LEFT JOIN (
                SELECT ifeed, COUNT(ifeed) AS cnt	
                FROM t_feed_fav
                GROUP BY ifeed
            ) E
            ON A.ifeed = E.ifeed
            LEFT JOIN (						
                SELECT ifeed
                FROM t_feed_fav
                WHERE iuser = :iuser
            ) F
            ON A.ifeed = F.ifeed
            ORDER BY A.ifeed DESC
            LIMIT :starIdx, :feedItemCnt
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":iuser", $param["iuser"]);
        $stmt->bindValue(":starIdx", $param["starIdx"]);
        $stmt->bindValue(":feedItemCnt", _FEED_ITEM_CNT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /*    makeFeedItem에서 regdt, iuser, mainimg, writer 값을 받아서 등록 완료하면 자동으로 맨위에 나타나게 하기.
        새글 등록할 때, result=1이 넘어와서 화면이 변경하는것. 성공하는 정보만 받아왔음.
        그러지 않고 그 피드의 값을 바로 받아와서 makeFeedItem에 뿌려주면 거기서 받아와서 맨 위에 
        새로고침없이 자동으로 추가 하게 하는 것.    */
    // 즉, 새로고침없이 글 등록 되자마자 피드 맨 위에 나타남.
    public function selFeedAfterReg(&$param) {   // Reg는 registration
        $sql = 
        "   SELECT A.ifeed, A.location, A.ctnt, A.iuser, A.regdt
            , C.nm AS writer, C.mainimg 
            , 0 AS favCnt
            , 0 AS isFav
            FROM t_feed A
            INNER JOIN t_user C
            ON A.iuser = C.iuser
            WHERE A.ifeed = :ifeed
            ORDER BY A.ifeed DESC
            -- 1개만 가져오기 때문에 limit 필요 없음.
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":ifeed", $param["ifeed"]);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function selFeedImgList($param) {    // 이때는 객체가 넘어옴.
        $sql = 
        "   SELECT img
            FROM t_feed_img
            WHERE ifeed = :ifeed
        ";
        $stmt = $this->pdo->prepare($sql);
        // $stmt -> bindValue(":ifeed", $param->ifeed); // 선
        $stmt -> bindValue(":ifeed", $param["ifeed"]);  // 후 => 이렇게 변경한 이유는 객체라서(배열)
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_OBJ);
    }

    // ----------------------------Fav-------------------------
    // 피드 좋아요 누가 했는지.
    public function insFeedFav(&$param) {
        $sql =
        "   INSERT INTO t_feed_fav
            (ifeed, iuser)
            VALUES
            (:ifeed, :iuser)        
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":ifeed", $param["ifeed"]);
        $stmt->bindValue(":iuser", $param["iuser"]);
        $stmt->execute();
        return $stmt->rowCount();
    }
    // feed삭제
    public function delFeedFav(&$param) {
        $sql =
        "   DELETE FROM t_feed_fav
            WHERE   ifeed = :ifeed  
            AND iuser = :iuser
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":ifeed", $param["ifeed"]);
        $stmt->bindValue(":iuser", $param["iuser"]);
        $stmt -> execute();
        return $stmt->rowCount();
    }
    

}