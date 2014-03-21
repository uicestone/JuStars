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

require_once 'lev_module.php';

class lev_class extends lev_module {
	
	public static $dtype = array(1, 2, 3, 4, 5, 6, 7);
	
	public function __construct() {
		parent::__construct();
		self::$dtype = self::$_G['adminid'] !=1 ? array(5, 6, 7) : array(1, 2, 3, 4, 5, 6, 7);
	}
	
	public static function _delawardlog() {
		global $_G;
		if ($_G['adminid'] ==1) {
			$sqlinstr = self::sqlinstr($_GET['chkall']);
			if ($sqlinstr) {
				DB::query("DELETE FROM ".DB::table('lev_award_award_log')." WHERE `id` IN($sqlinstr)");
				showmessage(self::$lang['succeed'], dreferer());
			}
			showmessage(self::$lang['error'].self::$lang['noact'].' instr');
		}
		showmessage(self::$lang['error'].self::$lang['noact'].' noadmin');
	}
	
	public static function _awardlog($doingid) {
		if (!self::$_G['uid']) exit('-5');
		$doingid = intval($doingid) >0 ? intval($doingid) : exit(self::$lang['error'].self::$lang['noact'].' doingid');
		$uid   = $_GET['type'] >0 ? 0 : self::$_G['uid'];
		$infos = self::awardlogs($doingid, $uid);
		$lang  = self::$lang;
		if (empty($infos)) exit(self::$lang['noawarded']);
		$str = '<table style="width:400px;">';
		foreach ($infos as $k => $r) {
			$bbsname = cutstr($r[bbsname], 6);
			if ($uid) $r['title'] .= '<br><font color=green>'.$r['descs'].'</font>';
			$str .= "<tr style='border-bottom:1px solid #ddd'>
				<td style='padding:2px;'>{$bbsname}</td>
				<td style='padding:2px;'>{$r[title]}</td>
				<td style='padding:2px;'>{$lang['rpc']}{$r[realprice]}{$lang['yuan']}</td>
				<td align='right'>".dgmdate($r[addtime], 'u')."</td></tr>";
		}
		$pagep = $_GET['page']-1;
		$pagep = $pagep >0 ? '<a href="javascript:;" 
							onclick="myawardlog('.$doingid.', '.intval($_GET['type']).', '.$pagep.')">'.$lang['pagep'].'</a>' : "";
		$pagen = max($_GET['page'] +1, 2);
		$pagen = count($infos) <15 ? "" : 
			'<a href="javascript:;" onclick="myawardlog('.$doingid.', '.intval($_GET['type']).', '.$pagen.')">'.$lang['pagen'].'</a>';
		$str .= '
		<tr><td colspan="4" align="center">
		'.$pagep.' &nbsp;&nbsp;
		'.$pagen.'
		</td></tr></table>';
		echo $str;exit();
	}
	public static function awardlogs($doingid = 0, $uid = 0, $order = 0) {
		$doingid = intval($doingid);
		$sql = DB::table('lev_award_award_log')." WHERE status >=0 ";
		$sql.= $doingid >0 ? " AND doingid=$doingid" : "";
		$sql.= $uid >0 ? " AND uid=$uid" : "";
		if ($order) {
			$sql.= " ORDER BY realprice DESC, addtime DESC";
			$limit = 3;
		}else {
			$sql.= " ORDER BY id DESC ";
			$limit = 15;
		}
		$rs  = self::levpages('', $sql, $limit, 0);
		return $rs['lists'];
	}
	
