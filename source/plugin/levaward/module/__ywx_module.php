<?php

/**
 * Lev.levme.com [ רҵ��������Discuz!��� ]
 *
 * Copyright (c) 2013-2014 http://www.levme.com All rights reserved.
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */

if(!defined('IN_DISCUZ')) {//.php�ļ�ȫ�������Ѿ�����ת�롣���ļ���ת�룬����������û�����롣
	exit('Access Denied');
}

class __ywx_module {

	public static function taskstatus($param = array()) {
		if (!is_file(DISCUZ_ROOT.'levywx.php')) return '';
		
		$doingid = $param['doing']['id'];
		$join = $param['join'];
		$param = $param['doing'];
		$mtask = unserialize($join['tasks']);
		$ymd = date('Ymd', TIMESTAMP);
		$tasks = unserialize($param['tasks']);
		
		if ($tasks[_ywx]['_wxnum']) $wx = " ��ע΢�ţ�<b><font color=#cc0000>".lev_base::levdiconv($tasks[_ywx]['_wxnum'], CHARSET, 'gbk')."</font></b>&nbsp;&nbsp;&nbsp;";
		if ($tasks[_ywx]['_yxnum']) $wx.= " ���ţ�<b><font color=#cc0000>".lev_base::levdiconv($tasks[_ywx]['_yxnum'], CHARSET, 'gbk').'</font></b>';
		if (!$wx) return '';
		$html = <<<EOF
			<div class="rfm" style="width:400px;"><table width="100%">
			<tr><td style="padding-bottom:0;">{$wx}&nbsp;&nbsp;&raquo;&nbsp;&nbsp;��ø���齱���ᣡ</td></tr></table>
			</div>
EOF;
		return lev_base::levdiconv($html, 'gbk');
	}
	
	public static function taskaward($_param = array()) {
	}
	
	public static function vhtml($param = array()) {
		
		$wxnum = lev_base::levdiconv($param[_ywx]['_wxnum'], CHARSET, 'gbk');
		$yxnum = lev_base::levdiconv($param[_ywx]['_yxnum'], CHARSET, 'gbk');
		
		$html = <<<EOF
		
<tr><th>΢�źţ�</th><td>
<input type="text" name="tasks[_ywx][_wxnum]" id="_wxnum" 
value="{$wxnum}" class="px" style="width:120px"/>
���밲װ��΢�Ż����˹ܼң���Ѱ�װ
<a href="http://addon.discuz.com/?@levwxyx.plugin" target="_blank" style="color:blue">�������</a>
</td></tr>
		
<tr><th>���źţ�</th><td>
<input type="text" name="tasks[_ywx][_yxnum]" id="_yxnum" 
value="{$yxnum}" class="px" style="width:120px"/>
</td></tr>
		
EOF;
		return lev_base::levdiconv($html, 'gbk');
	}
	
}







