<?php

/**
 * Lev.levme.com [ 专业开发各种Discuz!插件 ]
 *
 * Copyright (c) 2013-2014 http://www.levme.com All rights reserved.
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class _setstatusaward_module {

	public static function _setstatusaward($param = array()) {
		global $_G;
		//$lang = lev_base::$lang;
		$opid = intval($_GET['opid']);
		$sql  = "SELECT a.*, b.uid buid FROM ".DB::table('lev_award_award_log')." a LEFT JOIN ".
				 DB::table('lev_award')." b ON a.doingid=b.id WHERE a.id=$opid";
		$res = DB::fetch_first($sql);
		if (empty($res)) exit('-2');
		if ($_G['adminid'] !=1) {
			if ($res['uid'] !=$_G['uid']) {
				if ($res['buid'] !=$_G['uid']) exit('-1');
				if ($res['status'] !=2) exit('-3');
			}else {
				if ($res['status'] !=1) exit('-4');
			}
		}
		switch ($res['status']) {
			case 2 :
				$status = 1;
				$note = lev_base::levdiconv('您的奖品已寄出 ')
						.'&raquo; <a href="'.lev_base::$PLURL.'%3Aaward&doingid='.$res['doingid'].'">'.$res['title'].'</a>';
				notification_add($res['uid'], 'system', $note);
				break;
			case 3 :
				$status = 4;
				break;
			case 1 :
				$status = 5;
				break;
			default:
				$status = 0;
				break;
		}
		DB::update('lev_award_award_log', array('status'=>$status), array('id'=>$opid));
		echo $status;exit();
	}
	
}







