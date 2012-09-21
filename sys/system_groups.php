<?php

// group status : 0 = normal, 1 = restricted, 2 = suspended, 3 = deleted

function action_group_create(){
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "You need to be an Administrator to perform this action."; return; }
	if(!isset($_POST["groupname"]) or empty($_POST["groupname"])){ $_SESSION["message"][] = "Group Error : Missing/empty Group Name."; return; }
	if(eregi("[^A-Za-z0-9\.\_\-]",$_POST["groupname"])){ $_SESSION["message"][] = "Group Error : Invalid characters in Group Name."; return; }
	$data = mysql_query("SELECT * FROM groups WHERE statusx<3 AND groupname='".mysql_real_escape_string($_POST["groupname"])."';");
	if(!is_resource($data) or mysql_num_rows($data)>0){ $_SESSION["message"][] = "Group Error : This Group Name has already been taken."; return; }
	mysql_query("INSERT INTO groups (groupname,statusx) VALUES ('".mysql_real_escape_string($_POST["groupname"])."',0);");
	$_SESSION["message"][] = "Group created successfully.";
	}
function action_group_modify(){
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "You need to be an Administrator to perform this action."; return; }
	if(!isset($_GET["gid"]) or !is_numeric($_GET["gid"])){ $_SESSION["message"][] = "Group Error : Missing/invalid Group ID."; return; }
	if(!isset($_GET["groupname"]) or empty($_GET["groupname"])){ $_SESSION["message"][] = "Group Error : Missing/empty Group Name."; return; }
	if(eregi("[^A-Za-z0-9\.\_\-]",$_GET["groupname"])){ $_SESSION["message"][] = "Group Error : Invalid characters in Group Name."; return; }
	$data = mysql_query("SELECT * FROM groups WHERE statusx<3 AND groupname='".mysql_real_escape_string($_POST["groupname"])."';");
	if(!is_resource($data) or mysql_num_rows($data)>0){ $_SESSION["message"][] = "Group Error : This Group Name has already been taken."; return; }
	mysql_query("UPDATE groups SET groupname='$_GET[groupname] WHERE gid=$_GET[gid];");
	$_SESSION["message"][] = "Group created successfully.";
	}
function action_group_status(){
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "You need to be an Administrator to perform this action."; return; }
	if(!isset($_GET["gid"]) or !is_numeric($_GET["gid"])){ $_SESSION["message"][] = "Group Error : Missing/invalid Group ID."; return; }
	if(!isset($_GET["status"]) or !is_numeric($_GET["status"]) or $_GET["status"]<0 or $_GET["status"]>3){ $_SESSION["message"][] = "Group Error : Missing/invalid Group Status."; return; }
	$data = mysql_query("SELECT * FROM groups WHERE statusx<3 AND gid=$_GET[gid];");
	if(!is_resource($data) or mysql_num_rows($data)==0){ $_SESSION["message"][] = "Group Error : Could not select Group."; return; }
	mysql_query("UPDATE groups SET statusx=$_GET[status] WHERE gid=$_GET[gid];");
	// if($_GET["status"]=="3") mysql_query("UPDATE teams SET gid=0 WHERE gid=$_GET[gid];"); // delete
	$_SESSION["message"][] = "Group Status updated successfully.";
	}
function action_group_add(){}
function action_group_remove(){}

function display_admingroup(){
	if($_SESSION["status"]!="Admin"){
		global $currentmessage;
		$_SESSION["message"] = $currentmessage; $_SESSION["message"][] = "Access Denied : You need to be an Administrator to access that page.";
		echo "<script>window.location='?display=faq';</script>";
		return;
		}
	echo "<center><h2>Group Settings</h2>";
	echo "<form action='?action=group-create' method='post'><table><tr><th>New Group Name</th><td><input type='text' name='groupname'></td><td><input type='submit' value='Create New Group'></td></tr></table></form><br>";
	$data = mysql_query("SELECT * FROM groups WHERE statusx<3;");
	if(is_resource($data)){
		echo "<script>function group_status(gid,action){ document.location='?action=group-status&gid='+gid+'&status='+action; }</script>";
		echo "<table><tr><th>Group ID</th><th>Group Name</th><th>Status</th><th>Options</th></tr>";
		$gids = array(); $status = array();
		while($row = mysql_fetch_array($data)){
			$gids[] = $row["gid"]; $status[] = $row["statusx"];
			echo "<tr><td>$row[gid]</td><td>$row[groupname]</td><td><select name='group-status-$row[gid]' onChange='if(confirm(\"Are you sure you wish to perform this operation?\")) group_status($row[gid],this.value); else this.value=$row[statusx];'><option value=0>Normal</option><option value=1>Restricted</option><option value=2>Suspended</option><option value=3>Delete</option></select></td><td><input type='button' onClick=\"gname = prompt('Enter New Group Name (only alphanumeric, underscore, dot and dash characters allowed):'); if(gname) document.location='?action=group-modify&gid=$row[gid]&groupname='+gname; \" value='Rename'></td></tr>";
			}
		echo "</table>";
		echo "<script>gids = [".implode($gids,",")."]; status = [".implode($status,",")."]; for(var i=0;i<gids.length;++i) $('select[name=\"group-status-'+gids[i]+'\"]').val(status[i]);</script>";
		}
	}

?>