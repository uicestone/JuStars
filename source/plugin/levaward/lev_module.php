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

require_once 'lev_base.php';

class lev_module extends lev_base {
	
	public function __construct() {
		parent::__construct();
	}
	
	public static function _m($class ='', $method = '', $param = '') {
		if (!$method) $method = $class;
		if ($method[0] !='_') exit(self::$lang['noact']);
		self::ismodule2($class, $method, $param);
	}
	
	public static function ismodule($class = '') {
		return parent::levloadclass($class.'_module', 'module', 0);
	}
	
	public static function ismodule2($class = '', $method = '', $param = array()) {
		$ismodule = parent::levloadclass($class.'_module', 'module');
		if ($ismodule) {
			if (!$method) $method = $class;
			if (method_exists($ismodule, $method)) {
				return $ismodule->$method($param);
			}
		}
	}
	
}










