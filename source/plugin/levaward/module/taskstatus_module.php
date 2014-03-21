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

class taskstatus_module {

	public static function init($param = array()) {
		global $_G;
		$doingid = $param['id'];
		$sql = "SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid=$doingid AND uid=$_G[uid]";
		$rs  = DB::fetch_first($sql);
		$tasks = unserialize($param['tasks']);
		$html  = '';
		$arr['doing'] = $param;
		$arr['join']  = $rs;
		foreach ($tasks as $k => $r) {
			if ($param['funcset'] && $param['funcset'] !=$k) continue;
			if ($r) $html .= lev_module::ismodule2('_'.$k, 'taskstatus', $arr);
		}
		return $html;
	}
	
}







