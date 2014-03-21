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

class taskset_module {

	public static function init($param = array()) {
		$tasks = glob(DISCUZ_ROOT.'source/plugin/levaward/module/__*_module.php');
		if (empty($tasks)) return '';
		foreach ($tasks as $r) {
			$class = basename($r, '_module.php');
			$html .= lev_module::ismodule2($class, 'vhtml', $param);
		}
		return $html;
	}
	
}







