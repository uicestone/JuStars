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

class systype_field_module {

	public static function init($param = array()) {
		global $_G;
		$id   = $param['id'];
		$info = unserialize($param['info']);
		$_PLG = lev_base::$PL_G;
		$lev_lang  = lev_base::$lang;
		$extfields = explode("\n", trim($_PLG['extfields']));
		foreach ($extfields as $r) {
			$r = trim($r);
			$one = explode('=', $r);
			if (!$one[2] || strstr('|'.$one[2].'|', '|'.$id.'|')) $fields[] = $one;
		}
		if ($fields) {
			foreach ($fields as $v) {
				if (!$v[0]) continue;
				$html .= <<<EOF
				
		<tr><th>{$v[1]}{$lev_lang[mh]}</th><td>
		<input type="text" name="levef[ef_{$v[0]}]" id="ef_{$v[0]}" value="{$info['ef_'.$v[0]]}" class="px" />
		</td></tr>
				
EOF;
			}
			return $html;
		}
		
	}
	
}







