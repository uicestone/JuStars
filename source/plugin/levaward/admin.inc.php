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

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')){
	exit('Access Denied');
}

require_once 'lev_enter.php';//.php文件通过程序转码。转码方法lev_base::levdiconv();不会出现乱码。

$fna    = str_replace('.inc.php', '', basename(__FILE__));
$theurl = 'admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier='.$PLNAME.'&pmod='.$fna.'&fh='.FORMHASH;

if (FORMHASH ==$_GET['fh']) {
	if ($_GET['setstatus']) {
		ob_clean();
		$opid = intval($_GET['opid']);
		$rs = DB::fetch_first("SELECT * FROM ".DB::table('lev_award')." WHERE id=$opid");
		if ($rs['status']) { $status = 0; }else { $status = 1; }
		DB::update('lev_award', array('status'=>$status), array('id'=>$opid));
		echo $status;exit();
	}elseif ($_GET['setoption']) {
		ob_clean();
		$opid = intval($_GET['opid']);
		$name = trim($_GET['name']);
		$value= trim(lev_class::levdiconv($_GET['value']));
		DB::update('lev_award', array($name=>$value), array('id'=>$opid));
		exit('1');
	}elseif ($_GET['deldata']) {
		ob_clean();
		lev_class::_deldoings($_GET['opid']);
		exit('1');
	}
}

$sts = array('<span style="color:green">通过</span>', '<span style="color:red">待审</span>');
$ads = lev_class::levpages('lev_award', "1 ORDER BY addtime DESC", 20, 0, $theurl);
foreach ($ads['lists'] as $r) {
	$r = lev_class::levdiconv($r, CHARSET, 'utf-8');
	$addtime = lev_class::levdiconv(dgmdate($r['addtime'], 'u'), CHARSET, 'utf-8');
	if ($r[endtime] && $r[endtime] < TIMESTAMP) {
		$doingstatus = '<span style="color:#999">活动结束</span>';
	}elseif ($r[starttime] >TIMESTAMP){
		$doingstatus = '<span>还有 <b style="color: #f60;">'.intval(($r[starttime]-TIMESTAMP)/3600/24).'</b> 天开始</span>';
	}else{
		if ($r[endtime]){
			$doingstatus = '还有 <b style="color: #CC0000;">'.intval(($r[endtime]-TIMESTAMP)/3600/24).'</b> 天结束';
		}else{
			$doingstatus = '<span style="color:green">长期活动</span>';
		}
	}
	$adlists .= <<<EOF
	<tr><td>{$r['id']}</td>
	<td><a href="javascript:;" onclick="setstatus(this, {$r['id']}, 'status')" title="点击切换">{$sts[$r['status']]}</a></td>
	<td><a href="home.php?mod=space&uid={$r['uid']}" target="_blank">{$r['bbsname']}</a></td>
	<td><input type="text" value="{$r['title']}" style="width:auto;" title="{$r['title']}"
	 onchange="setoption(this, {$r['id']}, 'title', this.value)">
	</td>
	<td>
	<input type="text" value="{$r['tid']}" style="width:40px;"
	 onchange="setoption(this, {$r['id']}, 'tid', this.value)"></td>
	<td>
	<input type="text" value="{$r['joinnum']}" style="width:60px;"
	 onchange="setoption(this, {$r['id']}, 'joinnum', this.value)"></td>
	<td>
	<input type="text" value="{$r['thinknum']}" style="width:60px;"
	 onchange="setoption(this, {$r['id']}, 'thinknum', this.value)"></td>
	<td>
	<input type="text" value="{$r['topnum']}" style="width:60px;"
	 onchange="setoption(this, {$r['id']}, 'topnum', this.value)"></td>
	<td>{$doingstatus}</td>
	<td>{$addtime}</td>
	<td>
	<a href="{$PLURL}:award&doingid={$r['id']}" target="_blank">查看</a> | 
	<a href="{$PLURL}:{$PLNAME}&m=1&edtid={$r['id']}" target="_blank">编辑</a> | 
	<a href="javascript:;" onclick="deldata(this, {$r['id']})">删除</a>
	</td>
	</tr>
EOF;
}

