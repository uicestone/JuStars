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

class _3_module {

	public function __construct() {}

	public static function init($param = array()) {
		$magicid = $param['awardids'];
		$num = intval($param['awardnum']);
		if ($magicid >0) {
			require_once libfile('class/task');
			$task = new task();
			$task->reward_magic($magicid, $num);
		}
	}
	
}

