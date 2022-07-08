CREATE database phpgram;

CREATE TABLE t_user(
   iuser INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
   email VARCHAR(50) NOT NULL,   
   pw VARCHAR(100),
   nm VARCHAR(5) NOT NULL,
   cmt VARCHAR(50) DEFAULT '' COMMENT '코멘트',
   mainimg VARCHAR(50),
   regdt DATETIME DEFAULT NOW(),
   UNIQUE(email)
);

CREATE TABLE `t_user_follow` (
    `fromiuser` INT UNSIGNED NOT NULL,
    `toiuser` INT UNSIGNED NOT NULL,
    `regdt` DATETIME NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`fromiuser`, `toiuser`) USING BTREE,
    FOREIGN KEY (`fromiuser`) REFERENCES `t_user` (`iuser`),
    FOREIGN KEY (`toiuser`) REFERENCES `t_user` (`iuser`)
);

CREATE TABLE `t_user_img` (
    `iuser` INT(20) UNSIGNED NOT NULL,
    `img` VARCHAR(50) NOT NULL,
    `regdt` DATETIME NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`iuser`, `img`),
    FOREIGN KEY (`iuser`) REFERENCES `t_user` (`iuser`)
);

CREATE TABLE t_feed(
   ifeed INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
   location VARCHAR(20),
   ctnt TEXT,
   iuser INT UNSIGNED NOT NULL,
   regdt DATETIME DEFAULT NOW(),
   FOREIGN KEY (iuser) REFERENCES t_user(iuser)
);

CREATE TABLE t_feed_img(
   ifeed INT UNSIGNED NOT NULL,
   img VARCHAR(50) NOT NULL,
   PRIMARY KEY(ifeed, img),
   FOREIGN KEY (ifeed) REFERENCES t_feed(ifeed)
);

CREATE TABLE t_feed_cmt(
   icmt INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
   ifeed INT UNSIGNED NOT NULL,
   iuser INT UNSIGNED NOT NULL,
   cmt VARCHAR(200) NOT NULL,
   regdt DATETIME DEFAULT NOW(),
   FOREIGN KEY (ifeed) REFERENCES t_feed(ifeed),
   FOREIGN KEY (iuser) REFERENCES t_user(iuser)
);

CREATE TABLE t_feed_fav(
   ifeed INT UNSIGNED,
   iuser INT UNSIGNED,
   regdt DATETIME DEFAULT NOW(),
   PRIMARY KEY(ifeed, iuser),
   FOREIGN KEY (ifeed) REFERENCES t_feed(ifeed),
   FOREIGN KEY (iuser) REFERENCES t_user(iuser)
);

CREATE TABLE t_dm(
    idm INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    regdt DATETIME DEFAULT NOW(),
    lastmsg VARCHAR(200) NOT NULL,
    lastdt DATETIME DEFAULT NOW()
);

CREATE TABLE t_dm_user(
    idm INT UNSIGNED,
    iuser INT UNSIGNED,
    PRIMARY KEY(idm, iuser),
    FOREIGN KEY(idm) REFERENCES t_dm(idm),
    FOREIGN KEY(iuser) REFERENCES t_user(iuser)
);

CREATE TABLE t_dm_msg(
     idm INT UNSIGNED,
     seq INT UNSIGNED,
     iuser INT UNSIGNED,
     msg VARCHAR(200) NOT NULL,
     regdt DATETIME DEFAULT NOW(),
     PRIMARY KEY(idm, seq),
     FOREIGN KEY(idm) REFERENCES t_dm(idm),
     FOREIGN KEY(iuser) REFERENCES t_user(iuser)
)