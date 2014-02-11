<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$VeRsIoN = DB::result_first("select version from ".DB::table('common_plugin')." WHERE identifier='huxdzp'");
$dzp_root = "source/plugin/huxdzp";
global $_G;
$action = empty($_GET['action']) ? '' : dhtmlspecialchars(addslashes($_GET['action']));
$atclass[$action] = "class='a'";
$act = '';
if ($action == 'add') {
$act = 'add';
}elseif ($action == 'edit') {
$act = 'edit';
}
$uid = $_G['uid'];
$adminid = $_G['adminid'];
$username = $_G['username'];
$dzpsetting = $_G['cache']['plugin']['huxdzp'];
$notice = $dzpsetting['notice'];
$czurl = $dzpsetting['czurl'];
$paymoney = "extcredits".$dzpsetting['paymoney'];
$paymoneynum = $dzpsetting['paymoneynum'];
$paymoneyname = $_G['setting']['extcredits'][$dzpsetting['paymoney']]['title'];
$paymoneyunit = $_G['setting']['extcredits'][$dzpsetting['paymoney']]['uint'];
$jlmoneya = "extcredits".$dzpsetting['jlmoneya'];
$jlmoneyaname = $_G['setting']['extcredits'][$dzpsetting['jlmoneya']]['title'];
$jlmoneyaunit = $_G['setting']['extcredits'][$dzpsetting['jlmoneya']]['uint'];
$jlmoneyb = "extcredits".$dzpsetting['jlmoneyb'];
$jlmoneybname = $_G['setting']['extcredits'][$dzpsetting['jlmoneyb']]['title'];
$jlmoneybunit = $_G['setting']['extcredits'][$dzpsetting['jlmoneyb']]['uint'];
$jlmoneynamea = $_G['setting']['extcredits'][1]['title'];
$jlmoneynameb = $_G['setting']['extcredits'][2]['title'];
$jlmoneynamec = $_G['setting']['extcredits'][3]['title'];
$jlmoneynamed = $_G['setting']['extcredits'][4]['title'];
$jlmoneynamee = $_G['setting']['extcredits'][5]['title'];
$jlmoneynamef = $_G['setting']['extcredits'][6]['title'];
$jlmoneynameg = $_G['setting']['extcredits'][7]['title'];
$jlmoneynameh = $_G['setting']['extcredits'][8]['title'];
$playnum = $dzpsetting['playnum'];
$dzpname = $dzpsetting['pluginname'];
$mycash = getuserprofile($paymoney);
$jlmsg = $dzpsetting['jlmsg'];
$picwidth = $dzpsetting['picwidth'];
$uc = $dzpsetting['ucroot'];
$zjlistnum = $dzpsetting['nums'];
$shownew = $dzpsetting['shownew'];
$shownewnum = $dzpsetting['shownewnum'];
$msguid = $dzpsetting['msguid'];
$showgailv = $dzpsetting['showgailv'];
$gundong = $dzpsetting['gundong'];
$gundongh = $dzpsetting['gundongh'];
$gundongs = $dzpsetting['gundongs'];
$daohang = $dzpsetting['daohang'];
$daohangname = $dzpsetting['daohangname'];
$daohangtxt = $dzpsetting['daohangtxt'];
$langshenqingmsg = lang('plugin/huxdzp','shenqingmsg');
$langfafangmsg = lang('plugin/huxdzp','fafangmsg');
$jpming = lang('plugin/huxdzp','jpming');
$jpend = lang('plugin/huxdzp','jpend');
$jp_num = lang('plugin/huxdzp','jp_num');
$jp_bigpic = lang('plugin/huxdzp','jp_bigpic');
$jppainum = $dzpsetting['jppainum'];
$jppaiwidth = intval(1/$jppainum*100)-1;
$tglinkshow = $dzpsetting['tglinkshow'];
$dzpstyle = $dzpsetting['dzpstyle'];
$dzpbg = '';
$dzpswf = '';
$dzptop = '';
$dzpleft = '';
$playernum = 0;
if ($dzpstyle == '3' && $action == '') {
	$zjnum = 5;
	$querynewjla = DB::query("SELECT username,jxname,jpname FROM ".DB::table('hux_dzp_userjp')." WHERE jshow=0 ORDER BY eid DESC LIMIT 11");
	while ($newjla = DB::fetch($querynewjla)){
		$newjldataa[] = $newjla;
	}
} else {
	$zjnum = $dzpsetting['zjnums'];
}
if ($dzpstyle == '1') {
  $dzpswf = 'huxdzp';
  $dzpswfheight = '372px';
  $dzptop = '60px';
  $dzpleft = '100px';
  $dzpbgheight = '482px';
} elseif ($dzpstyle == '2') {
  $dzpswf = 'huxdzp2';
  $dzpswfheight = '372px';
  $dzptop = '50px';
  $dzpleft = '55px';
  $dzpbgheight = '482px';
} elseif ($dzpstyle == '3') {
  $dzpswf = 'huxdzp2';
  $dzpswfheight = '550px';
  $dzptop = '65px';
  $dzpleft = '15px';
  $dzpbgheight = '660px';
}
$CJNum = 0;
$atclassa = '';
$atclassb = '';
$atclassc = '';
$atclassd = '';
$atclasse = '';
$str = '';
if ($uid){
  $uidnum=DB::fetch_first("SELECT * FROM ".DB::table('hux_dzp_user')." WHERE uid = '$uid'");
  if (!$uidnum) {
    DB::query("INSERT INTO ".DB::table('hux_dzp_user')."(uid,playnum) VALUE('$uid','$playnum')");
  }else{
    $sycjnum = $uidnum['playnum'];
    $sycjqnum = $uidnum['cjqnum'];
  }
}

