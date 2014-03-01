<?php
/**
 * Lev.levme.com
 *
 * Copyright (c) 2013-2014 http://www.levme.com All rights reserved.
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')){
	exit('Access Denied');
}

require_once 'lev_enter.php';

if (isset($_POST['dosubmit']) && $_POST['formhash'] ==FORMHASH) {
	$ids = daddslashes($_POST['ids']);
	if (count($ids)) {
		DB::query("DELETE FROM ".DB::table('lev_pop')." WHERE `id` IN (".dimplode($ids).")");
		cpmsg($lev_lang['succeed'], dreferer(), 'succeed');
	}
}
showformheader('plugins&operation=config&do=' . $pluginid . '&identifier='.$PLNAME.'&pmod=admin3', 'submit');
if (intval($_GET['isuid'])) {
	$uinfos = DB::fetch_first("SELECT * FROM ".DB::table('common_member_profile')." WHERE uid=".intval($_GET['isuid']));
	$struin = $lev_lang['uname']."UID: ".$uinfos['uid']."
	<br>".$lev_lang['realname'].": ".$uinfos['realname']."
	<br>".$lev_lang['address'].": ".$uinfos['address']."
	<br>".$lev_lang['zipcode'].": ".$uinfos['zipcode']."
	<br>".$lev_lang['tel'].": ".$uinfos['telephone']."
	<br>".$lev_lang['mobile'].": ".$uinfos['mobile']."
	<br>QQ: ".$uinfos['qq'];
}
showtableheader($struin);
showsubtitle(array (
	'<a href="javascript:;"><label for="chkall">'.$lev_lang['slts'].'</label></a>',
	'ID',
	$lev_lang['uname'],
	$lev_lang['awardscores'],
	$lev_lang['awardname'],
	$lev_lang['isgoodss'],
	$lev_lang['randnum'],
	$lev_lang['isawardnum'],
	$lev_lang['isfreeset'],
	$lev_lang['addtime'],
	'IP',
));

$page  = max(intval($_GET['page']), 1);
$limit = 20;

$total = DB::result_first("SELECT COUNT(*) FROM ".DB::table('lev_pop')." WHERE isgoods>0 ORDER BY isgoods DESC, addtime DESC");//��ѯ��¼����
$pagecount = $total ? (($total < $limit) ? 1 : (($total % $limit) ? ((int)($total / $limit) + 1) : ($total / $limit))) : 0;//������ҳ��
if ($page > $pagecount) $page = $pagecount;

$start = ($page - 1) * $limit;

$sql   = "SELECT * FROM ".DB::table('lev_pop')." WHERE isgoods>0 ORDER BY isgoods DESC, addtime DESC".DB::limit($start, $limit);
$lists = DB::fetch_all($sql);//print_r($lists);

$pages = multi($total, $limit, $page, 'admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier='.$PLNAME.'&pmod=admin3');//print_r($pages);
if ($lists) {
	foreach ($lists as $v) {
		showtablerow(
		'',
		array(
			'width=30',
			'width=30',
		),
		array (
			"<input type=\"checkbox\" class=\"checkbox\" name=\"ids[]\" value=\"{$v['id']}\">",
		$v['id'],
		"<a href='admin.php?action=plugins&operation=config&do={$pluginid}&identifier={$PLNAME}&pmod=admin3&isuid={$v['uid']}'>".$v['username']."</a>",
		$v['awardscore'],
		$v['goods'],
		$v['isgoods'],
		$v['num'],
		$v['isaward'],
		$v['isfree'],
		dgmdate($v['addtime'], 'u'),
		$v['ip']
		));
	}
	showsubmit(
		'', 
		'', 
		'', 
		'<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'ids\')" />
		<label for="chkall">'.$lev_lang['slts'].'</label>&nbsp;&nbsp;<input type="submit" class="btn" name="dosubmit" value="'.$lev_lang['del'].'" onclick="return confirm(\''.$lev_lang['confirm'].'\');"/>
		', 
	$pages);
}else {
	showtablerow('',array(),array($lev_lang['nodata']));
}
showtablefooter();
showformfooter();



