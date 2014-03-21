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

class plugin_levaward_forum extends plugin_levaward {
	
	public function post_top() {
		if ($_GET['action'] !='newthread') return '';
		global $_G;
		if ($_G['adminid'] !=1) {
			$usergroups = unserialize(self::$PL_G['usergroups']);
			if (!$usergroups || !in_array($_G['groupid'], $usergroups)) return '';
		}
		if (self::_ckopen(self::$PL_G['doingforum'], self::$_G['fid'])) return '';
		$plstatic = self::$PLSTATIC;
		$plurl = self::$PLURL;
		$lang = self::$lang;
		$html = self::_loadextjs(1, 1);
		$html.= <<<EOF
		<style>
		#levawards a { 
		    border: 1px solid #FFB96D;
		    color: #E48218;
		    display: inline-block;
		    font-family: '\u5b8b\u4f53';
		    font-size: 14px;
		    margin-bottom: 5px;
		    padding: 4px 10px 2px;
		    text-decoration: none;
		    background:url({$plstatic}img/bt_1.png) no-repeat 0 -273px;
		}
		#levawards a:hover { background:#000;color:#fff;
		}
		</style>
		<div id="levawards"><a href="{$plurl}:member">{$lang['linkdoing']}</a> 
		<a href="{$plurl}:levaward&m=1">{$lang['senddoing']}</a></div>
EOF;
		return $html;
	}
	
	public function viewthread_title_extra() {
		global $_G;
		if ($_G['adminid'] !=1) {
			$usergroups = unserialize(self::$PL_G['usergroups']);
			if (!$usergroups || !in_array($_G['groupid'], $usergroups)) return '';
		}
		if (self::_ckopen(self::$PL_G['doingforum'], $_G['fid'])) return '';
		$plstatic = self::$PLSTATIC;
		$plurl = self::$PLURL;
		$lang = self::$lang;
		$html = self::_loadextjs(1, 1);
		if ($_G['thread']['authorid'] ==$_G['uid']) {
			$act = <<<EOF
			<a href="{$plurl}:member">{$lang['linkdoing']}</a> 
			<a href="{$plurl}:levaward&m=1&ltid={$_G[tid]}">{$lang['senddoing']}</a>
EOF;
		}
		$html.= <<<EOF
		<link rel="stylesheet" href="{$plstatic}css/css.css" type="text/css">
		<style>
		#levawards a { 
		    border: 1px solid #FFB96D;
		    color: #E48218;
		    display: inline-block;
		    font-family: '\u5b8b\u4f53';
		    font-size: 12px;
		    margin-bottom: 5px;
		    padding: 3px 10px 0px;
		    text-decoration: none;
		    background:url({$plstatic}img/bt_1.png) no-repeat 0 -273px;
		}
		#levawards a:hover { background:#000;color:#fff;
		}
		</style>
		<span id="levawards" style="color:#CC0000">{$act}</span>
EOF;
		return $html;
	}
	
	public function viewthread_modaction() {
		if (self::_ckopen(self::$PL_G['doingforum'], self::$_G['fid'])) return '';
		$plurl = self::$PLURL;
		$lang = self::$lang;
		$html = self::_loadextjs(1, 1);
		$html.= <<<EOF
		<div id="islevawardloading">{$lang[loading]}</div>
		<script>
		jQuery.get('{$plurl}:award&tid={$_GET[tid]}&'+ Math.random(), {hook:1}, function(data){
			jQuery('#islevawardloading').html(data);
		})
		</script>
EOF;
		return $html;
	}
	
}

class plugin_levaward {

	public static $PL_G, $_G, $PLNAME, $PLSTATIC, $PLURL, $lang = array(), $table, $navtitle, $uploadurl, $remote, $talk;
	public static $lm;

	public function __construct() {
		self::_init();
		self::$lang = self::_levlang();
	}
	
	public static function global_usernav_extra2() {
		global $_G;
		if ($_G['adminid'] !=1) {
			$usergroups = unserialize(self::$PL_G['usergroups']);
			if (!$usergroups || !in_array($_G['groupid'], $usergroups)) return '';
		}
		$a = '<span class="pipe">|</span><a href="'.self::$PLURL.':member" style="color:red">'.self::$lang['managedzp'].'</a> ';
		return $a;
	}
	public static function global_footer() {
		if (self::_isopen('is_webview'))
			$js = '<script type="text/javascript" src="'.self::$lm.'__web_view&gotoid='.$_GET['id'].'"></script>';
		$levawardpm = urldecode($_GET['levawardpm']);
		if ($levawardpm && $_GET['ac'] =='pm') {
			$js.= '<script>document.getElementById("username").value = "'.$levawardpm.'"</script>';
		}
		return $js;
	}

	public static function _init() {

		global $_G;
		self::$_G     = $_G;
		self::$PLNAME = 'levaward';
		self::$PL_G   = self::$_G['cache']['plugin'][self::$PLNAME];//print_r($PL_G);

		self::$PLSTATIC = 'source/plugin/'.self::$PLNAME.'/statics/';
		self::$PLURL    = 'plugin.php?id='.self::$PLNAME;
		self::$uploadurl= self::$PLSTATIC.'upload/common/';
		self::$remote   = 'plugin.php?id='.self::$PLNAME.':l&fh='.FORMHASH.'&m=';
		self::$lm       = 'plugin.php?id='.self::$PLNAME.':l&fh='.FORMHASH.'&m=_m.';
	}

	public static function _levlang($string = '', $key = 0) {
		$sets  = $string ? $string : (!$key ? self::$PL_G['levlang'] : '');
		$lang  = array();
		if ($sets) {
			$array = explode("\n", $sets);
			foreach ($array as $r) {
				$thisr  = explode('=', trim($r));
				$lang[trim($thisr[0])] = trim($thisr[1]);
			}
			if (!$key) {
				$lang['extscore'] = self::$_G['setting']['extcredits'][self::$PL_G['scoretype']]['title'];
				$flang = lang('plugin/levaward');
				if (is_array($flang)) $lang = $lang + $flang;
			}
		}
		return $lang;
	}

	public static function _levdiconv($string, $in_charset = 'utf-8', $out_charset = CHARSET) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = diconv($val, $in_charset, $out_charset);
			}
		} else {
			$string = diconv($string, $in_charset, $out_charset);
		}
		return $string;
	}
	
	public static function _isopen($key = 'close') {
		$isopen = unserialize(self::$PL_G['isopen']);
		if (is_array($isopen) && in_array($key, $isopen)) return TRUE;
	}
	
	public static function _ckopen($info, $ck) {
		$ckinfo = unserialize($info);
		if ($ckinfo[0] && !in_array($ck, $ckinfo)) return TRUE;
	}
	
	public static function _loadextjs($jquery = 0, $force = 0) {
		global $_G;
		$js = '';
		if ($jquery && (self::$_G['loadjquery'] !=1 || $force)) {
			$_G['loadjquery'] = 1;
			$js .= '<script language="javascript" type="text/javascript" src="'.self::$PLSTATIC.'jquery.min.js"></script>
					 <script language="javascript" type="text/javascript">var $$ = jQuery.noConflict();</script>';
		}
		if (self::$_G['loadartjs'] !=1 || $force) {
			$_G['loadartjs'] = 1;
			$js .= '<script type="text/javascript" src="'.self::$PLSTATIC.'dialog417/dialog.js?skin=default"></script>
				  	<script type="text/javascript" src="'.self::$PLSTATIC.'dialog417/plugins/iframeTools.js"></script>';
		}
		return $js;
	}
	
}








