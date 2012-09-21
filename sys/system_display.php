<?php

function display_main(){
	if(!isset($_GET["display"])) $_GET["display"]="notice";
	if($_GET["display"]=="register") display_register();
	else if($_GET["display"]=="clarifications") display_clarifications();
	else if($_GET["display"]=="account") display_account();
	else if($_GET["display"]=="problem") display_problem();
	else if($_GET["display"]=="submissions") display_submissions();
	else if($_GET["display"]=="rankings") display_rankings();
	else if($_GET["display"]=="code") display_code();
	else if($_GET["display"]=="scoreboard") display_scoreboard();
	
	else if($_GET["display"]=="adminsettings") display_adminsettings();
	else if($_GET["display"]=="admindata") display_admindata();
	else if($_GET["display"]=="adminproblem") display_adminproblem();
	else if($_GET["display"]=="adminteam") display_adminteam();
	else if($_GET["display"]=="admingroup") display_admingroup();
	else if($_GET["display"]=="adminlogs") display_adminlogs();
	else if($_GET["display"]=="doc") display_doc();
	else if($_GET["display"]=="faq") display_faq();
	else if($_GET["display"]=="notice") display_notice();
	else display_notice();
	}

function display_notice(){
	global $admin;
	echo "<center><h2>Important Notices</h2></center>";
	$edit = ((isset($_GET["edit"]) && $_GET["edit"]==1)?1:0);
	if($edit){
		echo "<form action='?action=noticeupdate' method='post'><textarea class='notice' name='notice'>";
		if(isset($admin["notice"]))	print stripslashes($admin["notice"]);
		echo "</textarea><br><br><center><input type='submit' value='Update Notice'> <input type='button' value='Clear Changes' onClick='window.location.reload();'> <input type='button' value='Cancel' onClick=\"window.location='?display=notice';\"></center></form>";
		}
	else {
		if(isset($admin["notice"])) $data = $admin["notice"]; else $data = "";
		$data = str_replace("\r","",$data);
		$data = eregi_replace("\n\n\n*","\n\n",$data);
		$data = eregi_replace("[\s\n]*$","",$data);
		$data = explode("\n\n",$data);
		foreach($data as $x){
			$y = explode("\n",$x);
			if(!isset($y[0])) continue;
			if(isset($y[0][0]) and $y[0][0]=="~" and $_SESSION["status"]!="Admin") continue;
			if(isset($y[0][0]) and $y[0][0]=="~") $y[0]=substr($y[0],1);
			echo "<br><table class='faq'><tr><th>".stripslashes($y[0])."</th></tr><tr><td>";
			for($i=1;$i<count($y);$i++) echo "<li>".stripslashes($y[$i])."</li>";
			echo "</td></tr></table>";
			}
		}
	if(!$edit && $_SESSION["status"]=="Admin") echo "<br><center><input type='button' value='Edit Notice' onClick=\"window.location='?display=notice&edit=1';\"></center>";
	}
function action_noticeupdate(){
	global $admin;
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Access Denied : You need to be an Administrator to perform this action."; return; }
	if(isset($_POST["notice"])){ $admin["notice"] = $_POST["notice"]; $_SESSION["message"][] = "Notice Updation Successful"; }
	else { $_SESSION["message"][] = "Notice Updation Error : Insufficient Data"; return; }
	}
	
function display_faq(){ include("sys/faq.html"); }
function display_doc(){ include("sys/doc.html"); }
	
function display_message(){
	global $currentmessage,$admin;
	if(empty($_SESSION["message"])) return;
	$currentmessage = $_SESSION["message"];
	echo "<div class='messagebox' onClick='$(this).slideUp(250);' title='Click to hide'>";
	if((isset($admin["mode"])&&$admin["mode"]=="Lockdown") && $_SESSION["status"]!="Admin") echo "Lockdown Mode";
	else foreach($_SESSION["message"] as $line) echo filter($line)."<br>";
	echo "</div>";
	$_SESSION["message"] = array();
	}

	

?>