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

class _1_module {

	public static function init($param = array()) {
		global $_G;
		$sql = "SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid={$param['doingid']} AND uid=".$_G['uid'];
		$rs  = DB::fetch_first($sql);
		DB::update('lev_award_join', array('awardnum'=>$rs['awardnum'] +$param['awardnum']), array('id'=>$rs['id']));
	}
	
}