	public static function _myawards($edtid = 0, $awards = 0) {
		global $_G;
		$insql = self::sqlinstr($awards);
		if ($insql) {
			$sql = "SELECT * FROM ".DB::table('lev_award_award')." WHERE id IN ($insql)";
			$sql.= $_G['adminid'] !=1 ? " AND uid={$_G['uid']}" : "";
			$infos = DB::fetch_all($sql." ORDER BY listorder ASC");
			$lists['lists'] = $infos;
			$lists['pages'] = TRUE;
		}else {
			$edtid = intval($edtid);
			if ($edtid >0) {
				$sq = "SELECT * FROM ".DB::table('lev_award')." WHERE id=$edtid";
				$sq.= $_G['adminid'] !=1 ? " AND uid=$_G[uid]" : "";
				$rs = DB::fetch_first($sq);
				$in = $rs ? self::sqlinstr($rs['awards']) : "";
			}
			$where = $in ? "id NOT IN ($in) AND" : "";
			$where.= $rs ? " uid=$rs[uid]" : " uid=$_G[uid]";
			$where.= " ORDER BY id DESC";
			$lists = self::levpages('lev_award_award', $where, 20);
		}
		if (isset($_GET['ajax'])) {
			$lev_lang = self::$lang;
			include template(self::$PLNAME.':setaward_list');
			exit();
		}
		return $lists;
	}
	
	public static function _deldoings($id = 0) {
		$id = intval($id);
		switch ($_GET['db']) {
			case 1 :
				$dbname = 'lev_award_award';
				break;
			default:
				$dbname = 'lev_award';
				break;
		}
		if (self::$_G['adminid'] !=1) {
			$rs = DB::fetch_first("SELECT * FROM ".DB::table($dbname)." WHERE id=$id AND uid=".self::$_G['uid']);
			if (empty($rs)) exit(self::$lang['error'].self::$lang['noact']);
		}
		DB::query("DELETE FROM ".DB::table($dbname)." WHERE `id`=$id");
		if ($dbname =='lev_award') {
			DB::query("DELETE FROM ".DB::table('lev_award_join')." WHERE `doingid`=$id");
		}
		exit('1');
	}
	
	public static function _setmytid($id = 0, $tid = 0) {
		if (!self::$_G['uid']) exit('-5');
		$ck = self::setmytid($id, $tid);
		echo $ck;exit;
	}
	public static function setmytid($id = 0, $tid = 0) {
		$id  = intval($id);
		$tid = intval($tid);
		$_rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE tid=$tid");
		if ($_rs) return '-1';
		if (self::$_G['adminid'] != 1) {
			$res = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$id AND uid=".self::$_G['uid']);
			if (empty($res)) return '-2';
			$rs  = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=$tid AND authorid=".self::$_G['uid']);
			if (empty($rs)) return '-3';
		}
		DB::update('lev_award', array('tid'=>$tid), array('id'=>$id));
		return '1';
	}
	
