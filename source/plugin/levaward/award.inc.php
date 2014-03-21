<?php

/**
 * 
 *
 * Copyright (c) 2013-2014 
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once 'lev_enter.php';//print_r($_G);

if (!defined('TPL_DEFAULT')) define('TPL_DEFAULT', TRUE);

$doingid = intval($_GET['doingid']);
$_in_mobile = intval($_GET['in_mobile']);

$doings = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$doingid");
	
if ($_GET['hook']) {
	if (empty($doings)) {
		$doingtid = intval($_GET['tid']) >0 ? intval($_GET['tid']) : exit($lev_lang['nodoing']);
		$doings = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE tid=$doingtid");
		if (empty($doings)) {
			if ($_GET['hook']) exit();
			showmessage($lev_lang['nodoing']);
		}
		$doingid = $doings['id'];
	}
	$insql  = lev_base::sqlinstr($doings['awards']);
	if ($insql) {
		$sql = "SELECT * FROM ".DB::table('lev_award_award')." WHERE id IN ($insql) ORDER BY listorder ASC, id DESC";
		$awards = DB::fetch_all($sql);
	}
	
	$awardlogs = lev_class::awardlogs($doingid);
	$awardbigs = lev_class::awardlogs($doingid, 0, 1);
	
	$isawardtips = $noawardtips = '';
	$_isawardtips = explode("\n", trim($_PLG['isawardtips']));
	foreach ($_isawardtips as $k => $r) {
		$r = addslashes(trim($r));
		$isawardtips .= <<<EOF
	isawardtips[$k] = '$r';
	
EOF;
	}
	$_noawardtips = explode("\n", trim($_PLG['noawardtips']));
	foreach ($_noawardtips as $k => $r) {
		$r = addslashes(trim($r));
		$isawardtips .= <<<EOF
	noawardtips[$k] = '$r';
	
EOF;
	}
	
	$myjoins = DB::fetch_first("SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid='$doingid' AND uid='{$_G['uid']}'");
	$mycards = unserialize($myjoins['tasks']);
		
	include template($PLNAME.':award');
}else {
 	if (lev_base::isopen('widthauto')) $_G['widthauto'] = $_G['setting']['allowwidthauto'] = 1;
 	$doingtmps = (array)unserialize($doings['tasks']);
 	$tmpdirs = DISCUZ_ROOT.'source/plugin/levaward/template/';
 	$specialtmp = 'systype_page_'.$doings['systype'];
 	if ((!$doingtmps['doing_tmp'] || $doingtmps['doing_tmp'] =='special') && is_file($tmpdirs.$specialtmp.'.htm')) {
 		$diytmp = $specialtmp;
 	}elseif ($doingtmps['doing_tmp'] && $doingtmps['doing_tmp'] !='default' && is_file($tmpdirs.$doingtmps['doing_tmp'].'.htm')) {
 		$diytmp = $doingtmps['doing_tmp'];
 	}else {
 		$diytmp = 'award_page';
 	}
 	if (checkmobile() && is_file($tmpdirs.'systype_'.$doings['systype'].'_mobile.htm')) {
 		$_in_mobile = 2;
		include template($PLNAME.':award_page_mobile');
 	}else {
 		include template('diy:'.$diytmp, '', $diydir);
 	}
}


















