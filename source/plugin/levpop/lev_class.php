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
	
	public static $awardinfos, $starttime, $endtime, $isstart = FALSE;

	public function __construct() {
		parent::__construct();
		self::$starttime  = strtotime(parent::$PL_G['starttime']) ? strtotime(parent::$PL_G['starttime']) : 0;
		self::$endtime    = strtotime(parent::$PL_G['endtime'])   ? strtotime(parent::$PL_G['endtime'])   : 0;
		self::isstart();
	}
	
	public static function isegg($isbuy = 0) {
		$egg = 1;
		if (! parent::$_G ['uid']) {
			echo '{"result":1,"stat":"-1","pId":"0"}';
			exit ();
		}elseif (!self::$isstart) {
			echo '{"result":1,"stat":"-3","msg":"'.parent::$lang['notime'].parent::$PL_G['starttime'].' - '.parent::$PL_G['endtime'].'","pId":"0"}';
			exit;
		}
		self::$awardinfos = self::egginfo($egg);
		if (parent::$PL_G ['ratioaward'] > 10000000) parent::$PL_G ['ratioaward'] = 10000000;
		$rand    = mt_rand ( 0, parent::$PL_G['ratioaward'] );
		$randnum = 0;
		foreach ( self::$awardinfos as $k => $v ) {
			if (intval ( $v['goodsaward'] )) {
				$awardarea = explode ( '-', $v['goodsaward'] );
				if ($rand > $awardarea[0] && $rand < $awardarea[1]) {
					$randnum = $k;
					break;
				}
			}
		}
		if (! in_array ( self::$awardinfos[$randnum]['goodsname'], array ('1', '0' ) )) {
			$starttime = self::$starttime;
			$endtime = self::$endtime;
			$sql = "SELECT COUNT(*) FROM " . DB::table ( 'lev_pop' ) . " WHERE isgoods='$randnum$egg' AND addtime>" . $starttime . " AND addtime<" . $endtime;
			$awards = DB::result_first ( $sql );
			if ($awards >= self::$awardinfos[$randnum]['goodsnum']) {
				$randnum = 0;
			} else {
				$insert ['isgoods'] = $randnum.$egg;
			}
		}
		$isfree = self::getfree ($egg);
		
		if (self::$awardinfos[$randnum]['goodsname'] == "1")
			$goods = parent::$lang['extscore'];
		else
			$goods = self::$awardinfos[$randnum]['goodsname'];
		$insert['uid'] = parent::$_G ['uid'];
		$insert['num'] = $rand;
		$insert['isaward'] = $randnum;
		$insert['username'] = addslashes ( parent::$_G ['username'] );
		$insert['ip'] = parent::$_G ['clientip'];
		$insert['goods'] = $goods;
		$insert['awardscore'] = self::$awardinfos [$randnum] ['goodsnum'];
		$insert['addtime'] = time ();
		
		if ($isfree > 0) {
			$insert ['isfree'] = $egg;
			DB::insert ( "lev_pop", $insert );
			if (self::$awardinfos [$randnum] ['goodsname'] == "1") {
				parent::acscore ( self::$awardinfos [$randnum] ['goodsnum'] );
			}
		}else {
			$msgs = parent::$lang['nologin'];
			if ($eggscore = self::eggscore($egg)) {
				//$msgs .= parent::$lang['usescore'].$eggscore.parent::$lang['extscore'].parent::$lang['zaegg'];
			}
			$echo = '{"result":1,"stat":"-2","msg":"'.$msgs.'","pId":"0"}';
			echo $echo;
			exit ();
		}
		if ($goods) {
			if ($k > 6) $k = mt_rand(1, 6);
			$nums = $k.'-'.$k.'-'.$k;
			if ($insert ['isgoods']) {
				$echo = parent::$lang['gongxi'].' '.$goods.' 1';
				$echo = '{"result":1,"stat":"ok","msg":"'.$echo.'","num":"'.$nums.'","isreal":"1","res":"1","isaward":"1"}';
			}else {
				$echo = parent::$lang['gongxi'].' '.$goods.self::$awardinfos [$randnum] ['goodsnum'];
				$echo = '{"result":1,"stat":"ok","msg":"'.$echo.'","num":"'.$nums.'","isreal":"1","res":"1","isaward":"1"}';
			}
		}else {
			$nums = mt_rand(1, 6).'-'.mt_rand(4, 6).'-'.mt_rand(1, 3);
			$echo = '{"result":1,"stat":"ok","msg":"'.parent::$lang['noaward'].'","num":"'.$nums.'","isreal":"0","res":"0","isaward":"0"}';
		}
		echo $echo;
		exit ();
	}
	
	public static function egginfo($egg) {
		$awardinfos = array ();
		if (in_array ( $egg, array (1, 2, 3 ) )) {
			$awardinfo = explode ( "\r\n", parent::$PL_G ['egg' . $egg] );
			foreach ( $awardinfo as $k => $v ) {
				if ($k > 9) break;
				$data = explode ( '=', $v );
				$awardinfos [$k + 1] ['goodsname'] = $data [0];
				$awardinfos [$k + 1] ['goodsnum'] = $data [1];
				$awardinfos [$k + 1] ['goodsaward'] = $data [2];
			}
		}
		return $awardinfos;
	}

	public static function freeauto() {
		$isfreed = DB::result_first("SELECT COUNT(*) FROM ".DB::table("lev_pop")." WHERE isfree=9 AND uid=".parent::$_G['uid']." AND addtime>".strtotime(date('Y-m-d')));
		if (parent::$PL_G['freeauto'] - $isfreed >0) return TRUE;
	}
	
	public static function getfree($k = 0) {
		$eggs = explode('=', parent::$PL_G['isfree']);
		$freegroup = unserialize(parent::$PL_G['freegroup']);//print_r($freegroup);
		if (in_array(parent::$_G['member']['groupid'], $freegroup)) {
			$geggs = explode('=', parent::$PL_G['addfree']);
			$freeg = intval($geggs[$k-1]);
		} else {
			$freeg = 0;//echo $freeg;
		}
		$isfreed = DB::result_first("SELECT COUNT(*) FROM ".DB::table("lev_pop")." WHERE isfree='$k' AND uid=".parent::$_G['uid']." AND addtime>".strtotime(date('Y-m-d')));
		$isfree  = intval($eggs[$k-1]) + $freeg - $isfreed;
		if ($isfree < 0) $isfree = 0;
		return $isfree;
	}
	
	public static function isstart() {
		if (self::$starttime >time()) {
			return '-1';
		}elseif (self::$endtime <time()) {
			return '-2';
		}
		self::$isstart = TRUE;
	}

	public static function awardlist($order = 0, $limit = 15) {
		switch ($order) {
			case 1 :
				$order = 'isgoods DESC, awardscore DESC, addtime';
				break;
			default:
				$order = 'addtime';
				break;
		}
		$limit = ($limit <101 && $limit >0) ? $limit : 15;
		$awardlist = DB::fetch_all('SELECT * FROM '.DB::table('lev_pop').' WHERE awardscore>0 ORDER BY '.$order.' DESC LIMIT '.$limit);
		return $awardlist;
	}
	
	public static function geteggs() {
		$eggnum['egg1'] = self::getfree(1);
		echo json_encode($eggnum);exit;
	}
	
	public static function eggscore($k) {
		$eggs = explode('=', parent::$PL_G['usescore']);
		$regg = intval($eggs[$k-1]);
		if ($regg >0) return $regg;
	}
	
}













