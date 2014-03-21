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

class _getbase_module {

	public static function _init($uid = 0) {
		global $_G;
		$lev_lang = lev_base::$lang;
		$doingid = intval($_GET['doingid']);
		if ($doingid <=0) exit($lev_lang['noact'].' did!');
		$res  = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id='{$doingid}'");
		if ($_G['adminid'] !=1) {
			if ($res['uid'] !=$_G['uid']) exit($lev_lang['noact'].' adid!');
		}
		$mbp  = unserialize($res['mbprofile']);
		$uid  = intval($uid);
		$sql  = "SELECT *
				FROM ".DB::table('common_member')." a LEFT JOIN ".DB::table('common_member_profile')." b 
				ON(a.uid=b.uid) WHERE a.uid='$uid'";
		$user = DB::fetch_first($sql);
		if (empty($user))  exit($lev_lang['noact'].' noinfo!');
		$sql = "SELECT * FROM ".DB::table('common_member_profile_setting')." WHERE available=1";
		$fied = DB::fetch_all($sql, array(), 'fieldid');
		$html = '<div style="padding:5px;height:220px;overflow-x:hidden;overflow-y:scroll;border-bottom:1px dotted #CDCDCD">';
		$html.= '<div style="border-top:1px dotted #CDCDCD"><table>';
		if ($mbp) {
			foreach ($mbp as $r) {
				$html .= '<tr><th style="text-align:right;padding:3px;">'.$fied[$r]['title'].$lev_lang['mh'].'</th><td>'.$user[$r].'</td></tr>';
			}
		}
		$html .= '</table></div>';
		$html .= '<div style="border-top:1px dotted #CDCDCD"><table>';
		if (!in_array('realname', $mbp))
		$html .= '<tr><th style="text-align:right;padding:3px;">'.$fied['realname']['title'].$lev_lang['mh'].'</th><td>'.$user['realname'].'</td></tr>';
		if (!in_array('address', $mbp))
		$html .= '<tr><th style="text-align:right;padding:3px;">'.$fied['address']['title'].$lev_lang['mh'].'</th><td>'.$user['address'].'</td></tr>';
		if (!in_array('zipcode', $mbp))
		$html .= '<tr><th style="text-align:right;padding:3px;">'.$fied['zipcode']['title'].$lev_lang['mh'].'</th><td>'.$user['zipcode'].'</td></tr>';
		if (!in_array('mobile', $mbp))
		$html .= '<tr><th style="text-align:right;padding:3px;">'.$fied['mobile']['title'].$lev_lang['mh'].'</th><td>'.$user['mobile'].'</td></tr>';
		if (!in_array('telephone', $mbp))
		$html .= '<tr><th style="text-align:right;padding:3px;">'.$fied['telephone']['title'].$lev_lang['mh'].'</th><td>'.$user['telephone'].'</td></tr>';
		if (!in_array('email', $mbp))
		$html .= '<tr><th style="text-align:right;padding:3px;">Email'.$lev_lang['mh'].'</th><td>'.$user['email'].'</td></tr>';
		$html .= '</table></div></div>';
		echo $html;
	}
	
}

