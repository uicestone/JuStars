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

class _5_module {

	public function __construct() {}

	public static function init($param = array()) {
		global $_G;
		$lang = lev_base::$lang;
		$plurl = lev_base::$PLURL;
		$note = '恭喜您，抽中【<a href="'.$plurl.'%3Aaward&doingid=
			'.$param['doingid'].'">'.lev_base::levdiconv($param['title'], CHARSET, 'utf-8').'</a>】 
			<a href="home.php?mod=spacecp&ac=profile&op=base">完善资料</a>';
		notification_add($_G['uid'], 'system', lev_base::levdiconv($note));
	}
	
}

