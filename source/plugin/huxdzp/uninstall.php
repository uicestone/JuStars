<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE pre_hux_dzp_jp;
DROP TABLE pre_hux_dzp_jx;
DROP TABLE pre_hux_dzp_user;
DROP TABLE pre_hux_dzp_userjp;
DELETE FROM pre_common_cron WHERE `name` = 'HUXDZP';

EOF;

runquery($sql);

$finish = TRUE;

?>