$formhash = FORMHASH;
$html = <<<EOF

<link rel="stylesheet" href="{$PLSTATIC}css/css.css" type="text/css">
<script type="text/javascript" src="{$PLSTATIC}dialog417/dialog.js?skin=default"></script>
<script type="text/javascript" src="{$PLSTATIC}dialog417/plugins/iframeTools.js"></script>
<script type="text/javascript" src="{$PLSTATIC}jquery.min.js"></script>
<script type="text/javascript" src="static/js/common.js?WT2"></script>
<script type="text/javascript" src="static/js/calendar.js"></script>

<script type="text/javascript">var $$ = jQuery.noConflict();</script>
<style>
#levad .none {display:none;}
#levad .rfm {
    border-bottom: 1px dotted #CDCDCD;
}
#levad .rfm .adlists td {
    border-bottom: 1px dotted #CDCDCD;
}
#levad .rfm th {
    padding-right: 10px;
    text-align: right;
    width: 10em;
}
#levad .rfm th, .rfm td {
    line-height: 24px;
    padding:4px;
    vertical-align: middle;
}
#levad input, #levad select, #levad textarea {width:220px;}
#levad .sa a {
	display: inline-block;
	vertical-align: middle;
	position: relative;
	border: 2px solid #FFF;
	text-align:center;
}
#levad .sa a:hover {border: 2px solid #006A92;}
#levad .sa a.simg {border: 2px solid #006A92;}
#levad .sa a.simg em {
	display: block;
	width: 15px;
	height: 15px;
	position: absolute;
	right: -1px;
	bottom: -1px;
	overflow: hidden;
	background: url({$PLSTATIC}img/temper_120718.gif) no-repeat;
}
#levad .userg input {width:auto;margin:0 0 0 5px;vertical-align: middle;}


</style>
<div id="levad">

<div class="rfm">
<table>
<tr><th><a href="{$PLURL}:{$PLNAME}&m=1" target="_blank"><b>添加活动</b></a></th><td></td></tr>
</table>
</div>
<div class="rfm">
<table class="adlists" width="100%">
<tr>
<td>ID</td><td>状态</td><td>发起人</td><td>活动主题</td><td>帖子ID</td><td>参加人</td><td>感兴趣</td><td>很赞</td><td>活动状态</td>
<td>添加时间</td><td>管理操作</td>
</tr>
{$adlists}
</table>
</div>

<div class="rfm">
<table width="100%">
<tr>
<th><a href="plugin.php?id=levaward:levaward&m=1" target="_blank"><b>添加活动</b></a></th>
<td style="text-align:right">{$ads['pages']}</td>
</tr>
</table>
</div>

<script>

function setstatus(obj, id, name) {
	//var name = name ? name : '';
	$$.get('{$theurl}&setstatus=1', {opid:id, name:name, t:Math.random()}, function(data){
		switch(parseInt(data)) {
			case 0 :
				$$(obj).html('{$sts[0]}');
				break;
			case 1 :
				$$(obj).html('{$sts[1]}');
				break;
		}
		art.dialog.tips('操作成功！');
	});
}

function setoption(obj, id, name, value) {
	$$.get('{$theurl}&setoption=1', {opid:id, name:name, value:value, t:Math.random()}, function(data){
		art.dialog.tips('操作成功！');
	});
}

function deldata(obj, id) {
	if (confirm('您确定要删除吗？'+ id)) {
		$$.get('{$theurl}&deldata=1', {opid:id, t:Math.random()}, function(data) {
			art.dialog.tips('操作成功！');
			$$(obj).parent().parent().fadeOut('slow');
		});
	}
}

function toMao(val){
	var myY = $$("#"+val).offset().top
	$$("html,body").stop().animate({ scrollTop:myY},500);
}
</script>

</div>
EOF;
echo lev_class::levdiconv($html);
?>