	public static function _xdoing($id = 0, $type = 0){
		$cancel = intval($_GET['can']);
		$rs = self::xdoing($id, $type, $cancel);
		echo $rs;exit();
	}
	public static function xdoing($id = 0, $type = 0, $cancel = 0) {
		if (!self::$_G['uid']) return '-5';
		$id = intval($id);
		$res = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$id");
		if (empty($res)) return '-2';
		switch ($type) {
			case 1 :
				$type  = 'istop';
				$_type = 'topnum';
				break;
			case 2 :
				$type  = 'isjoin';
				$_type = 'joinnum';
				break;
			default:
				$type  = 'isthink';
				$_type = 'thinknum';
				break;
		}
		$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid=$id AND uid=".self::$_G['uid']);
		$num = $res[$_type]+1;
		$liuyans = strip_tags($_GET['liuyans']);
		if ($rs[$type]) {
			if ($cancel && $type =='isjoin') {
				$num -= 2;
				DB::update('lev_award_join', array($type=>0), array('id'=>$rs['id']));
			}else {
				return '-1';
			}
		}elseif ($rs) {
			$datas = array($type=>1);
			if ($liuyans) $datas['contents'] = $liuyans;
			DB::update('lev_award_join', $datas, array('id'=>$rs['id']));
		}else {
			DB::insert('lev_award_join', 
				array('uid'=>self::$_G['uid'], 'bbsname'=>self::$_G['username'], 
				'doingid'=>$id, 'contents'=>$liuyans, $type=>1, 'addtime'=>TIMESTAMP));
		}
		DB::update('lev_award', array($_type=>$num), array('id'=>$id));
		return $num;
	}
	public static function _regdoings($id = 0) {
		if ($_GET['dosubmit']) {
			$infos = $_GET['info'];
			if ($infos) {
				$files = self::mbprofile();
				foreach ($infos as $k => $r) {
					if ($k =='uid') continue;
					if ($files[$k]) $updatas[$k] = $r;
				}
				if ($updatas) DB::update('common_member_profile', $updatas, array('uid'=>self::$_G['uid']));
			}
			$r = self::xdoing($id, 2, 1);
			if ($r <0) showmessage(self::$lang['error'].self::$lang['noact']);
			showmessage(self::$lang['succeed'], dreferer());
		}else {
			$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$id");
			if (empty($rs)) exit(self::$lang['error'].self::$lang['noact']);
			if (!$_GET['cls']) $str  = self::getprofile($rs);
			$lang = self::$lang;
			$remote = self::$remote;
			$data = <<<EOF
			<div class="rfm" style="width:auto;padding:0 10px;">
			<table><tr><td>{$lang['baomin']} [<font color=red>{$rs[title]}</font>]</td></tr></table>
			</div>
			<div class="rfm" style="width:auto;padding:0 10px;">
			<form id="regdoings" action="{$remote}_regdoings.{$id}" method="post">
				<table>{$str}<tr><th style="width:auto">{$lang['gtbk']}</th><td>
				<textarea id="liuyans" name="liuyans" style="height:60px" class="px"></textarea><br>255{$lang['n255']}
				<font color=red>{$lang['addl2']}</font>
				</td></tr></table>
				<input type="hidden" name="dosubmit" value="1">
			</form>
			</div>
EOF;
			echo $data;
			exit();
		}
	}
	public static function getprofile($info) {
		$file = unserialize($info['mbprofile']);
		if ($file) {
			$lang   = self::$lang;
			$myinfo = self::getbaseuserinfo(self::$_G['uid']);
			$files  = self::mbprofile();
			foreach ($file as $r) {
				$r = trim($r);
				if ($myinfo[$r] || !$r) continue;
				if ($files[$r]['formtype'] =='select' && $files[$r]['choices']) {
					$_arr = explode("\n", trim($files[$r]['choices']));
					$option = '';
					foreach ($_arr as $v) {
						$v = trim($v);
						if ($v ==$myinfo[$r]) $slt = 'selected'; else $slt = '';
						$option .= '<option value="'.$v.'" '.$slt.'>'.$v.'</option>';
					}
					$str .= <<<EOF
					<tr><th style="width:auto;padding:4px;"><i style="color:red">*</i>{$files[$r]['title']}</th>
					<td style="padding:4px;">
			<select id="x_{$r}" name="info[{$r}]">
			<option value="">{$lang['pslt']}</option>{$option}</select>
			</td></tr>
EOF;
				}elseif ($files[$r]['formtpe'] =='textarea') {
					$str .= <<<EOF
					<tr><th style="padding:4px;width:auto"><i style="color:red">*</i>{$files[$r]['title']}</th>
					<td style="padding:4px;">
			<textarea id="x_{$r}" name="info[{$r}]" style="height:60px" class="px">{$myinfo[$r]}</textarea>
			</td></tr>
EOF;
				}else {
					$str .= <<<EOF
					<tr><th style="padding:4px;width:auto"><i style="color:red">*</i>{$files[$r]['title']}</th>
					<td style="padding:4px;">
			<input type="text" id="x_{$r}" name="info[{$r}]" value="{$myinfo[$r]}" class="px"></textarea>
			</td></tr>
EOF;
				}
			}
			return $str;
		}
	}
	
	public static function _listorder() {
		if ($_GET['dolistorder']) {
			foreach ($_GET['listorder'] as $k => $r) {
				DB::update('lev_award_award', array('listorder'=>$r), array('id'=>$k, 'uid'=>self::$_G[uid]));
			}
			showmessage(self::$lang['succeed'], dreferer());
		}
		showmessage(self::$lang['noact']);
	}	
	
