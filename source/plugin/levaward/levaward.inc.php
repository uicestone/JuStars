<?php

/**
 * 
 *
 * Copyright (c) 2013-2014 
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once 'lev_enter.php';//print_r($_G);

$time  = TIMESTAMP;

$m = intval($_GET['m']);
switch ($m) {
	case 1 :
		if ($_G['adminid'] !=1) {
			$ck = lev_module::ismodule2('senddoing', 'init');
			if (!$ck || $ck <0) showmessage($lev_lang['error'].$lev_lang['udoingtips'].$ck);
		}
		$edtid = intval($_GET['edtid']);
		if ($edtid >0) {
			$sql = "SELECT a.*, b.subject subject FROM ".DB::table('lev_award')." a LEFT JOIN ".DB::table('forum_thread')." b
					ON a.tid=b.tid WHERE a.id=$edtid";
			$sql.= $_G['adminid'] !=1 ? " AND a.uid=$_G[uid]" : "";
			$idinfo = DB::fetch_first($sql);
			if ($idinfo) {
				extract($idinfo);
				$tasks = unserialize($tasks);
				if (is_array($tasks)) extract($tasks);
			}
		}
		$lists = lev_class::_myawards($edtid, $awards);
		$mytid = lev_class::mytids();
		$profile = lev_class::mbprofile();
		$tmp = 'add';
		$menuname = $lev_lang['senddoing'];
		break;
	default:
		$typeid  = intval($_GET['typeid']);
		$systype = intval($_GET['typeid2']);
		$doingst = intval($_GET['doingst']);
		$uid     = intval($_GET['uid']);
		switch ($doingst) {
			case 1 :
				$where = " (endtime>$time OR endtime=0) AND starttime<$time AND ";
				break;
			case 2 :
				$where = " endtime<$time AND endtime>0 AND ";
				break;
			case 3 :
				$where = " starttime>$time AND ";
				break;
			default:
				break;
		}
		$tmp   = $PLNAME;
		$pgurl = $PLURL.':'.$PLNAME; 
		$limit = 20;
		$where.= $typeid >0 ? "typeid=$typeid AND" : ($systype >0 ? "systype=$systype AND" : "");
		$where.= $uid >0 ? "uid=$uid AND" : "";
		$where.= " status!=1 ORDER BY uptime DESC";
		$lists = lev_base::levpages('lev_award', $where, $limit, 0, $pgurl);
		$menuname = $lev_lang['doinglist'];
		
		if ($_G['uid']) {
			$insql = lev_base::sqlinstr($lists['lists'], 'id');
			if ($insql) {
				$sql = "SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid IN ($insql) AND uid=$_G[uid]";
				$joins = DB::fetch_all($sql, array(), 'doingid');
			}
		}
		break;
}
include template('diy:'.$tmp, '', $diydir);


















