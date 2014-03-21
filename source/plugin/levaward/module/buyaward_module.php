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

class buyaward_module {//此文件中文已程序转码。此文件为gbk编码。在utf8环境已测试，不会出现乱码。

	public static function init($param) {
		global $_G;
		$spend  = $param['buynum'] * $param['regscore'];
		if ($spend >0) {
			$sql = "SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid={$param['id']} AND uid=".$_G['uid'];
			$rs  = DB::fetch_first($sql);
			if (empty($rs)) return '-3';
			$secs = $rs['uptime'] + lev_base::$PL_G['buywait'] - TIMESTAMP;
			if ($secs >0) {
				echo lev_base::levdiconv('系统繁忙，请等候<b style="font-size:16px;color:red"> '.$secs.' </b>秒钟！', 'gbk');
				exit();
			}
			$notice = '<a href="plugin.php?id=levaward%3Aaward&doingid='.$param['id'].'">['.$param['title'].'] ';
			$notice.= lev_base::levdiconv('购买活动参与次数。</a>', 'gbk');
			$ck = lev_base::acscore(-$spend, $notice);
			if ($ck) {
				DB::update('lev_award_join', 
					array(
						'awardnum' => $rs['awardnum'] +$param['buynum'],
						'uptime'   => TIMESTAMP,
					), array('id'=>$rs['id']));
				return '1';
			}
			return '-8';
		}
		return '-9';
	}
	
	public static function addawardnum($param = array()) {
		global $_G;
		if (!$_G['uid']) return '';
		$num = intval($param['num']);
		$doingid = intval($param['doingid']);
		if ($num) {
			$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid=$doingid AND uid=$_G[uid]");
			if ($rs) {
				$awardnum = $rs['awardnum'] +$num;
				DB::update('lev_award_join', array('awardnum' =>$awardnum), array('id' =>$rs['id']));
				return $awardnum;
			}
		}
	}
	
}