	public static function _setoptions($id = 0, $name = 0) {
		$id  = $id >0 ? intval($id) : exit('error id');
		$val = $_GET['val'];
		$sql = "SELECT * FROM ".DB::table('lev_award_award')." WHERE id=$id";
		$rs  = DB::fetch_first($sql);
		if (self::$_G['adminid'] !=1) {
			if (empty($rs) || self::$_G['uid'] != $rs['uid']) exit('-1');
		}
		switch ($name) {
			case 'ratios' :
				$ratios = self::ckfield('ratios', $val);
				if (!$ratios && self::$_G['adminid'] !=1) exit('error ratios');
				break;
			case 'daynum' :
				if ($val >0) {
					$val += self::ymdinfo($rs);
				}else if (is_numeric($val)) {
					$val = self::ymdinfo($rs);
				}else {
					$val = 0;
				}
				break;
			case 'awardtotal' :
				if ($val >0) {
					$val += $rs['totals'];
				}else if (is_numeric($val)) {
					$val = $rs['totals'];
				}else {
					$val = 0;
				}
				break;
			case 'awardnum' :
				$val = intval($val) >0 ? intval($val) : exit('error awardnum');
				break;
			default:
				exit('-4');
				break;
		}
		DB::update('lev_award_award', array($name=>$val), array('id'=>$id));
		exit('1');
	}
	public static function _setoptionsreg($id = 0, $name = 0) {
		$id  = $id >0 ? intval($id) : exit('error id');
		$val = $_GET['val'];
		$sql = "SELECT * FROM ".DB::table('lev_award_join')." WHERE id=$id";
		$rs  = DB::fetch_first($sql);
		if (self::$_G['adminid'] !=1) {
			if ($name !='isjoin') exit('-2');
			if (empty($rs) || self::$_G['uid'] != $rs['uid']) exit('-1');
		}
		DB::update('lev_award_join', array($name=>$val), array('id'=>$id));
		exit('1');
	}
	public static function ckfield($field, $value) {
		$_ratio = 100/self::$PL_G['awardratio'];
		$ratios = is_numeric($value) && $value >= $_ratio ? $value : 0;
		return $ratios;
	}
	
	public static function _setaward() {
		if ($_GET['dosubmit']) {
			$edtid = intval($_GET['edtid']);
			$tips  = self::$lang['error'].self::$lang['noact'];
			$infos = $_GET['info'];
			$title = trim(strip_tags($infos['title']));
			$title = $title ? $title : showmessage(self::$lang['error'].self::$lang['noact'].' title');
			$attach = self::upload($_FILES['awardimg'], 1, array());
			$awardimg = $attach ? self::$uploadurl.$attach['attachment'] : $infos['awardimg'];
			if (!$awardimg) showmessage(self::$lang['error'].self::$lang['noact'].' award img');
			$awardtype = in_array($infos['awardtype'], self::$dtype) ? $infos['awardtype'] : showmessage(self::$lang['noact'].' dtype');
			if ($awardtype !=7) { 
				$ratios = self::ckfield('ratios', $infos['ratios']);
				if (!$ratios && self::$_G['adminid'] !=1) showmessage(self::$lang['error'].self::$lang['noact'].' ratios');
				$awardnum = $infos['awardnum'] >0 ? intval($infos['awardnum']) : showmessage(self::$lang['noact'].' award num');
				if ($awardtype ==4) {
					$scoretype = self::$_G[setting][extcredits][$infos['scoretype']];
					$scoretype = $scoretype ? $infos['scoretype'] : showmessage($tips.' scoretype');
				}
			}
			
			$datas = array(
				'title'      => $title,
				'awardtype'  => $awardtype,
				'idcars'     => $infos['idcars'],
				'usergroups' => serialize($infos['usergroups']),
				'awardimg'   => $awardimg,
				'awardids'   => $infos['awardids'],
				'scoretype'  => $scoretype,
				'awardnum'   => $awardnum,
				'awardtotal' => $infos['awardtotal'],
				'daynum'     => $infos['daynum'],
				'ratios'     => $ratios,
				'descs'      => strip_tags($infos['descs']),
				'uids'       => $infos['uids'],
				'uidstime'   => $infos['uidstime'],
				'realprice'  => $infos['realprice'],
			);
			if ($edtid >0) {
				$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award_award')." WHERE id=$edtid");
				if ($rs && $rs['uid'] ==self::$_G['uid']) {
					DB::update('lev_award_award', $datas, array('id'=>$edtid));
				}else {
					showmessage($tips.' edtid');
				}
			}else {
				$datas['uid']     = self::$_G['uid'];
				$datas['bbsname'] = self::$_G['username'];
				$datas['adminid'] = self::$_G['adminid'];
				$datas['uptime']  = TIMESTAMP;
				$datas['addtime'] = TIMESTAMP;
				DB::insert('lev_award_award', $datas);
			}
			showmessage(self::$lang['succeed'], dreferer());
		}
	}
	
