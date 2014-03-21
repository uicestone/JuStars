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

class _6_module {

	public static function init($param = array()) {
		global $_G;
		$idcars = explode("\n", trim($param['idcars']));
		$descs = trim($idcars[0]);
		if ($descs) {
			unset($idcars[0]);
			foreach ($idcars as $r) {
				$r = trim($r);
				if ($r) $_idcars .= $r."\r\n";
			}
			DB::update('lev_award_award', array('idcars'=>trim($_idcars)), array('id'=>$param['id']));
			/*$lang = lev_base::$lang;
			$plurl = lev_base::$PLURL;
			$note = '恭喜您，抽中【<a href="'.$plurl.'%3Aaward&doingid=
				'.$param['doingid'].'">'.lev_base::levdiconv($param['title'].' &raquo; '.$descs, CHARSET, 'utf-8').'</a>】 ';
			notification_add($_G['uid'], 'system', lev_base::levdiconv($note));*/
			return $descs;
		}
		return lev_base::$lang['sendallaward'];
	}
	
}

