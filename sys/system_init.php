<?php

if(file_exists("sys/system_config.php")) include("sys/system_config.php"); else exit;

// Include System Files
$f=opendir("sys");
while($e=readdir($f)){
	if($e=="."||$e=="..") continue;
	if(eregi("^system",$e) && file_exists("sys/$e") && $e!="system_init.php" && $e!="system_config.php")
		include("sys/$e");
	}
closedir($f);

$admin = array();
$currentmessage;
$ajaxlogout=0;
$fullresult = array("AC"=>"Accepted","WA"=>"Wrong Answer","PE"=>"Presentation Error","CE"=>"Compilation Error","RTE"=>"Run Time Error","TLE"=>"Time Limit Exceeded","DQ"=>"Disqualified","NA"=>"Unjudged");
$extension = array("Brain"=>"b","C"=>"c","C++"=>"cpp","C#"=>"cs","Java"=>"java","JavaScript"=>"js","Pascal"=>"pas","Perl"=>"pl","PHP"=>"php","Python"=>"py","Ruby"=>"rb","Text"=>"txt");
$brush = array("Brain"=>"text","C"=>"c","C++"=>"cpp","C#"=>"csharp","Java"=>"java","Java","JavaScript"=>"js","Pascal"=>"text","Perl"=>"perl","PHP"=>"php","Python"=>"python","Ruby"=>"ruby","Text"=>"text");
$invalidchars = "[^A-Za-z0-9`~!@#$%^&*()_+|=\\\{\}\[\];:<>?,./ 	\n-]";
$invalidchars_js = eregi_replace("\n","\\n",$invalidchars);
$execoptions = array(""=>"None","P" => "Lenient Presentation Check");
$defaultlang = "C++";
$maxcodesize = 1024*100; // 100KB - Max size of source code
$maxfilesize = 3*1024*1024; // 3MB - Max size of input, output, statement, image
// application/octet-stream

// To add new results or new languages, the only change reqd is to add them to the $fullresult and $extension arrays above.

session_start();
$sessionid = (SID=="")?$_REQUEST["PHPSESSID"]:eregi_replace("PHPSESSID=","",SID);

if(!isset($_SESSION["SCRIPT_NAME"])) $_SESSION["SCRIPT_NAME"] = $_SERVER["SCRIPT_NAME"];
if($_SESSION["SCRIPT_NAME"]!=$_SERVER["SCRIPT_NAME"]){ // Context Switching
	$temp = array("SCRIPT_NAME"=>$_SERVER["SCRIPT_NAME"]);
	}

$phpself = $_SERVER["SERVER_ADDR"].$_SERVER["PHP_SELF"];
if(!isset($_SESSION["tid"])) $_SESSION = array("tid"=>0,"teamname"=>"","status"=>"","time"=>time(),"ghost"=>0);
if(!isset($_SESSION["message"])) $_SESSION["message"] = array();

mysql_initiate();

if(isset($_SESSION["redirect"]) && !empty($_SESSION["redirect"])){ header("Location: ".$_SESSION["redirect"]); unset($_SESSION["redirect"]); exit; }

if($_SESSION["status"]!="Admin" && (!isset($_SESSION["ghost"])||!$_SESSION["ghost"]) && (!isset($_GET["action"])||$_GET["action"]!="ajaxrefresh")) mysql_query("INSERT INTO logs VALUES (".(time()).",'".$_SERVER["REMOTE_ADDR"]."','".$_SESSION["tid"]."','".addslashes(json_encode($_GET))."')");

if(isset($_GET["download"]) and in_array($_GET["download"],array("code","output")) and isset($_GET["rid"]) and is_numeric($_GET["rid"])){
	if($_SESSION["status"]!="Admin" and $_GET["download"]=="output"){
		echo "Aurora Online Judge : You do not have the authorization to download this resource.";
		exit;
		}
	if($_SESSION["status"]=="Admin") $t = mysql_query("SELECT * FROM runs WHERE rid=".$_GET["rid"]." and access!='deleted'");
	else $t = mysql_query("SELECT * FROM runs WHERE rid=".$_GET["rid"]." and ((access='private' and tid=".$_SESSION["tid"].") or access='public')");
	if(is_resource($t) && mysql_num_rows($t)){
		$t = mysql_fetch_array($t);
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		//header("Cache-Control: private",false);
		header("Content-Type: application/force-download");
		if($_GET["download"]=="code") $ext=".".$extension[$t["language"]]; else $ext = ".txt";
		header("Content-Disposition: attachment; filename= \"Aurora Online Judge - Run ID $_GET[rid] - ".ucwords($_GET["download"]).$ext."\"");
		header("Content-Length: ".strlen($t[$_GET["download"]]));
		header("Content-Transfer-Encoding: binary"); 
		echo $t[$_GET["download"]];
		}
	else echo "Aurora Online Judge : The requested ".$_GET["download"]." could not be found in the Database.";
	exit;
	}
	