	public static function _adddoings($edtid = 0, $m = 0) {
		$systype = intval($_GET['info']['systype']);
		if (self::$_G['adminid'] !=1) {
			$param = array('systype' => $systype);
			$ck = self::ismodule2('senddoing', 'init', $param);
			if (!$ck || $ck <0) showmessage(self::$lang['error'].self::$lang['udoingtips'].$ck);
		}
		if (!self::$_G['uid']) showmessage(self::$lang['login'], '', '', array('login'=>TRUE));
		$thumb = $_FILES['thumb'];
		if (!$edtid) self::ckupload($thumb);
		$info  = $_GET['info'];
		$startime = strtotime($info['starttime']);
		$endtime  = strtotime($info['endtime']);
		if ($endtime && $endtime < $startime) showmessage(self::$lang['noact'].' time');
		$title = trim(strip_tags($info['title'])); 
		$descs = trim(strip_tags($info['descs'])); 
		$datas = array(
			'title'     => $title ? $title : showmessage(self::$lang['noact'].' title'),
			'typeid'    => $info['typeid'],
			'systype'   => $systype,
			'starttime' => $startime >0 ? $startime : TIMESTAMP,
			'endtime'   => $endtime  >0 ? $endtime  : 0,
			'address'   => strip_tags($info['address']),
			'url'       => substr($info['url'], 0, 7) =='http://' ? $info['url'] : "",
			'descs'     => $descs ? $descs : showmessage(self::$lang['noact'].' descs'),
			'city'      => strip_tags($info['city']),
			'gender'    => $info['gender'],
			'maxnum'    => $info['maxnum'],
			'regscore'  => $info['regscore'],
			'mbprofile' => serialize($info['mbprofile']),
			'price'     => $info['price'],
			'teamprice' => $info['teamprice'],
			'freenum'   => $info['freenum'] >=1 ? intval($info['freenum']) : 0,
			'awards'    => serialize($_GET['awards']),
			'maxjoinnum'=> $info['maxjoinnum'],
			'stopip'    => strip_tags($info['stopip']),
			'tasks'     => serialize($_GET['tasks']),
			'xml'       => serialize($_GET['levef']),
			'price'     => $info['price'],
			'teamprice' => $info['teamprice'],
		);
		if ($systype ==9) $datas['maxnum'] = $info['tmaxnum'];
		if (is_numeric($edtid) && $edtid >0) {
			if (self::$_G['adminid'] !=1) {
				$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$edtid AND uid=".self::$_G['uid']);
				if (empty($rs)) showmessage(self::$lang['error'].self::$lang['noact'].' edit');
			}
			$attach = self::upload($thumb, 1, array(), $edtid, self::$_G['uid'].'_thumb');
			if ($attach) $datas['thumb'] = $attach['attachment'];
			DB::update('lev_award', $datas, array('id'=>$edtid));
		}else {
			if (self::$PL_G['usescore']) {
				$_ck = self::acscore(-self::$PL_G['usescore'], self::$lang['m01']);
				if (!$_ck) showmessage(self::$lang['error'].self::$lang['noscore']);
			}
			$datas['uid']     = self::$_G['uid'];
			$datas['bbsname'] = self::$_G['username'];
			$datas['uptime']  = TIMESTAMP;
			$datas['addtime'] = TIMESTAMP;
			if (self::isopen('checkreg') && self::$_G['adminid'] !=1) $datas['status'] = 1;
			$edtid  = DB::insert('lev_award', $datas, TRUE);
			$attach = self::upload($thumb, 1, array(), $edtid, self::$_G['uid'].'_thumb');
			if ($attach) {
				DB::update('lev_award', array('thumb'=>$attach['attachment']), array('id'=>$edtid));
			}
		}
		if ($info['tid'] && $info['tid'] != $rs['tid']) {
			$ck = self::setmytid($edtid, $info['tid']);
			if ($ck <0) $msg = ' subject!'.$ck;
		}
		$referer = self::$PLURL.':'.self::$PLNAME.'&m=1&edtid='.$edtid;
		showmessage(self::$lang['succeed'].$msg, $referer);
	}
	
