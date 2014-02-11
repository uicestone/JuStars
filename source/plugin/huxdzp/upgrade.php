<?php

# Éý¼¶½Å±¾ 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF 

DROP TABLE IF EXISTS pre_hux_dzp_user;
CREATE TABLE IF NOT EXISTS pre_hux_dzp_user (
  `eid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `playnum` int(5) NOT NULL DEFAULT '0',
  `cjqnum` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eid`)
) ENGINE=MyISAM ;

EOF;

runquery($sql);

$finish = TRUE;

?>