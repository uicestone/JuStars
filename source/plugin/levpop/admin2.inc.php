<?php
/**
 * Lev.levme.com
 *
 * Copyright (c) 2013-2014 http://www.levme.com All rights reserved.
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')){
	exit('Access Denied');
}

require_once 'lev_enter.php';
cpmsg($lev_lang['nofree'], dreferer(), 'error');
