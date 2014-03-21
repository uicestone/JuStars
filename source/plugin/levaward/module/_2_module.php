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

class _2_module {

	public static function init($param = array()) {
		$medalid = $param['awardids'];
		$day = intval($param['awardnum']);
		if ($medalid >0) {
			require_once libfile('class/task');
			$medal = new task();
			$medal->reward_medal($medalid, $day);
		}
	}
	
}

