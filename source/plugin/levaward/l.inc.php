<?php

/**
 * Lev.levme.com [ 专业开发各种Discuz!插件 ] - 路由器
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

require_once 'lev_enter.php';

$m = trim($_GET['m']) ? trim($_GET['m']) : (trim($_POST['m']) ? trim($_POST['m']) : '_run');
if (!preg_match('/^[a-zA-Z_.,-][a-zA-Z0-9_.,-=]+$/', $m)) exit('error param m!');
$param = explode('.', $m);
$func  = isset($param[0]) && trim($param[0]) ? trim($param[0]) : '_run';
unset($param[0]);

if ($func[0] !='_') {
	if (!$_G['uid']) exit('error op!');
	if ($_G['adminid'] !=1 && strpos($_PLG['isadmin'].'=', $_G['uid'].'=') ===FALSE) {//check admin
		exit('error op!');
	}
}

if (!method_exists('lev_class', $func)) exit('method not exits!');

if ($func[1] !='_') lev_class::checkfh();

call_user_func_array(array($C, $func), $param);






