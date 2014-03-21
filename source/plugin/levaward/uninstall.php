<?php
/**
 *	Version: 1.0
 *	Date: 2013-8-16 22:44
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS `pre_lev_award`;
DROP TABLE IF EXISTS `pre_lev_award_join`;
DROP TABLE IF EXISTS `pre_lev_award_award`;
DROP TABLE IF EXISTS `pre_lev_award_award_log`;
DROP TABLE IF EXISTS `pre_lev_award_team_log`;

EOF;

runquery($sql);
$finish = true;
?>