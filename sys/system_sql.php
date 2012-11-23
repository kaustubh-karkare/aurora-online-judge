<?php

function _md5($str){ return $str; } // no hash

function mysql_initiate(){
	global $mysql_hostname,$mysql_username,$mysql_password,$mysql_database,$admin,$ajaxlogout,$sessionid,$admin_teamname,$admin_password;
	
	$link = mysql_connect($mysql_hostname,$mysql_username,$mysql_password);
	if(!$link){ $_SESSION["message"][] = "SQL Error : Could Not Establish Connection."; return; }
	if(!mysql_select_db($mysql_database)){
		mysql_query("CREATE DATABASE ".$mysql_database);
		if(!mysql_select_db($mysql_database)){ $_SESSION["message"][] = "SQL Error : Could Not Select Database."; return; }
		}
	$data = mysql_list_tables($mysql_database); $table = array(); if(is_resource($data)) while($temp=mysql_fetch_row($data)) $table[] = $temp[0];
	if(!in_array("teams",$table)){ mysql_query("CREATE TABLE teams (tid int not null primary key auto_increment,teamname tinytext,teamname2 tinytext,pass tinytext,status tinytext,score int,penalty bigint,name1 tinytext,roll1 tinytext,branch1 tinytext,email1 tinytext,phone1 tinytext,name2 tinytext,roll2 tinytext,branch2 tinytext,email2 tinytext,phone2 tinytext,name3 tinytext,roll3 tinytext,branch3 tinytext,email3 tinytext,phone3 tinytext,platform text,ip text,session tinytext,gid int not null)"); }
	if(!in_array("problems",$table)){ mysql_query("CREATE TABLE problems (pid int not null primary key auto_increment,code tinytext,name tinytext,type tinytext,status tinytext,pgroup tinytext,statement longtext,image blob,imgext tinytext,input longtext,output longtext,timelimit int,score int,languages tinytext,options tinytext)"); }
	if(!in_array("runs",$table)){ mysql_query("CREATE TABLE runs (rid int not null primary key auto_increment,pid int,tid int,language tinytext,name tinytext,code longtext,time tinytext,result tinytext,error text,access tinytext,submittime int,output longtext)"); }
	if(!in_array("admin",$table)){ mysql_query("CREATE TABLE admin (variable tinytext,value longtext)"); }
	if(!in_array("logs",$table)){ mysql_query("CREATE TABLE logs (time int not null primary key,ip tinytext,tid int,request tinytext)"); }
	if(!in_array("clar",$table)){ mysql_query("CREATE TABLE clar (time int not null primary key,tid int,pid int,query text,reply text,access tinytext,createtime int)"); }
	if(!in_array("groups",$table)){ mysql_query("CREATE TABLE groups (gid int not null primary key auto_increment, groupname tinytext, statusx int)"); }
	
	// If empty tables
	$temp = mysql_query("SELECT * FROM teams"); if(is_resource($temp) && mysql_num_rows($temp)==0){
		mysql_query("INSERT INTO teams (teamname,pass,status,score,name1,roll1,branch1,email1,phone1) VALUES ('".($admin_teamname)."','"._md5($admin_password)."','Admin',0,'Kaustubh Karkare','','','kaustubh.karkare@gmail.com','')");
		mysql_query("INSERT INTO teams (teamname,pass,status,score,name1,roll1,branch1,email1,phone1) VALUES ('ACM','"._md5($admin_password)."','Admin',0,'ACM Team','','','','')"); ###
		}
	$temp = mysql_query("SELECT * FROM problems"); if(is_resource($temp) && mysql_num_rows($temp)==0){
		mysql_query("INSERT INTO problems (pid,code,name,type,status,pgroup,statement,input,output,timelimit,score,languages) VALUES (1,'TEST','Squares','Ad-Hoc','Active','#00 Test','".addslashes(file_get('data/example/problem.txt'))."','".addslashes(file_get('data/example/input.txt'))."','".addslashes(file_get('data/example/output.txt'))."',1,0,'Brain,C,C++,C#,Java,JavaScript,Pascal,Perl,PHP,Python,Ruby,Text')");
		}
	$temp = mysql_query("SELECT * FROM runs"); if(is_resource($temp) && mysql_num_rows($temp)==0){
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (1,1,1,'C','code','".(addslashes(file_get('data/example/code.c')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (2,1,1,'C++','code','".(addslashes(file_get('data/example/code.cpp')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (3,1,1,'C#','code','".(addslashes(file_get('data/example/code.cs')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (4,1,1,'Java','code','".(addslashes(file_get('data/example/code.java')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (5,1,1,'JavaScript','code','".(addslashes(file_get('data/example/code.js')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (6,1,1,'Pascal','code','".(addslashes(file_get('data/example/code.pas')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (7,1,1,'Perl','code','".(addslashes(file_get('data/example/code.pl')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (8,1,1,'PHP','code','".(addslashes(file_get('data/example/code.php')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (9,1,1,'Python','code','".(addslashes(file_get('data/example/code.py')))."',NULL,NULL,'public')");
		mysql_query("INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (10,1,1,'Ruby','code','".(addslashes(file_get('data/example/code.rb')))."',NULL,NULL,'public')");
		}
	$temp = mysql_query("SELECT * FROM admin"); if(is_resource($temp) && mysql_num_rows($temp)==0){
		mysql_query("INSERT INTO admin VALUES ('mode','Passive');");
		mysql_query("INSERT INTO admin VALUES ('lastjudge','0');");
		mysql_query("INSERT INTO admin VALUES ('ajaxrr','0');");
		mysql_query("INSERT INTO admin VALUES ('mode','Passive');");
		mysql_query("INSERT INTO admin VALUES ('penalty','20');");
		
		mysql_query("INSERT INTO admin VALUES ('mysublist','5');");
		mysql_query("INSERT INTO admin VALUES ('allsublist','10');");
		mysql_query("INSERT INTO admin VALUES ('ranklist','10');");
		mysql_query("INSERT INTO admin VALUES ('clarpublic','2');");
		mysql_query("INSERT INTO admin VALUES ('clarprivate','2');");
		
		mysql_query("INSERT INTO admin VALUES ('regautoauth','1');");
		mysql_query("INSERT INTO admin VALUES ('multilogin','0');");
		
		mysql_query("INSERT INTO admin VALUES ('clarpage','10');");
		mysql_query("INSERT INTO admin VALUES ('substatpage','25');");
		mysql_query("INSERT INTO admin VALUES ('probpage','25');");
		mysql_query("INSERT INTO admin VALUES ('teampage','25');");
		mysql_query("INSERT INTO admin VALUES ('rankpage','25');");
		mysql_query("INSERT INTO admin VALUES ('logpage','100');");
		mysql_query("INSERT INTO admin VALUES ('notice','Announcements\nWelcome to the Aurora Online Judge.');");
		}
	
	// Other Inits
	$data = mysql_query("SELECT * FROM admin"); if(is_resource($data)) while($temp = mysql_fetch_array($data))
		if(!in_array($temp["variable"],array("scoreboard"))) $admin[$temp["variable"]]=$temp["value"];
	if($admin["mode"]=="Active" && time()>=$admin["endtime"]){ $admin["mode"]="Disabled"; }
	if($admin["mode"]=="Lockdown" && $_SESSION["tid"]!=0 && $_SESSION["status"]!="Admin"){
		$_SESSION["message"][] = "Access Denied : You have been logged out as the contest has been locked down. Please try again again.";
		action_logout();
		$ajaxlogout=1;
		}
	if(!$admin["multilogin"] && $_SESSION["tid"] && $_SESSION["status"]!="Admin"){
		$sess = mysql_query("SELECT session FROM teams WHERE tid=".$_SESSION["tid"]);
		$sess = mysql_fetch_array($sess); $sess = $sess["session"];
		if($sess!=$sessionid){
			$_SESSION["message"][] = "Multiple Login Not Allowed.";
			action_logout();
			$ajaxlogout=1;
			}
		}
	if(1 || !isset($admin["adminwork"]) || $admin["adminwork"]<time()){ action_adminwork(); $admin["adminwork"]=time()+10; }
	
	return 0; // Success
	}

	
	
	
	
function mysql_terminate(){
	global $admin;
	//if($_SESSION["status"]=="Admin") print_r($admin);
	foreach($admin as $key=>$value){
		$temp = mysql_query("SELECT * FROM admin WHERE variable='$key'");
		if(is_resource($temp) && mysql_num_rows($temp)>0) mysql_query("UPDATE admin SET value='".addslashes($value)."' WHERE variable='".addslashes($key)."'");
		else mysql_query("INSERT INTO admin VALUES ('".addslashes($key)."','".addslashes($value)."')");
		}
	$_SESSION["time"]=time();
	mysql_close();
	}
	

function mysql_getdata($query){
	$t = mysql_query($query);
	if(!is_resource($t)) return NULL;
	$data = array();
	while($row = mysql_fetch_array($t)) $data[] = $row;
	return $data;
	}
	
?>