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

class _4_module {

	public function __construct() {}

	public static function init($param = array()) {
		global $_G;
		$dataArr = array('extcredits'.$param['scoretype'] => $param['awardnum']);
		updatemembercount($_G['uid'], $dataArr);
		//$notice = lev_base::levdiconv('参加活动，中奖。').'['.$param['title'].']';
		//lev_base::acscore($param['awardnum'], $notice, $param['scoretype']);
	}
	
}

