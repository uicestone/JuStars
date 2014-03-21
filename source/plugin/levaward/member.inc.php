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

if (!$_G['uid']) showmessage('Login...', '', '', array('login'=>TRUE));

require_once 'lev_enter.php';

$m = intval($_GET['m']);
$t = intval($_GET['t']);
$srhkey  = trim($_GET['srhkey']) ? trim($_GET['srhkey']) : $lev_lang['srhkey'];
switch ($m) {
	case 6 :
	case 4 ://活动奖品管理
		lev_class::_setaward();
		$sql = "SELECT * FROM ".DB::table('common_usergroup')." ORDER BY radminid DESC, creditshigher DESC, stars DESC";
		$ugroup = lev_class::fetch_all($sql, array(), 'groupid');
		$awardimgs = glob(DISCUZ_ROOT.$PLSTATIC.'award/*');
		$magics = lev_class::fetch_all("SELECT * FROM ".DB::table('common_magic')." WHERE available=1 ORDER BY displayorder ASC");//道具
		$medals = lev_class::fetch_all("SELECT * FROM ".DB::table('forum_medal')." WHERE available=1 ORDER BY displayorder ASC");//勋章
		switch ($t) {
			case 1 :
				$edtid = intval($_GET['edtid']);
				if ($edtid >0) {
					$sql = "SELECT * FROM ".DB::table('lev_award_award')." WHERE id=$edtid";
					$einfo = DB::fetch_first($sql);
					if ($einfo && $einfo['uid'] = $_G['uid']) {
						extract($einfo);
						$usergroups = unserialize($usergroups);
					}
				}
				break;
			default:
				$t = 0;
				$awardid = intval($_GET['awardid']);
				$limits = 20;
				if ($awardid >0 ) {
					$sql = "SELECT * FROM ".DB::table('lev_award')." WHERE id=$awardid";
					$sql.= ($_G['adminid'] ==1 && $m==6) ? "" : " AND uid=$_G[uid]";
					$rs = DB::fetch_first($sql);
					$menuname = '<b>'.$lev_lang['vuaward'].' &raquo; '.$rs['title'].'</b>';
					$insql = lev_base::sqlinstr($rs['awards']);
					if (!$insql) showmessage($rs['title'].' &raquo; '.$lev_lang['noaward2']);
					$limits = 100;
				}
				if ($srhkey != $lev_lang['srhkey']) {
					if (substr($srhkey, 0, 4) =='uid_') {
						$_uid = intval(str_replace('uid_', '', $srhkey));
						$where = " uid=$_uid AND ";
					}else {
						$where = " (id='$srhkey' OR title LIKE '%$srhkey%') AND ";
					}
				} 
				if ($insql) {
					$where .= "id IN ($insql)";
					$where .= ($_G['adminid'] ==1 && $m==6) ? "" : " AND uid=$_G[uid]";
					$where .= " ORDER BY listorder ASC, id DESC";
				}else {
					$where .= ($_G['adminid'] ==1 && $m==6) ? "1" : "uid=$_G[uid]";
					$where .= " ORDER BY id DESC";
				}
				$pgurl = $PLURL.':member&m=4&srhkey='.$srhkey.'&awardid='.$awardid;
				$lists = lev_base::levpages('lev_award_award', $where, $limits, 0, $pgurl);
				break;
		}
		break;
	case 5 :
		$doingid = $_GET['doingid'] >0 ? intval($_GET['doingid']) : 0;
		if ($doingid) {
			$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$doingid");
			if (empty($rs)) showmessage($lev_lang['nodoing']);
			if ($rs['uid'] != $_G['uid'] && $_G['adminid'] !=1) showmessage($lev_lang['noact']);
		}elseif ($srhkey != $lev_lang['srhkey']) {
			$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id='$srhkey' OR title LIKE '%$srhkey%'");
			if (empty($rs)) showmessage($lev_lang['nodoing']);
			if ($rs['uid'] != $_G['uid'] && $_G['adminid'] !=1) showmessage($lev_lang['noact']);
			$doingid = $rs['id'];
		}
		$index = intval($_GET['index']);
		$url = $PLURL.':member&m=5&doingid='.$doingid.'&index='.$index.'&t='.$t;
		$sql = DB::table('lev_award');
		$sql.= $_G['adminid'] !=1 ? " WHERE uid=$_G[uid]" : "";
		$sql.= " ORDER BY id DESC";
		$mydoing = lev_base::levpages('', $sql, 20, $index, 0, '*', 1);
		$doingid = $doingid ? $doingid : intval($mydoing['lists'][0]['id']);
		switch ($t) {
			case 2 :
				$_ext = ' AND status >0 ';
			case 1 :
				$where = DB::table('lev_award_award_log')." WHERE doingid=$doingid $_ext ORDER BY id DESC";
				$lists = lev_base::levpages('', $where, 20, 0, $url);
				$insql = lev_base::sqlinstr($lists['lists'], 'awardid');
				if ($insql) {
					$sql = "SELECT * FROM ".DB::table('lev_award_award')." WHERE id IN ($insql)";
					$award = DB::fetch_all($sql, array(), 'id');
				}
				break;
			default:
				$where = DB::table('lev_award_join')." WHERE isjoin=1 AND doingid=$doingid ORDER BY id DESC";
				$lists = lev_base::levpages('', $where, 20, 0, $url);
				break;
		}
		break;
	case 6 :
		$ats    = isset($_GET['ats']) ? intval($_GET['ats']) : '';
		$srhkey = $srhkey ==$lev_lang['srhkey'] ? $lev_lang['srhorder'] : $srhkey;
		$lists  = lev_module::ismodule2('_teambuy', 'lists', array($t, $srhkey, $ats));
		break;
	default:
		$m = 0;
		if ($srhkey != $lev_lang['srhkey']) {
			$ext = " AND (a.id='$srhkey' OR a.title LIKE '%$srhkey%') ";
		} 
		$felds = 'a.*, b.awardnum bawardnum, b.allnum ballnum';
		switch ($t) {
			case 1 :
				$where = DB::table('lev_award')." a LEFT JOIN ".DB::table('lev_award_join')." b ON a.id=b.doingid 
						WHERE b.uid={$_G['uid']} AND b.isjoin=1 $ext ORDER BY a.id DESC";
				break;
			case 2 :
				$where = DB::table('lev_award')." a LEFT JOIN ".DB::table('lev_award_join')." b ON a.id=b.doingid 
						WHERE b.uid={$_G['uid']} AND b.isthink=1 $ext ORDER BY a.id DESC";
				break;
			case 3 :
				$where = DB::table('lev_award')." a LEFT JOIN ".DB::table('lev_award_join')." b ON a.id=b.doingid 
						WHERE b.uid={$_G['uid']} AND b.istop=1 $ext ORDER BY a.id DESC";
				break;
			default:
				$mytid = lev_class::mytids();
				$felds = "a.*, b.subject subject";
				$where = DB::table('lev_award')." a LEFT JOIN ".DB::table('forum_thread')." b ON a.tid=b.tid 
						WHERE a.uid={$_G['uid']} $ext ORDER BY a.id DESC";
				break;
		}
		$pgurl = $PLURL.':member&t='.$t.'&srhkey='.$srhkey;
		$lists = lev_base::levpages('', $where, 20, 0, $pgurl, $felds);
		break;
}

include template($PLNAME.':member');


