	public static function mytids() {
		global $_G;
		if (!$_G['uid']) return '';
		$insql = self::sqlinstr(self::$PL_G['doingforum']);
		$insql = $insql ? " AND fid IN ($insql) " : "";
		$tidss = self::fetch_all("SELECT tid FROM ".DB::table('lev_award')." WHERE uid=$_G[uid] AND tid>0");
		$tidss = self::sqlinstr($tidss, 'tid');
		$insql.= $tidss ? " AND tid NOT IN ($tidss) " : "";
		$isfid = self::sqlinstr(self::$PL_G['doingforum']);
		$insql.= $isfid ? " AND fid IN ($isfid) " : "";
		$_sql  = "SELECT * FROM ".DB::table('forum_thread')." WHERE authorid=$_G[uid] $insql ORDER BY tid DESC LIMIT 10";
		$thrad = self::fetch_all($_sql);
		
		foreach ($thrad as $r) {
			if ($r['tid'] ==$_GET['ltid']) $slt = 'selected'; else $slt = '';
			$mytid .= "<option value='{$r['tid']}' {$slt}>{$r['subject']}</option>";
		}
		return $mytid;
	}
	
	public static function mbprofile() {
		$sql = "SELECT * FROM ".DB::table('common_member_profile_setting')." WHERE available=1";
		$proinfo = self::fetch_all($sql, array(), 'fieldid');
		return $proinfo;
	}
	
	public static function ymdinfo($info, $yd = 0) {
		if ($yd) {
			$ymd = date('Ymd', TIMESTAMP - 3600*24);
			if ($ymd ==$info['ymd']) return $info['tdtotals'];
			if ($ymd < $info['ymd']) return $info['ydtotals'];
		}else {
			$ymd = date('Ymd', TIMESTAMP);
			if ($info['ymd'] ==$ymd) return $info['tdtotals'];
		}
		return '0';
	}
	
	public static function _initdoing($doingid) {
		$doingid = intval($doingid);
		if ($doingid >0) self::initdoing($doingid);
	}
	public static function initdoing($doingid) {
		$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$doingid");
		if (empty($rs)) exit(' nothing');
		if ($rs['uid'] !=self::$_G['uid']) exit(' no uid');
		$datas['isrun'] = $rs['isrun'] ? 0 : 1;
		DB::update('lev_award', $datas, array('id'=>$doingid));
		echo $datas['isrun'];
		exit();
	}
	
	public static function _buyaward($doingid = 0, $type = 0) {
		if (!self::$_G['uid']) exit('-5');
		global $_G;
		$doingid = intval($doingid);
		$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$doingid");
		if (empty($rs)) exit(self::$lang['nodoing']);
		if ($type) {
			$lang = self::$lang;
			$task = unserialize($rs['tasks']);
			$taskstatus = self::ismodule2('taskstatus', 'init', $rs);
			include template(self::$PLNAME.':buyaward');
			exit();
		}
		$num = intval($_GET['buynum']) >0 ? intval($_GET['buynum']) : exit('-9 error buynum');
		self::ckdoing($doingid, $rs);
		$rs['buynum'] = $num;
		$ck = self::ismodule2('buyaward', 'init', $rs);
		echo $ck;exit;
	}
	