$playernum=DB::result_first("SELECT count(*) FROM ".DB::table('hux_dzp_user')."");

$querynewjl = DB::query("SELECT username,jxname,jpname FROM ".DB::table('hux_dzp_userjp')." WHERE jshow=1 ORDER BY eid DESC LIMIT $zjnum");
while ($newjl = DB::fetch($querynewjl)){
$newjldata[] = $newjl;
}

$querynew = DB::query("SELECT username,d.uid FROM ".DB::table('hux_dzp_user')." d LEFT JOIN ".DB::table('common_member')." u ON d.uid = u.uid GROUP BY eid ORDER BY eid DESC LIMIT $shownewnum");
while ($new = DB::fetch($querynew)){
$newdata[] = $new;
}

$cjqnum = DB::fetch_first("SELECT cjqnum FROM ".DB::table('hux_dzp_user')." WHERE uid='$uid'");

if ($action == 'managejp') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }
  $atclassd = 'class=a';
  $query = DB::query("SELECT * FROM ".DB::table('hux_dzp_jp')." ORDER BY jpid DESC");
  while($dzp = DB::fetch($query)) {
    $mnlist[] = $dzp;
  }
}elseif ($action == 'jpdel') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }
  $jpid = addslashes($_GET['jpid']);
  DB::query("DELETE FROM ".DB::table('hux_dzp_jp')." WHERE jpid='$jpid'");
  showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=managejp');
}elseif ($action == 'add') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }
  if(submitcheck('addsubmit')){
    $jpname = dhtmlspecialchars(trim(addslashes($_GET['jpname'])));
    if(empty($jpname)){
      showmessage('huxdzp:name_empty');
    }
    $jid= intval(addslashes($_GET['jid']));
    $jptype= intval(addslashes($_GET['jptype']));
    $jfnum= addslashes($_GET['jfnum']);
    $jppic= addslashes($_GET['jppic']);
    $jptxt= addslashes($_GET['jptxt']);
    $jpnum= intval(addslashes($_GET['jpnum']));
    $setarr = array(
      'jpname' => $jpname,
      'jid' => $jid,
      'jptype' => $jptype,
      'jfnum' => $jfnum,
      'jppic' => $jppic,
      'jptxt' => $jptxt,
      'jpnum' => $jpnum,
    );
    DB::insert('hux_dzp_jp',$setarr);
    showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=managejp');
  }
}elseif ($action == 'edit') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }
  $jpid = addslashes($_GET['jpid']);
  $ed = DB::fetch_first("SELECT * FROM ".DB::table('hux_dzp_jp')." WHERE jpid='$jpid'");
  if(submitcheck('addsubmit')){
    $jpname = dhtmlspecialchars(trim(addslashes($_GET['jpname'])));
    if(empty($jpname)){
      showmessage('huxdzp:name_empty');
    }
    $jid= intval(addslashes($_GET['jid']));
    $jptype= intval(addslashes($_GET['jptype']));
    $jfnum= addslashes($_GET['jfnum']);
    $jppic= addslashes($_GET['jppic']);
    $jptxt= addslashes($_GET['jptxt']);
    $jpnum= intval(addslashes($_GET['jpnum']));
    DB::query("UPDATE ".DB::table('hux_dzp_jp')." SET jpname='$jpname',jid='$jid',jptype='$jptype',jfnum='$jfnum',jppic='$jppic',jptxt='$jptxt',jpnum='$jpnum' WHERE jpid='$jpid'");
    showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=managejp');
  }
}elseif ($action == 'info') {
  if(empty($uid)) showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));

  $eid = addslashes($_GET['eid']);
  $jf = DB::fetch_first("SELECT uid,jinfo FROM ".DB::table('hux_dzp_userjp')." WHERE eid='$eid'");
  if ($uid != $jf['uid'] && $adminid != '1') {
    showmessage('huxdzp:not_allow', 'plugin.php?id=huxdzp:huxdzp');
  }
  $lianxi = explode('|',$jf['jinfo']); 
  if(submitcheck('addsubmit')){
    $jinfo = dhtmlspecialchars(trim(addslashes($_GET['lianxia'])))."|".dhtmlspecialchars(trim(addslashes($_GET['lianxib'])))."|".dhtmlspecialchars(trim(addslashes($_GET['lianxic'])))."|".dhtmlspecialchars(trim(addslashes($_GET['lianxid'])))."|".dhtmlspecialchars(trim(addslashes($_GET['lianxie'])))."|".dhtmlspecialchars(trim(addslashes($_GET['lianxif'])));
    DB::query("UPDATE ".DB::table('hux_dzp_userjp')." SET jinfo='$jinfo' WHERE eid='$eid'");
    showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=myjl');
  }
}elseif ($action == 'jplist') {
  $atclassb = 'class=a';
  $totalgailv = DB::result_first("SELECT sum(jweight) FROM ".DB::table('hux_dzp_jx')."");
  $query = DB::query("SELECT * FROM ".DB::table('hux_dzp_jx')." ORDER BY jorder");
  while($dzp = DB::fetch($query)) {
  $jid = $dzp['jid'];
  $totaljp = DB::result_first("SELECT sum(jpnum) FROM ".DB::table('hux_dzp_jp')." WHERE jid='$jid'");
  if ($totaljp > 0) {
  	$totaljpming = $totaljp.$jpming;
  }else{
  	$totaljpming = "<span style='color:#999'>".$jpend."</span>";
  }
   if ($showgailv == '1') {
     $jweight = '('.round($dzp['jweight']/$totalgailv*100,2).'%)';
   }else{
     $jweight = '';
   }
   $queryb = DB::query("SELECT jpname,jppic,jptxt,jpnum FROM ".DB::table('hux_dzp_jp')." WHERE jid='$jid' and jpnum>0 ORDER BY jpid DESC");
   $jxlist.="<div class='bm'><div class='bm_h cl'><h2><font color=blue>$dzp[jname] $jweight</font> $totaljpming</h2></div><div class='bm_c'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>";
   $i = 0;
   while($dzpb = DB::fetch($queryb)) {
     if (empty($dzpb['jppic'])) {
       $jppic = $dzp_root."/images/nopic.gif";
     }else{
       $jppic = $dzpb['jppic'];
     }
     $jxlist.="<td width='1%'><a href='".$jppic."' target='_blank'><img src='".$jppic."' width='".$picwidth."' height='".$picwidth."' border='0' align='absmiddle' alt='$jp_bigpic' /></a></td><td width='".$jppaiwidth."%' valign='top' style='padding:2px;'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td><font color='red'>$dzpb[jpname]</font> <span title='$jp_num' style='color:#999'>($dzpb[jpnum])</span></td></tr><tr><td>$dzpb[jptxt]</td></tr></table></td>";
     $i++;
     if (!($i % $jppainum)) {
       $jxlist.="</tr><tr>";
     }
   }
   $jxlist.="</tr></table></div></div>";
  }
}elseif ($action == 'myjl') {
  if(empty($uid)) showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));

  $atclassc = 'class=a';
  $perpage = $zjlistnum;
  $n = DB::query("SELECT * FROM ".DB::table('hux_dzp_userjp')." WHERE uid='$uid'");
  $numd = DB::num_rows($n);
  $page = max(1, addslashes($_GET['page']));	
  $start = ($page-1)*$perpage;
  $queryd = DB::query("SELECT * FROM ".DB::table('hux_dzp_userjp')." WHERE uid='$uid' ORDER BY eid DESC limit $start,$perpage");
  while($resultd = DB::fetch($queryd)){
    $myjl[] = $resultd;
  }
  $multi = multi($numd, $perpage, $page, "plugin.php?id=huxdzp:huxdzp&action=myjl");
}elseif ($action == 'jlshow') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }
  $atclassf = 'class=a';
  $perpage = $zjlistnum;
  $n = DB::query("SELECT * FROM ".DB::table('hux_dzp_userjp')."");
  $numd = DB::num_rows($n);
  $page = max(1, addslashes($_GET['page']));	
  $start = ($page-1)*$perpage;
  $queryd = DB::query("SELECT * FROM ".DB::table('hux_dzp_userjp')." ORDER BY eid DESC limit $start,$perpage");
  while($resultd = DB::fetch($queryd)){
    $myjl[] = $resultd;
  }
  $multi = multi($numd, $perpage, $page, "plugin.php?id=huxdzp:huxdzp&action=jlshow");
}elseif ($action == 'ffsq') {
  if(empty($uid)) showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));

  $eid = addslashes($_GET['eid']);
  $jf = DB::fetch_first("SELECT uid,jptype,jfnum FROM ".DB::table('hux_dzp_userjp')." WHERE eid='$eid'");
  if ($uid != $jf['uid']) {
    showmessage('huxdzp:not_allow', 'plugin.php?id=huxdzp:huxdzp');
  }
  DB::query("UPDATE ".DB::table('hux_dzp_userjp')." SET sqstate='1' WHERE eid='$eid'");
  //sendpm($msguid, $dzpname, $username.$langshenqingmsg, $uid);
  notification_add($msguid,0,$username.$langshenqingmsg,0,1);
  showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=myjl');
}elseif ($action == 'managejpff') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }
  $atclasse = 'class=a';
  $perpage = $dzpsetting['nums'];
  $n = DB::query("SELECT * FROM ".DB::table('hux_dzp_userjp')." WHERE sqstate='1' and jpstate='0'");
  $numd = DB::num_rows($n);
  $page = max(1, addslashes($_GET['page']));	
  $start = ($page-1)*$perpage;
  $queryd = DB::query("SELECT * FROM ".DB::table('hux_dzp_userjp')." WHERE sqstate='1' and jpstate='0' ORDER BY eid DESC limit $start,$perpage");
  while($resultd = DB::fetch($queryd)){
    $mnjpff[] = $resultd;
  }
  $multi = multi($numd, $perpage, $page, "plugin.php?id=huxdzp:huxdzp&action=managejpff");
}elseif ($action == 'jpffsave') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }

  $eid = addslashes($_GET['eid']);
  $ffuid = addslashes($_GET['uid']);
  DB::query("UPDATE ".DB::table('hux_dzp_userjp')." SET jpstate='1' WHERE eid='$eid'");
  //sendpm($ffuid, $dzpname, $langfafangmsg, $uid);
  notification_add($ffuid,0,$langfafangmsg,0,1);
  showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=managejpff');
}elseif ($action == 'jlshowdel') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }

  $eid = addslashes($_GET['eid']);
  DB::query("DELETE FROM ".DB::table('hux_dzp_userjp')." WHERE eid='$eid'");
  showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=jlshow');
}elseif ($action == 'jlshowsave') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }

  $eid = addslashes($_GET['eid']);
  $query = DB::fetch_first("SELECT jshow FROM ".DB::table('hux_dzp_userjp')." WHERE eid='$eid'");
  if ($query['jshow'] == '0') {
    DB::query("UPDATE ".DB::table('hux_dzp_userjp')." SET jshow='1' WHERE eid='$eid'");
  }else{
    DB::query("UPDATE ".DB::table('hux_dzp_userjp')." SET jshow='0' WHERE eid='$eid'");
  }
  showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=jlshow');
}elseif ($action == 'datadel') {
  if($adminid != '1'){
    showmessage('huxdzp:not_allow');
  }

  $delname = addslashes($_GET['delname']);
  $deltype = addslashes($_GET['deltype']);
  if ($deltype == '0') {
    DB::query("DELETE FROM ".DB::table('hux_dzp_userjp')." WHERE jxname='$delname' and jpstate='1'");
  }elseif ($deltype == '1') {
    DB::query("DELETE FROM ".DB::table('hux_dzp_userjp')." WHERE jxname='$delname' and jpstate='0'");
  }elseif ($deltype == '2') {
    DB::query("DELETE FROM ".DB::table('hux_dzp_userjp')." WHERE jxname='$delname'");
  }
  showmessage('huxdzp:op_sus','plugin.php?id=huxdzp:huxdzp&action=jlshow');
}else{
  $atclassa = 'class=a';
}
include template('huxdzp:huxdzp');
?>