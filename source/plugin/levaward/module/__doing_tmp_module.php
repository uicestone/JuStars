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

if(!defined('IN_DISCUZ')) {//.php文件全部中文已经程序转码。此文件以转码，经反复测试没有乱码。程序转码方法lev_base::levdiconv();
	exit('Access Denied');
}

class __doing_tmp_module {
	
	public static function vhtml($param = array()) {
		//$lev_lang = lev_base::$lang;
		$value = $param['doing_tmp'] ? $param['doing_tmp'] : "special";
		$html = <<<EOF
		
<tr><th>活动页面模板：</th><td>
<table width="100%"><tr><td width="100">
<input type="text" name="tasks[doing_tmp]" id="doing_tmp"  class="px" value="{$value}" style="width:100px">
</td><td>
<a href="javascript:;" onclick="jQuery('#doing_tmp').val('default')" style="color:blue">通用模板</a> | 
<a href="javascript:;" onclick="jQuery('#doing_tmp').val('special')" style="color:blue">专题模板</a>
</td><td> &raquo; 
此处填写活动页面调用的模板文件名称
</td></tr></table>
</td></tr>
		
EOF;
		return lev_base::levdiconv($html);
	}
	
}







