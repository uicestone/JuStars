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

class stopip_module {

	public static function init($param = array()) {
		global $_G;
		$stopips = explode("\n", $param['stopip']);
		if ($stopips[0]) {
			foreach ($stopips as $r) {
				if (strstr($r, $_G['clientip'])) return TRUE;
				if (strpos($r, '*') !==FALSE) {
					$ips = str_replace(array('.*', '*'), '', trim($r));
					if (strstr($_G['clientip'], $ips)) return TRUE;
				}
			}
		}
	}
	
}







