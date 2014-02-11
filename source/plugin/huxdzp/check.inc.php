<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;
$uid = $_G['uid'];
$username = $_G['username'];
$dzpsetting = $_G['cache']['plugin']['huxdzp'];
$paymoney = "extcredits".$dzpsetting['paymoney'];
$paymoneyname = $_G['setting']['extcredits'][$dzpsetting['paymoney']]['title'];
$paymoneyunit = $_G['setting']['extcredits'][$dzpsetting['paymoney']]['uint'];
$paymoneynum = $dzpsetting['paymoneynum'];
$jlmoneya = "extcredits".$dzpsetting['jlmoneya'];
$jlmoneyaname = $_G['setting']['extcredits'][$dzpsetting['jlmoneya']]['title'];
$jlmoneyaunit = $_G['setting']['extcredits'][$dzpsetting['jlmoneya']]['uint'];
$jlmoneyb = "extcredits".$dzpsetting['jlmoneyb'];
$jlmoneybname = $_G['setting']['extcredits'][$dzpsetting['jlmoneyb']]['title'];
$jlmoneybunit = $_G['setting']['extcredits'][$dzpsetting['jlmoneyb']]['uint'];
$awmoney = $dzpsetting['awmoney'];
$cpadmin = unserialize($dzpsetting['allowgp']);
$playnum = $dzpsetting['playnum']-1;
$mycash = getuserprofile($paymoney);
$dzpname = $dzpsetting['pluginname'];
$open = $dzpsetting['open'];
$bbsnotice = $dzpsetting['bbsnotice'];
$bbsnoticetime = $dzpsetting['bbsnoticetime'];
$langninb = lang('plugin/huxdzp','ninb');
$langbuzu = lang('plugin/huxdzp','buzu');
$langweizhong = lang('plugin/huxdzp','weizhong');
$langloginmsg = lang('plugin/huxdzp','loginmsg');
$langgroupmsg = lang('plugin/huxdzp','groupmsg');
$langclosemsg = lang('plugin/huxdzp','closemsg');
$langcjwan = lang('plugin/huxdzp','cjwan');
$langcardmi = lang('plugin/huxdzp','cardmi');
$jlmsg = $dzpsetting['jlmsg'];
$mrawmsg = $dzpsetting['mrawmsg'];
$jpless = $dzpsetting['jpless'];
$feedopen = $dzpsetting['feedopen'];
$gailvs = trim($dzpsetting['gailv']);
$gailv = explode(',' ,$gailvs);
$loginstat = '';
$gamemsg = '';
$winmsg = '';
$mypalynum = 0;
if ($uid){
if (!in_array($_G['groupid'], $cpadmin)){
  if ($open == '1') {
    $uidnum=DB::fetch_first("SELECT * FROM ".DB::table('hux_dzp_user')." WHERE uid = '$uid'");
      if (!$uidnum) {
        DB::query("INSERT INTO ".DB::table('hux_dzp_user')."(uid,playnum) VALUE('$uid','$playnum')");
      }else{
        $mypalynum = $uidnum['playnum'];
        if ($mypalynum > 0) {
          if($mycash >= $paymoneynum){
            $loginstat = '&game=1';
          }else{
            if ($uidnum['cjqnum'] > 0) {
              $loginstat = '&game=1';
            }else{
              $loginstat = '&game=0';
              $gamemsg = $langninb.$paymoneyname.$langbuzu;
            }
          }
        }else{
        $loginstat = '&game=0';
        $gamemsg = $langcjwan;
        }
      }
  }else{
  $loginstat = '&game=0';
  $gamemsg = $langclosemsg;
  }
}else{
$loginstat = '&game=0';
$gamemsg = $langgroupmsg;
}
}else{
$loginstat = '&game=0';
$gamemsg = $langloginmsg;
}
if ($loginstat == '&game=1') {
            DB::query("UPDATE ".DB::table('hux_dzp_user')." SET playnum=playnum-1 WHERE uid='$uid'");
            $uidnums=DB::fetch_first("SELECT * FROM ".DB::table('hux_dzp_user')." WHERE uid = '$uid'");
            $sycjqnum = $uidnums[cjqnum];
            if ($uidnums['cjqnum'] > 0) {
              DB::query("UPDATE ".DB::table('hux_dzp_user')." SET cjqnum=cjqnum-1 WHERE uid='$uid'");
            }else{
				updatemembercount($uid , array($paymoney => $paymoney-$paymoneynum));
            }
            $gamemsg = $uidnums['playnum'];
	    $sqlgl = DB::query("SELECT * FROM ".DB::table('hux_dzp_jx')."");
	    while($rowgl=mysql_fetch_array($sqlgl)){
	      $data[] = array('numberid' => $rowgl[0],'numberweight' => $rowgl[3]);
	    }
	    $numberweight = 0;
	    $tempdata = array();
	    foreach ($data as $one) {
	      $numberweight += $one['numberweight'];
	      for ($i = 0; $i < $one['numberweight']; $i ++) {
	        $tempdata[] = $one;
	      }
	    }
	    $use = mt_rand(0, $numberweight-1);
	    $one = $tempdata[$use];
	    $winnumber = $one['numberid'];

            $jx = DB::fetch_first("SELECT * FROM ".DB::table('hux_dzp_jx')." where jid='$winnumber'");
	    $jid = $jx['jid'];
	    $jshow = $jx['jshow'];
	    if ($jid > 12) {
	      $winmsg = $langweizhong;
	    }else{
	      $jp = DB::fetch_first("SELECT * FROM ".DB::table('hux_dzp_jp')." where jid='$jid' and jpnum>0 ORDER BY rand() LIMIT 1");
		if (!$jp) {
		  DB::query("INSERT INTO ".DB::table('hux_dzp_userjp')." (jxname,jpname,jpstate,sqstate,uid,username,jptype,jfnum,jshow) VALUE('$mrawmsg','$jlmoneyaname$awmoney$jlmoneyaunit','1','1','$uid','$username','1','$awmoney','$jshow')");
		  updatemembercount($uid , array($jlmoneya => $jlmoneya+$awmoney));
		  $winmsg = $jlmsg.'['.$mrawmsg.']';
                  $jpmsg = $jlmoneyaname.$awmoney.$jlmoneyaunit;
		}else{
                  if ($jp['jptype'] == '0') {
		    DB::query("INSERT INTO ".DB::table('hux_dzp_userjp')." (jxname,jpname,jpstate,sqstate,uid,username,jptype,jfnum,jshow) VALUE('$jx[jname]','$jp[jpname]','0','0','$uid','$username','0','0','$jshow')");
                  }else{
		    $jlmoney = "extcredits".$jp[jptype];
		    DB::query("INSERT INTO ".DB::table('hux_dzp_userjp')." (jxname,jpname,jpstate,sqstate,uid,username,jptype,jfnum,jshow) VALUE('$jx[jname]','$jp[jpname]','1','1','$uid','$username','$jp[jptype]','$jp[jfnum]','$jshow')");
                    if ($jp['jptype'] == '-1') {
                      $cardmsg = $dzpname.$langcardmi.':'.$jp[jfnum];
                      notification_add($uid,0,$cardmsg,0,1);
                      sendpm($uid, $dzpname, $cardmsg, 1);
                    }elseif ($jp['jptype'] == '-2') {
		      $querymagic = DB::query("SELECT magicid FROM ".DB::table('common_member_magic')." WHERE uid='$uid' AND magicid='$jp[jfnum]'");
		      if(DB::num_rows($querymagic)) {
		        DB::query("UPDATE ".DB::table('common_member_magic')." SET num=num+1 WHERE uid='$uid' AND magicid='$jp[jfnum]'");
		      } else {
		        DB::query("INSERT INTO ".DB::table('common_member_magic')." (uid, magicid, num) VALUES ('$uid', '$jp[jfnum]', '1')");
		      }
                    }else{
		      updatemembercount($uid , array($jlmoney => $jlmoney+intval($jp[jfnum])));
                    }
                  }
                  if ($jpless == '1') {
		    DB::query("UPDATE ".DB::table('hux_dzp_jp')." SET jpnum=jpnum-1 WHERE jpid='$jp[jpid]'");
                  }
		  if ($bbsnotice == '1' && $jx['jshow'] == '1') {
		    $subject = '['.$dzpname.']'.$username.$jlmsg.'['.$jx['jname'].']'.$jp['jpname'];
		    $message = "{$_G[siteurl]}plugin.php?id=huxdzp:huxdzp";
		    $noticeend = $_G[timestamp]+$bbsnoticetime*24*3600;
		    DB::query("INSERT INTO ".DB::table('forum_announcement')." (author,subject,type,displayorder,starttime,endtime,message,groups) VALUES ('$username', '$subject', '1', '0', '$_G[timestamp]', '$noticeend', '$message', '')");
		    require './source/function/function_cache.php';
		    updatecache(array('announcements', 'announcements_forum'));
		  }
                  if ($feedopen == '1' && $jx['jshow'] == '1') {
        	    require_once libfile('function/feed');
		    $hft = dgmdate($_G['timestamp'], 'Y-m-d H:i');
		    if ($jp['jppic'] == '') {
		      $feed_pic = "source/plugin/huxdzp/images/nopic.gif";
		    } else {
		      $feed_pic = $jp['jppic'];
		    }
        	    feed_add('event', '{actor}({datetime):'.$dzpname.$jlmsg.':', array('datetime' => $hft), '', '', '<b>['.$jx[jname].']'.$jp[jpname].'</b>', array($feed_pic), array('plugin.php?id=huxdzp:huxdzp'));
                  }
		  $winmsg = $jlmsg.'['.$jx['jname'].']';
                  $jpmsg = $jp['jpname'];
		}
            }
}

if (CHARSET == 'big5') {
	$gamemsg = diconv($gamemsg,'big5','GBK');
	$winmsg = diconv($winmsg,'big5','GBK');
	$jpmsg = diconv($jpmsg,'big5','GBK');
} elseif (CHARSET == 'gbk') {
	$gamemsg = diconv($gamemsg,'GBK','utf-8');
	$winmsg = diconv($winmsg,'GBK','utf-8');
	$jpmsg = diconv($jpmsg,'GBK','utf-8');
}

echo "$loginstat|$gamemsg|$winmsg|$winnumber|$jpmsg|$sycjqnum";
?>