<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
include("sys/system_init.php");
?>

<html><head><title>Aurora Online Judge [SourceCode]</title>
<meta name="description" content="Aurora Online Judge" />
<meta name="author" content="Kaustubh Karkare" />
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<link rel='shortcut icon' href='data/laptop_black.png' />
<link rel='stylesheet' type='text/css' href='data/style.css' /> 
<script src="data/jquery.js" type="text/javascript"></script> 
<script src="data/browser.js" type="text/javascript"></script> 
<script src="data/select.js" type="text/javascript"></script>

<script type="text/javascript" src="data/syntax-highlighter/shCore.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushCpp.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushCSharp.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushJava.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushJScript.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushPerl.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushPhp.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushPlain.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushPython.js"></script>
	<script type="text/javascript" src="data/syntax-highlighter/shBrushRuby.js"></script>
<link type="text/css" rel="stylesheet" href="data/syntax-highlighter/shCoreDefault.css"/>

<script>
var countdown = -1;
function step(){
	if(countdown==0) $("div#ajax-contest-status").html("Disabled");
	if(countdown>0) $("div#ajax-contest-time").html(parseInt(countdown/3600)+"h "+parseInt((countdown/60))%60+"m "+(countdown%60)+"s");
	else $("div#ajax-contest-time").html("NA");
	if(countdown>=0) countdown--;
	window.setTimeout("step();",1000);
	}
function url_get(q){
    s = window.location.search;
    var re = new RegExp('&'+q+'=([^&]*)','i');
    return (s=s.replace(/^\?/,'&').match(re)) ? s=s[1] : s='';
    }
function process(key,value){
	if(key=="refresh"&&value==1) window.location.reload();
	else if((key=="newclar"||key=="newclar2")&&value!="") alert(value);
	else if(key=="ajax-contest-status") $("div#"+key).html(value=="Active"?"CQM":value=="Passive"?"Practice":value);
	else if(key=="ajax-contest-time") countdown = parseInt(value);
	else $("div#"+key).html(value);
	}
function init(){
	if(BrowserDetect.browser!="Firefox") $("input[type=button]").css('padding','3px');
	$("code").each( function(index){
		$(this).html( "<div class='limit code'>"+$(this).html().replace(/<br>/,'')+"</div>" );
		$(this).attr('id','select_code_'+index).attr('title','Double click to select all code.');
		$(this).dblclick(function(index){ selectElement($(this).attr('id')); });
		} );
	}
function load(){
	data = eval("(<?php if($admin["ajaxrr"]==0) echo addslashes(action_ajaxrefresh(1)); ?>)");
	$.each(data,function(key,value){ process(key,value); });
	}
function reload(){
	$('#ajaxtimer').html('Contacting server via Ajax ...');
	$.getJSON("index.php",{action:"ajaxrefresh"},function(data){
		$('#ajaxtimer').html('Updating data ...');
		$.each(data,function(key,value){ process(key,value); });
		ajaxtimer = <?php echo $admin["ajaxrr"]; ?>;
		for(i=ajaxtimer;i>0;i--) window.setTimeout("$('#ajaxtimer').html('Updating data in "+i+" second(s).')",(ajaxtimer-i)*1000);
		window.setTimeout("reload();",ajaxtimer*1000);
		});
	}
function problem_search(){
	query = $('input#query').attr('value').toLowerCase();
	if(query.length>0){
		$('div.probindex div.probheaders1').slideUp(250);
		$('div.probindex div.probheaders2').slideDown(250);
		}
	else {
		$('div.probindex div.probheaders1').slideDown(250);
		$('div.probindex div.probheaders2').slideUp(250);
		}
	$('div.probindex div.problem').each(function(i){
		match=0;
		$(this).find('td').each( function(){
			if($(this).text().toLowerCase().indexOf(query)!=-1) match++;
			});
		if(match==0) $(this).slideUp(250); else $(this).slideDown(250);
		});
	}
function addslashes(str) {
	//str=str.replace(/\\/g,'\\\\');
	//str=str.replace(/\'/g,'\\\'');
	//str=str.replace(/\"/g,'\\"');
	//str=str.replace(/\0/g,'\\0');
	return str;
	}
scroll_lock = true;
$(document).ready(function(){
	$("#output").scroll(function(){ if(scroll_lock){
		$("#actual").scrollTop($("#output").scrollTop());
		$("#actual").scrollLeft($("#output").scrollLeft());
		}});
	$("#actual").scroll(function(){ if(scroll_lock){
		$("#output").scrollTop($("#actual").scrollTop());
		$("#output").scrollLeft($("#actual").scrollLeft());
		}});
	});
</script>

</head>
<body onLoad="init(); <?php if($admin["ajaxrr"]==0) echo "load();"; else echo "reload();"; ?> step();">

<!--
<input type='button' value='HTML' onClick="alert($('body').html());">
<!-- -->
<div style='position:fixed;top:5;right:5;font-size:10px;background:rgba(128,128,128,0.2);padding:2px;border-radius:5;' id='ajaxtimer'></div>
<center><h1>Aurora Online Judge</h1></center>
<center><table class='main'><tr><td class='side'>
	<div class='sidebox'><h3>Contest Status</h3><table><tr><th>Mode</th><th>Judgement</th><th>Timer</th></tr><tr><td><div id='ajax-contest-status'></div></td><td><div id='ajax-contest-judgement'></div></td><td><div id='ajax-contest-time'></div></td></tr></table></div>
	<div class='sidebox' id='ajax-problem'></div>
	<div class='sidebox'>
		<li><a href='?display=notice'>Important Notices</a></li>
		<li><a href='?display=faq'>Frequently Asked Questions</a></li>
		<?php if(0)echo "<li><a href='?display=scoreboard'>Main Scoreboard</a></li>"; ?>
		<?php if($_SESSION["tid"]!=0){ echo "<li><a href='?display=account'>Account Settings</a></li>"; } ?>
		<li><a href='?display=problem'>Problems Index</a></li>
		<li><a href='?display=clarifications'>Clarifications</a></li>
		<li><a href='?display=rankings'>Current Rankings</a></li>
		<li><a href='?display=submissions'>Submissions Status</a></li>
		<?php if($_SESSION["status"]=="Admin"){
			echo "<br>";
			echo "<li><a href='?display=adminsettings'>Administrator Settings</a></li>";
			if(0)echo "<li><a href='?display=admindata'>Data Commitment</a></li>";
			echo "<li><a href='?display=adminproblem'>Problem Settings</a></li>";
			echo "<li><a href='?display=adminteam'>Teams Settings</a></li>";
			echo "<li><a href='?display=admingroup'>Group Settings</a></li>";
			echo "<li><a href='?display=adminlogs'>Access Logs</a></li>";
			} ?>
	</div>
	<div class='sidebox' id='ajax-allsubmit'></div>
	<div class='sidebox' id='ajax-rankings'></div>
</td><td class='center'>
	<?php display_message(); ?>
	<div class='centerbox'><?php display_main(); ?><br><br></div>
</td><td class='side'>
	<div class='sidebox'><?php display_statusbox(); ?></div>
	<div class='sidebox' id='ajax-mysubmit'></div>
	<div class='sidebox' id='ajax-privateclar'></div>
	<div class='sidebox' id='ajax-publicclar'></div>
	<div class='sidebox'><i>Created by Kaustubh Karkare [<a href='http://192.168.111.111/' target='new'>SourceCode</a>].</i></div>
</td></table></center>
<a name="bottom"></a>
<script type="text/javascript">SyntaxHighlighter.all();</script>
</body>
</html>
<?php mysql_terminate(); ?>