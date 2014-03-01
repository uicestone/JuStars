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

if(defined('IN_ADMINCP')) loadcache('plugin');

class lev_base {

	public static $PL_G, $_G, $PLNAME, $PLSTATIC, $PLURL, $lang = array(), $table, $navtitle, $debug = FALSE;

	public function __construct() {
		//self::__debug();
		self::init();
		if (!self::$PL_G['is_open']) showmessage('plugin close!');;//is open plugin?
		self::$lang = self::levlang();
		self::navtitle();
	}

	public static function init() {
		$plugin_dir   = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));//print_r($arrs_dir);

		global $_G;
		self::$_G     = $_G;
		self::$PLNAME = trim(end($plugin_dir));
		self::$PL_G   = self::$_G['cache']['plugin'][self::$PLNAME];//print_r($PL_G);

		self::$PLSTATIC = 'source/plugin/'.self::$PLNAME.'/statics/';
		self::$PLURL    = 'plugin.php?id='.self::$PLNAME;
		self::$table    = '#'.self::$PLNAME.'#lev';
	}

	public static function run() {
		echo 'Welcome Levme!';
	}

	public static function levlang($string = '', $key = 0) {
		$sets  = $string ? $string : self::$PL_G['levlang'];
		$lang  = array();
		if ($sets) {
			$array = explode("\r\n", $sets);
			foreach ($array as $r) {
				$thisr  = explode("=", trim($r));
				$lang[trim($thisr[0])] = trim($thisr[1]);
			}
			if (!$key) {
				$lang['extscore'] = self::$_G['setting']['extcredits'][self::$PL_G['scoretype']]['title'];
				$flang = lang('plugin/'.self::$PLNAME);
				if (is_array($flang)) $lang = $lang + $flang;
			}
		}
		return $lang;
	}
	
	public static function navtitle() {
		$navs = self::$_G['setting']['navs'];
		foreach ((array)$navs as $v) {
			if (strpos($v['nav'], self::$PLNAME) !==FALSE) {
				self::$navtitle = $v['navname'];
				break;
			}
		}
	}

	public static function getpluginid() {
		$sql = "SELECT * FROM ".DB::table('common_plugin')." WHERE identifier='".self::$PLNAME."'";
		$pluginid = DB::result_first($sql);
		return $pluginid;
	}
	
	public static function getpluginvar($var) {
		$pluginid  = self::getpluginid();
		$sql = "SELECT * FROM ".DB::table('common_pluginvar')." WHERE pluginid={$pluginid} AND variable='{$var}'";
		$pluginvar = DB::fetch_first($sql);
		return $pluginvar;
	}
	
	public static function acscore($spend, $type = 0, $uid = 0) {
		$type = intval($type) ? intval($type) : self::$PL_G['scoretype'];
		$uid  = intval($uid) ? intval($uid) : self::$_G['uid'];
		if ($uid && intval($spend) && $type >0 && $type <9) {
			$score   = C::t('common_member_count')->fetch($uid);//print_r($score);
			$upscore = $score['extcredits'.$type] + $spend;
			if ($upscore >=0) {
				DB::update(
					'common_member_count', 
				array('extcredits'.$type=>$upscore),
				array('uid'=>$uid)
				);
				return TRUE;
			}
		}
	}

	public static function getimgsrc($tid) {
		if($attach = C::t('forum_attachment_n')->fetch_max_image('tid:'.$tid, 'tid', $tid)) {
			if($attach['remote']) {
				$imgsrc = self::$_G['setting']['ftp']['attachurl'].'forum/'.$attach['attachment'];
			} else {
				$imgsrc = self::$_G['siteurl'].self::$_G['setting']['attachurl'].'forum/'.$attach['attachment'];
			}
		}
		return $imgsrc;
	}
	
	public static function getconfig($key = '', $file = 'config', $defaultvalue = '') {
		static $config = array();
		if (!$key && $config) return $config;
		if (isset($config[$key]) && $config[$key]) return $config[$key];
	
		$m_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$file.'.inc.php';
		if(!file_exists($m_path)) return $defaultvalue;
		$config = include $m_path;
		if (!$key) return $config;
		if (!isset($config[$key]) || !$config[$key]) return $defaultvalue;
		return $config[$key];
	}

	//return $instr = 1,2,3,4,5,6
	public static function sqlinstr($str) {
		$array = unserialize($str);
		$instr = '';
		foreach ((array)$array as $v) {
			if (is_numeric($v)) $instr .= $v.',';
		}
		if ($instr) $instr = substr($instr, 0, -1);
		//$instr = implode(",", $array);
		return $instr;
	}

	public static function lev_load_class($classname, $class_path = '', $initialize = 1) {
		static $classes = array();
		if (empty($class_path)) $class_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'lev_class.php';

		$key = md5($class_path.$classname);
		if (isset($classes[$key])) {
			if (!empty($classes[$key])) {
				return $classes[$key];
			} else {
				return true;
			}
		}

		if (file_exists($class_path)) {
			include $class_path;
			$name = $classname;
			if ($initialize) {
				$classes[$key] = new $name;
			} else {
				$classes[$key] = true;
			}
			return $classes[$key];
		} else {
			return false;
		}
	}

	public static function doCurl($arr) {
			//没地址结束
			if(empty($arr['url'])) return;
	
			//没用户信息自动获取
			if ($arr['agent'] ==1) $arr['agent'] = $_SERVER['HTTP_USER_AGENT'];
	
			//没用户IP自动获取
			if (empty($arr['ip'])) $arr['ip'] = '';//ip();//$_SERVER["REMOTE_ADDR"];
	
			//没header 设置成假
			if (empty($arr['header'])) $arr['header'] = false;
			
			//来路设置
			if(empty($arr['referer'])) $arr['referer'] = 'http://web.qq.com';//http://fight.pet.qq.com/fightindex.html?sourceid=108&ADTAG=cop.innercop.qqsh-actionhall';//'
	
			//没cookie 保存设置成空
			if(empty($arr['cookiejar'])) $arr['cookiejar'] = '';
	
			//没cookie 读取设置成空
			if(empty($arr['cookiefile'])) $arr['cookiefile'] = '';
	
			//没发送post 设置成空
			if(empty($arr['post'])) $arr['post'] = '';
			
			//输出方式
			if (empty($arr['bisfer'])) $arr['bisfer'] = FALSE;
			
			//超时时间没有设置成常量默认
			if(empty($arr['time'])) {// $arr['time'] = 5;
				$arr['time'] = 5;
			}
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$arr['url']);	
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, $arr['bisfer']) ;		
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, $arr['header']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:{$arr['ip']}","CLIENT-IP:{$arr['ip']}"));
			
			//用户浏览器信息
			if ($arr['agent']) curl_setopt($ch, CURLOPT_USERAGENT, $arr['agent']);
			
			if (!empty($arr['httpheader'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $arr['httpheader']);
	
			//读取cookie
			if ($arr['cookiefile']) curl_setopt($ch, CURLOPT_COOKIEFILE, $arr['cookiefile']);
	
			//保存cookie
			if ($arr['cookiejar']) curl_setopt($ch, CURLOPT_COOKIEJAR, $arr['cookiejar']);
			
			//来路
			if ($arr['referer']) curl_setopt($ch, CURLOPT_REFERER, $arr['referer']);
	
			//post数据
			if ($arr['post']) curl_setopt($ch, CURLOPT_POSTFIELDS, $arr['post']);
			
			//发送cookie
			if (empty($arr['cookie'])) $arr['cookie'] = '';
			$arr['cookie'] .= 'wherefrom=108;pgvReferrer=http://game.108.qq.com/game.html';//水浒礼包
			//$arr['cookie'] .= 'wherefrom=201;ts_refer=ADTAGNMC.farm.act;pgvReferrer=http://game.108.qq.com/game.html';//农场礼包
			curl_setopt($ch, CURLOPT_COOKIE, $arr['cookie']);
	
			//多少秒超时
			curl_setopt($ch, CURLOPT_TIMEOUT, $arr['time']);
	
			$c_url = curl_exec($ch);
			curl_close($ch);
			return $c_url;
	}
		
	private static function __debug() {
		if (self::$debug) {
			error_reporting(E_ALL ^ E_NOTICE);//显示除去 E_NOTICE 之外的所有错误信息
			ini_set('display_errors', 1);
		}
	}

}













