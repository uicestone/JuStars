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

class senddoing_module {

	public static function init($param = array()) {
		global $_G;
		if (!$_G['uid']) return '-5';
		
		$_PLG = lev_base::$PL_G;
		$lev_lang = lev_base::$lang;
		
		$udoing = unserialize($_PLG['udoing']);
		if (!$udoing[0]) return '-1';
		
		$usergroups = unserialize($_PLG['usergroups']);
		if (!$usergroups[0] || !in_array($_G['groupid'], $usergroups)) return '-2';
		
		if (isset($param['systype'])) {
			$systype = $param['systype'];
			if (!in_array($systype, $udoing)) return '-3';
		}
		return TRUE;
	}
	
	public static function udoing() {
		
	}
	
}







