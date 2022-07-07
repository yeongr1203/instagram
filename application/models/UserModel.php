<?php
namespace application\models;
use PDO;


//$pdo -> lastInsertId();

class UserModel extends Model {
    public function insUser(&$param) {
        $sql = "INSERT INTO t_user
                ( email, pw, nm ) 
                VALUES 
                ( :email, :pw, :nm )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":email", $param["email"]);
        $stmt->bindValue(":pw", $param["pw"]);
        $stmt->bindValue(":nm", $param["nm"]);
        $stmt->execute();
        return $stmt->rowCount();

    }
    
    public function selUser(&$param) {
        $sql = "SELECT * FROM t_user
                WHERE email = :email";  // 받은 param을 집어 넣을 것이다.
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":email", $param["email"]);        
        $stmt->execute();   // 실행함.
        return $stmt->fetch(PDO::FETCH_OBJ);    // 실행 결과값을 리턴.
    }
    
    // public function selUserByIuser(&$param) {
    //     $sql = "SELECT iuser, email, nm, cmt, mainimg, regdt 
    //                FROM t_user
    //               WHERE iuser = :iuser";
    //     $stmt = $this->pdo->prepare($sql);
    //     $stmt->bindValue(":iuser", $param["iuser"]);
    //     $stmt->execute();
    //     return $stmt->fetch(PDO::FETCH_OBJ);
    // }

    // usercontroller에서 selUserByIuser 를 selUserProfile로 변경.
    public function selUserProfile(&$param) {
        $feediuser = $param["feediuser"];
        $loginiuser = $param["loginiuser"];
        $sql = 
        "SELECT iuser, email, nm, cmt, mainimg
                , (SELECT COUNT(ifeed) FROM t_feed WHERE iuser = {$feediuser}) AS feedCnt
                , (SELECT COUNT(fromiuser) FROM t_user_follow WHERE toiuser = {$feediuser} ) AS followerCnt
                , (SELECT COUNT(toiuser) FROM t_user_follow WHERE fromiuser = {$feediuser} ) AS followCnt
                , (SELECT COUNT(fromiuser) FROM t_user_follow WHERE fromiuser = {$feediuser} AND toiuser = {$loginiuser} ) AS youme
        		, (SELECT COUNT(fromiuser) FROM t_user_follow WHERE fromiuser = {$loginiuser} AND toiuser = {$feediuser} ) AS meyou
        FROM t_user
        WHERE iuser= {$feediuser}  
        ";
        // 물음표는 순서. 물음표 순서대로 값이 들어감.
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        // bindValue지우기고 아래행 실행하면 동일함.
        // $stmt->execute(array($param["iuser"],$param["iuser"]));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // 

    public function updUser(&$param) {
        $sql = 
            "UPDATE t_user
            SET moddt = now() ";
        if(isset($param["mainimg"])) {
            $mainimg = $param["mainimg"];
            $sql .= ", mainimg = '{$mainimg}'";
        }
        if(isset($param["delMainImg"])) {
            $sql .= ", mainimg = null";
        }
        
        $sql .= " WHERE iuser = :iuser ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":iuser", $param["iuser"]);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // -------------------------------- Follow ------------------------//
    public function insFollow($param) {
        $sql =
        "   INSERT INTO t_user_follow
            (fromiuser, toiuser)    
            -- 로그인한사람의 pk값, 팔로우하려는 사람의 pk값
            VALUES
            (:fromiuser, :toiuser)        
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":fromiuser", $param["fromiuser"]);
        $stmt->bindValue(":toiuser", $param["toiuser"]);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delFollow($param) {
        // 딜리트 방식으로 상대방pk 값을 날렸을 때, 팔로우 취소 해주면 됨. 
        $sql = 
        "   DELETE FROM t_user_follow
            WHERE fromiuser = :fromiuser
            AND toiuser = :toiuser
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":fromiuser", $param["fromiuser"]);
        $stmt->bindValue(":toiuser", $param["toiuser"]);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    // -------------------------------- Feed ------------------------//

    public function selFeedList(&$param) {
        // $iuser = $param["iuser"];
        $sql = 
        "   SELECT A.ifeed, A.location, A.ctnt, A.iuser, A.regdt
            , C.nm AS writer, C.mainimg
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
                WHERE iuser = :loginiuser
            ) F
            ON A.ifeed = F.ifeed
            WHERE c.iuser = :toiuser  -- where 절에 넣어도 되고, ON 넣어도 된다. 근데, 퍼포먼스 적으로 WHERE가 더 낫다.
            ORDER BY A.ifeed DESC
            LIMIT :starIdx, :feedItemCnt
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":toiuser", $param["toiuser"]);
        $stmt->bindValue(":loginiuser", $param["loginiuser"]);
        $stmt->bindValue(":starIdx", $param["starIdx"]);
        $stmt->bindValue(":feedItemCnt", _FEED_ITEM_CNT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function selFeedImgList($param) {    // 이때는 객체가 넘어옴.
        $sql = 
        "   SELECT img
            FROM t_feed_img
            WHERE ifeed = :ifeed
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt -> bindValue(":ifeed", $param->ifeed);
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_OBJ);
    }
}