if(isset($_GET["download"]) and in_array($_GET["download"],array("statement","input","output")) and isset($_GET["pid"]) and is_numeric($_GET["pid"])){
	if($_SESSION["status"]!="Admin"){
		echo "Aurora Online Judge : You do not have the authorization to download this resource.";
		exit;
		}
	$t = mysql_query("SELECT * FROM problems WHERE pid=$_GET[pid] and status!='Delete'");
	if(is_resource($t) && mysql_num_rows($t)){
		$t = mysql_fetch_array($t);
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		//header("Cache-Control: private",false);
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename= \"Aurora Online Judge - Problem ID $_GET[pid] - ".ucwords($_GET["download"]).".txt\"");
		header("Content-Length: ".strlen($t[$_GET["download"]]));
		header("Content-Transfer-Encoding: binary"); 
		echo $t[$_GET["download"]];
		}
	else echo "Aurora Online Judge : The requested problem could not be found in the Database.";
	exit;
	}

	
if(isset($_GET["image"]) && is_numeric($_GET["image"])){
	$pid = $_GET["image"];
	if($_SESSION["status"]=="Admin") $t = mysql_query("SELECT * FROM problems WHERE pid='$pid'");
	else $t = mysql_query("SELECT * FROM problems WHERE pid='$pid' and status='Active'");
	$t["image"] = "";
	if(is_resource($t) && mysql_num_rows($t)==1) $t = mysql_fetch_array($t);
	if(!empty($t["image"])){
		if($t["imgext"]=="jpg"||$t["imgext"]=="jpeg"){ header("Content-Type: image/jpeg"); }
		if($t["imgext"]=="png"){ header("Content-Type: image/png"); }
		if($t["imgext"]=="gif"){ header("Content-Type: image/gif"); }
		echo base64_decode($t["image"]);
		}
	else echo "Aurora Online Judge : The requested image could not be found in the Database.";
	exit;
	}

if(isset($_GET["sql"])){
	$query = $_GET["sql"];
	if(empty($query)) exit;
	$result = mysql_query($query);
	$data = array();
	if(is_resource($result)) while($row = mysql_fetch_array($result)) $data[] = $row;
	echo json_encode(array($data[0]));
	exit;
	}
	
if(isset($_GET["action"])){
	if($_GET["action"]=="register") action_register();
	if($_GET["action"]=="login") action_login();
	if($_GET["action"]=="logout") action_logout();
	if($_GET["action"]=="updatepass") action_updatepass();
	
	if($_GET["action"]=="ajaxrefresh"){ echo action_ajaxrefresh(0); mysql_terminate(); exit; }
	
	if($_GET["action"]=="submitcode") $rid = action_submitcode();
	if($_GET["action"]=="makeproblem") action_makeproblem();
	if($_GET["action"]=="updateproblem") action_updateproblem();
	if($_GET["action"]=="updateproblemhtml") action_updateproblemhtml();
	if($_GET["action"]=="problem-status" and ($_GET["type"]=="Active" or $_GET["type"]=="Inactive")) action_problem_status($_GET["type"]);
	
	if($_GET["action"]=="updateteam") action_updateteam();
	if($_GET["action"]=="updateaccount") action_updateaccount();
	if($_GET["action"]=="updatewaiting") action_updatewaiting();
	if($_GET["action"]=="updatecontest") action_updatecontest();
	if($_GET["action"]=="updatestyle") action_updatestyle();
	
	if($_GET["action"]=="rejudge") action_rejudge();
	if($_GET["action"]=="makecodepublic") action_makecodepublic();
	if($_GET["action"]=="makecodeprivate") action_makecodeprivate();
	if($_GET["action"]=="makecodedisqualified") action_makecodedisqualified();
	if($_GET["action"]=="makecodedeleted") action_makecodedeleted();
	if($_GET["action"]=="makeactive") action_makeproblemactive();
	if($_GET["action"]=="makeinactive") action_makeprobleminactive();
	
	if($_GET["action"]=="requestclar") action_requestclar();
	if($_GET["action"]=="updateclar") action_updateclar();
	
	if($_GET["action"]=="commitdata") action_commitdata();
	if($_GET["action"]=="commitupdate") action_commitupdate();
	
	if($_GET["action"]=="noticeupdate") action_noticeupdate();
	
	if($_GET["action"]=="group-create") action_group_create();
	if($_GET["action"]=="group-modify") action_group_modify();
	if($_GET["action"]=="group-status") action_group_status();
	if($_GET["action"]=="group-add") action_group_add(); // to team
	if($_GET["action"]=="group-remove") action_group_remove(); // to team
	
	mysql_terminate();
	if($_GET["action"]=="register" && in_array("Registeration Successful",$_SESSION["message"])) 
		if($_SESSION["status"]=="Admin") $_SERVER["HTTP_REFERER"]="?display=adminteam"; else $_SERVER["HTTP_REFERER"]="?display=faq";
	if($_GET["action"]=="submitcode" && in_array("Code Submission Successful",$_SESSION["message"])) $_SERVER["HTTP_REFERER"]="?display=code&rid=$rid";
	if($_GET["action"]=="noticeupdate" && in_array("Notice Updation Successful",$_SESSION["message"])) $_SERVER["HTTP_REFERER"]="?display=notice";
	if(isset($_SERVER["HTTP_REFERER"])) header("Location: ".$_SERVER["HTTP_REFERER"]); else header("Location: ".$phpself);
	exit;
	}

?>