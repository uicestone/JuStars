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

class task_module {

	public static function init($param = array()) {
		global $_G;
		if (!is_file(DISCUZ_ROOT.'source/plugin/levquick/lev_base.php')) return '-1';
		$tae = 'levaward';
		$tsk = unserialize($param['tasks']);
		$sql = "SELECT * FROM ".DB::table('lev_quick_task')." WHERE uid=$_G[uid] AND doingid=$param[id] AND taskname='$tae' AND status=1";
		$res = DB::fetch_all($sql);
		if (empty($res)) return '-2';
		$awardnum = 0;
		foreach ($res as $r) {
			$tsknum = intval($tsk[$r['logintype'].'_'.$r['tktype']]);
			if ($tsknum >0) {
				$awardnum += $tsknum;
				//DB::update('lev_quick_task', array('status'=>0), array('id'=>$r['id']));
			}
		}
		if ($awardnum >0) {
			$_awardnum = $awardnum + $param['myawardnumed'];
			DB::update('lev_quick_task', array('status'=>0), array('uid'=>$_G[uid], 'doingid'=>$param['id'], 'taskname'=>$tae));
			DB::update('lev_award_join', array('awardnum'=>$_awardnum), array('id'=>$param['myjoinid']));
			return $awardnum;
		}
	}
	
	public static function _award($doingid =0) {
		$awardtype = trim($_GET['awardtype']);
		if (!is_file(DISCUZ_ROOT.'source/plugin/levaward/module/__'.$awardtype.'_module.php')) exit('-78');
		
		$doingid = $doingid >0 ? intval($doingid) : exit('0');
		global $_G;
		if (!$_G['uid']) exit('-5');
		$mydo = DB::fetch_first("SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid=$doingid AND uid=$_G[uid]");
		if (empty($mydo)) exit('-2');
		$sql = "SELECT * FROM ".DB::table('lev_award')." WHERE id=$doingid";
		$rs  = DB::fetch_first($sql);
		$tasks = unserialize($rs['tasks']);
		$r = $tasks['_'.$awardtype];
		if (!$r) exit('-7');
		$param = array('doing' => $rs, 'join' => $mydo);
		lev_module::ismodule2('__'.$awardtype, 'taskaward', $param);
	}
	
	public static function awardjs() {
		$lev_lang = lev_base::$lang;
		$lm = lev_base::$lm;
		$js = <<<EOF
<script type="text/javascript">
var _ispop = 0;
function taskaward(doingid, awardtype, taskid, obj) {
	$$.get('{$lm}task._award.'+ doingid, {awardtype:awardtype, taskid:taskid}, function(data){
		var _anum = parseInt(data);
		if (_anum >0) {
			_ispop = 0;
			_onum = parseInt($$('#lev_lottery_num').text());
			$$('#lev_lottery_num').html(_anum + _onum);
			art.dialog.tips('{$lev_lang['succeed']}');
		}else {
			art.dialog.tips('{$lev_lang['error']}{$lev_lang['noact']}');
		}
		$$(obj).fadeOut();
	});
}
</script>
		
EOF;
		return $js;
	}
	
}







