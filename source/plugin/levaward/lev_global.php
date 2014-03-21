<?php

/**
 * Levme.com [ 专业开发各种Discuz!插件 ]
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

$_types = lev_base::levlang($_PLG['types'], 1);

//$_systype = lev_base::getpluginvar('template');
$_systype = array();
$doingtmp = (array)unserialize($_PLG['template']);
foreach ($doingtmp as $r) {
	if (is_file(DISCUZ_ROOT.$diydir.'systype_'.$r.'.htm')) {
		$_systype[$r] = $lev_lang['doingtmp'.$r];
	}
}

$_awardtype = lev_class::$dtype;