	public static function _awardnum($id) {
		if (!self::$_G['uid']) exit('-5');
		$mynum = self::awardnum($id);
		echo $mynum;exit();
	}
	public static function awardnum($doingid, $doinfos = array(), $ck = 0) {
		$doingid = intval($doingid);
		if (empty($doinfos)) {
			$doinfos = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$doingid");
		}
		if (empty($doinfos)) return '-2';
		$ckfil = self::ismodule2('profile', 'init', $doinfos);
		if ($ckfil) return '-4';
		$time  = date('Ymd', TIMESTAMP);
		$mynum = DB::fetch_first("SELECT * FROM ".DB::table('lev_award_join')." WHERE doingid=$doingid AND uid=".self::$_G['uid']);
		if (!$mynum['isjoin']) self::xdoing($doingid, 2);//return '-3';
		if ($doinfos['freenum'] >0) {
			if ($mynum['ymd'] <$time) {
				$tdnum  = 1;
				$fnums  = $doinfos['freenum'];
				$isfree = 1;
			}else {
				$tdnum  = $mynum['tdnum'] +1;
				$fnums  = $doinfos['freenum'] - $mynum['freenum'];
				$isfree = $mynum['freenum'] +1;
			}
		}else {
			$tdnum = ($mynum['ymd'] <$time) ? 1 : ($mynum['tdnum'] +1);
		}
		if ($ck) {
			$secs = $mynum['uptime'] + self::$PL_G['openwait'] - TIMESTAMP;
			if ($secs >0) return '-808';
			if ($doinfos['maxjoinnum'] >0 && $tdnum >$doinfos['maxjoinnum']) return '-6';
			if ($fnums >0) {
				$_updata = array('ymd'=>$time, 'allnum'=>$mynum['allnum'] +1, 'tdnum'=>$tdnum, 'uptime'=> TIMESTAMP);
				if ($isfree) $_updata['freenum'] = $isfree; 
				DB::update('lev_award_join', $_updata, array('id'=>$mynum['id']));
				return '1';
			}elseif ($mynum['awardnum'] >0) {
				$_updata = array('ymd'=>$time, 'awardnum'=>$mynum['awardnum'] -1, 'allnum'=>$mynum['allnum'] +1, 'tdnum'=>$tdnum);
				$_updata['uptime'] = TIMESTAMP;
				DB::update('lev_award_join', $_updata, array('id'=>$mynum['id']));
				return '0';
			}
			return '-1';
		}
		$doinfos['myawardnumed'] = $mynum['awardnum'];
		$doinfos['myjoinid'] = $mynum['id'];
		$total = $mynum['awardnum'] + $fnums;
		//$tknum = intval(self::ismodule2('task', 'init', $doinfos));
		//$total+= $tknum >0 ? $tknum : 0;
		return $total;
	}
	
