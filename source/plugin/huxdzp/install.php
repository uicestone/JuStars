<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS pre_hux_dzp_jp;
CREATE TABLE IF NOT EXISTS pre_hux_dzp_jp (
  `jpid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jid` int(2) NOT NULL DEFAULT '0',
  `jpname` varchar(20) NOT NULL DEFAULT 'NoName',
  `jptype` tinyint(1) NOT NULL DEFAULT '0',
  `jfnum` varchar(200) NOT NULL DEFAULT '0',
  `jpnum` int(10) NOT NULL DEFAULT '0',
  `jppic` varchar(100) DEFAULT NULL,
  `jptxt` text,
  PRIMARY KEY (`jpid`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS pre_hux_dzp_jx;
CREATE TABLE IF NOT EXISTS pre_hux_dzp_jx (
  `jid` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `jorder` int(2) NOT NULL DEFAULT '0',
  `jname` varchar(10) NOT NULL,
  `jweight` int(10) NOT NULL DEFAULT '0',
  `jshow` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`jid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 ;

DROP TABLE IF EXISTS pre_hux_dzp_user;
CREATE TABLE IF NOT EXISTS pre_hux_dzp_user (
  `eid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `playnum` int(5) NOT NULL DEFAULT '0',
  `cjqnum` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eid`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS pre_hux_dzp_userjp;
CREATE TABLE IF NOT EXISTS pre_hux_dzp_userjp (
  `eid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jxname` varchar(10) NOT NULL,
  `jpname` varchar(20) NOT NULL,
  `jpstate` tinyint(1) NOT NULL DEFAULT '0',
  `sqstate` tinyint(1) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0',
  `username` char(15) NOT NULL,
  `jptype` tinyint(1) NOT NULL DEFAULT '0',
  `jfnum` varchar(200) NOT NULL DEFAULT '0',
  `zjtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jshow` tinyint(1) NOT NULL DEFAULT '0',
  `jinfo` text,
  PRIMARY KEY (`eid`)
) ENGINE=MyISAM ;

INSERT INTO pre_hux_dzp_jx (`jid`, `jorder`, `jname`, `jweight`) VALUES
(1, 0, 'no1', '1'),
(2, 1, 'no2', '2'),
(3, 2, 'no3', '3'),
(4, 3, 'no4', '5'),
(5, 4, 'no5', '10'),
(6, 5, 'no6', '20'),
(7, 6, 'no7', '50'),
(8, 7, 'no8', '100'),
(9, 8, 'no9', '300'),
(10, 9, 'no10', '500'),
(11, 10, 'no11', '1000'),
(12, 11, 'no12', '2000');

INSERT INTO pre_common_cron (`available`, `type`, `name` , `filename` ,`lastrun` ,`nextrun` ,`weekday`, `day`, `hour` , `minute`) VALUES
('1','user', 'HUXDZP', 'hux_dzp.php' , '1287466825' ,'1287504000' ,'-1','-1','0','0');
EOF;

runquery($sql);

$finish = TRUE;
?>