	public static function ckdoing($id, $rs = array()) {
		if (empty($rs)) {
			$id = intval($id);
			$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$id");
		}
		if (empty($rs)) exit('-9 nothing');
		if (self::$_G['adminid'] !=1) {
			if ((!$rs['isrun'] || $rs['status'] ==1) && self::$_G['uid'] !=$rs['uid']) exit('-10 reading'.$rs['status']);
		}
		if ($rs['isrun'] && !$rs['status']) {//设置模式或未通过审核不检测
			if ($rs['status'] ==1) exit('-11 checking');
			if ($rs['starttime'] >TIMESTAMP) exit('-12no start!');
			if ($rs['endtime'] && $rs['endtime'] <TIMESTAMP) exit('-13end!');
			if (self::ismodule2('stopip', 'init', $rs)) exit('-14 stop ip');
		}
		$insql = self::sqlinstr($rs['awards']);
		if ($insql) {
			$sql = "SELECT * FROM ".DB::table('lev_award_award')." WHERE id IN ($insql) AND uid=".$rs['uid'];
			$sql.= " ORDER BY ratios DESC";
			$r = DB::fetch_all($sql);
		}
		if (!$r) exit('-15no award goods');
		return $r;
	}
	public static function _openaward($id) {
		$id = intval($id);
		if ($id >0) {
			self::openaward($id);
		}else {
			exit('-44');
		}
	}
	public static function openaward($id) {
		$id = intval($id);
		$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$id");
		$r  = self::ckdoing($id, $rs);
		$isfree = self::awardnum($id, $rs, 1);
		if ($isfree <0) exit($isfree.'no awardnum');
		$award = self::isopenaward($r);//print_r($award);
		if ($award) {
			DB::update('lev_award', array('opnum'=>$rs['opnum'] +1, 'anum'=>$rs['anum'] +1), array('id'=>$id));
			$award['doingid'] = $id;
			$award['isfree']  = $isfree;
			self::dbaward($award);
			if ($rs['systype'] ==10) self::ismodule2('ggk_card', 'sendcard', $id);
			echo $award['id']; exit();
		}else {
			foreach ($r as $v) {
				if ($v['awardtype'] ==7) $noaward[] = $v['id'];
			}
			$awardid = $noaward ? $noaward[mt_rand(0, count($noaward) -1)] : 0;
			DB::update('lev_award', array('opnum'=>$rs['opnum'] +1), array('id'=>$id));
			echo $awardid;exit();
		}
	}
	public static function dbaward($award) {
		$descs = self::ismodule2('_'.$award['awardtype'], 'init', $award);
		$datas = array(
			'uid'     => self::$_G['uid'], 
			'bbsname' => self::$_G['username'],
			'title'   => $award['title'],
			'doingid' => $award['doingid'], 
			'awardid' => $award['id'], 
			'isfree'  => $award['isfree'], 
			'realprice'=> $award['realprice'],
			'descs'   => $descs, 
			'addtime' => TIMESTAMP
		);
		if ($award['awardtype'] ==5) $datas['status'] = 2;
		DB::insert('lev_award_award_log', $datas);
		$td = date('Ymd', TIMESTAMP);
		$updata = array(
			'ymd'      => $td,
			'tdtotals' => $award['ymd'] ==$td ? $award['tdtotals'] +1 : 1,
			'totals'   => $award['totals'] +1,
		);
		if ($award['ymd'] != $td) $updata['ydtotals'] = $award['tdtotals'];
		DB::update('lev_award_award', $updata, array('id'=>$award['id']));
	}
	public static function isopenaward($r) {
		foreach ($r as $k => $v) {
			if ($v['ratios'] <=0) {
				unset($r[$k]);
				continue;
			}
			$daynum = self::getdaynum($v);
			if ($daynum <=0) {
				unset($r[$k]);
				continue;
			}
			if ($v['adminid'] ==1) {
				$group = unserialize($v['usergroups']);
				if ($group[0] && !in_array(self::$_G['groupid'], $group)) {
					unset($r[$k]);
					continue;
				}
			}
			$ratios += $v['ratios'];
			if (!$award) {
				$awardnum = mt_rand(1, self::$PL_G['awardratio']);
				$ratio = round($awardnum / self::$PL_G['awardratio'] * 100, 5);
				if ($ratio && $ratio <= $v['ratios']) $award = $v;
			}
		}
		if (!$award && $ratios >=100) {
			$award = reset($r);
		}
		return $award;
	}
	public static function getdaynum($r) {
		if ($r['awardtotal']) $kcu = $r['awardtotal'] - $r['totals'];
		$num = $r['daynum'] - self::ymdinfo($r);
		if (is_numeric($kcu)) {
			//if ($kcu <=0) return '0';
			if (!$r['daynum'] || $kcu <$r['daynum']) return $kcu;
			return $num;
		}elseif (!$r['daynum']) {
			return TRUE;
		}else {
			return $num;
		}
	}
	
}













