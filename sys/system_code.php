<?php

function display_code(){
	global $fullresult,$extension,$brush;
	echo "<center><h2>Source Code</h2></center>";
	if(!isset($_GET["rid"]) || empty($_GET["rid"]) ) $rid=0; else $rid=$_GET["rid"];
	$run = mysql_query("SELECT * FROM runs WHERE rid=$rid AND access!='deleted'");
	$error = 0;
	if(!$error)	if(!is_resource($run) || mysql_num_rows($run)==0) $error=1; else $run = mysql_fetch_array($run);
	if(!$error){
		$problem = mysql_query("SELECT * FROM problems WHERE pid=$run[pid]");
		if(!is_resource($problem) || mysql_num_rows($problem)==0) $error=2; else $problem = mysql_fetch_array($problem);
		}
	if(!$error){
		$team = mysql_query("SELECT * FROM teams WHERE tid=$run[tid]");
		if(!is_resource($team) || mysql_num_rows($team)==0) $error=3; else $team = mysql_fetch_array($team);
		}
	if($_SESSION["status"]!="Admin" && $_SESSION["tid"]!=$run["tid"] && $run["access"]!="public") $error=4;
	
	if($error){
		echo "<table width=100%><tr><th>Run ID</th><td>NA</td><th>Team Name</th><td>NA</td><th>Result</th><td>NA</td><th>File Name</th><td>NA</td><th rowspan=2>Options</th><td rowspan=2>NA</td></tr>";
		echo "<tr><th>Language</th><td>NA</td><th>Problem Name</th><td>NA</td><th>Run Time</th><td>NA</td><th>Submission Time</th><td>NA</td></tr>";
		}
	if($error==1) echo "<tr><td colspan=10 style='padding:30;'>Code you requested does not exist in the Database.</td></tr>";
	if($error==2) echo "<tr><td colspan=10 style='padding:30;'>The problem for which this code is a solution does not exist.</td></tr>";
	if($error==3) echo "<tr><td colspan=10 style='padding:30;'>The team which submitted this code does not exist.</td></tr>";
	if($error==4) echo "<tr><td colspan=10 style='padding:30;'>You are not authorized to access this code.</td></tr>";
	else if(!$error){
		$filename = $run["name"].".".$extension[$run["language"]];
		$result = $run["result"]; if(isset($fullresult[$result])) $result = $fullresult[$result];
		$code = eregi_replace("<","&lt;",$run["code"]);//$code = filter($run["code"]); $code = eregi_replace("\n","<br>",$code); $code = eregi_replace("	","    ",$code); $code = eregi_replace(" ","&nbsp;",$code);

		$options="";
		if($_SESSION["tid"] || $run["access"]=="public"){
			$options.="<input type='button' style='width:100%;' value='Edit' onClick=\"window.location='?display=problem&pid=$run[pid]&edit=$rid#bottom';\"><br>";
			$options.="<input type='button' style='width:100%;' value='Download' onClick=\"window.location='?download=code&rid=$rid';\"><br>";
			}
		if($_SESSION["status"]=="Admin"){
			$options.="<input type='button' style='width:100%;' value='Rejudge' onClick=\"window.location='?action=rejudge&rid=$run[rid]';\"><br>";
			if($run["access"]=="private") $options.=" <input type='button' style='width:100%;' value='Private' title='Make this code Public (visible to all).' onClick=\"window.location='?action=makecodepublic&rid=$rid';\"><br>";
			else $options.=" <input type='button' style='width:100%;' value='Public' title='Make this code Private (visible only to the team that submitted it).' onClick=\"window.location='?action=makecodeprivate&rid=$rid';\"><br>";
			$options.="<input type='button' style='width:100%;' value='Disqualify' onClick=\"if(confirm('Are you sure you wish to disqualify Run ID $run[rid]?')) window.location='?action=makecodedisqualified&rid=$run[rid]';\"><br>";
			$options.="<input type='button' style='width:100%;' value='Delete' onClick=\"if(confirm('Are you sure you wish to delete Run ID $run[rid]?'))window.location='?action=makecodedeleted&rid=$run[rid]';\"><br>";
			}

	
		echo "<table width=100%><tr><th width=20%>Run ID</th><th width=20%>Team Name</th><th width=20%>Problem Name</th><th width=20%>Result</th><th width=20%>Options</th>";
		echo "<tr><td>$rid</td><td><a href='?display=submissions&tid=$team[tid]'>$team[teamname]</a></td><td><a href='?display=problem&pid=$problem[pid]' title='$problem[code]'>$problem[name]</td><td>$result</td><td rowspan=3>$options</td></tr></tr>";
		echo "<tr><th>Language</th><th>File Name</th><th>Submission Time</th><th>Run Time</th></tr>";
		echo "<tr><td>".($run["language"]=="Brain"?"Brainf**k":$run["language"])."</td><td>$filename</td><td>".fdate($run["submittime"])."</td><td>$run[time]</td></tr>";

		echo "<tr><td colspan=10 style='text-align:left;'><div class='limit'><pre class='brush: ".$brush[$run["language"]]."'>$code</pre></div></td></tr>";
		if(($run["result"]!="RTE"||$_SESSION["status"]=="Admin") && !empty($run["error"])) echo "<tr><th colspan=10>Error Message</th></tr><tr><td colspan=10 style='text-align:left;padding:0;'><div class='limit'><pre class='brush:text'>".eregi_replace("<br>","\n",filter($run["error"]))."</pre></div></td></tr>";
		if(($_SESSION["status"]=="Admin" || $run["access"]=="public") and isset($_GET["io"]) and $_GET["io"]=="yes"){
			$problem['input'] = filter($problem['input']);
			$problem['output'] = filter($problem['output']);
			if($run["result"]=="AC") $run['output'] = $problem['output'];
			else $run['output'] = filter($run['output']);
			$problem['input'] = explode("<br>",$problem['input']);
			$problem['output'] = explode("<br>",$problem['output']);
			$run['output'] = explode("<br>",$run['output']);
			$index1 = $index2 = $index3 = "";
			$k = count($problem["input"]);
			for($i=0;$i<$k;++$i) $index1.=($i+1).($i==0?"<br>":"")."<br>";
			$k = count($problem["output"]);
			for($i=0;$i<$k;++$i) $index2.=($i+1).($i==0?"<br>":"")."<br>";
			$k = count($run["output"]);
			for($i=0;$i<$k;++$i) $index3.=($i+1).($i==0?"<br>":"")."<br>";
			$k = max(count($problem['output']),count($run['output']));
			$l = strlen("".$k);
			$fm = -1;
			for($i=0,$j=0;$i<$k;$i++){
				if($i>count($problem['output'])){ $problem['output'][$i]="<red>".$run['output'][$i]."<red>"; $j++; if($fm==-1) $fm=$i+1; continue; }
				if($i>count($run['output'])){ $problem['output'][$i]="<green>".$problem['output'][$i]."<green>"; $j++; if($fm==-1) $fm=$i+1; continue; }
				$p = strstr($problem["options"],"P");
				if(!$p and $problem['output'][$i]==$run['output'][$i]) continue;
				$pout = eregi_replace(" +"," ",eregi_replace(" *$","",eregi_replace("^ *","",$problem['output'][$i])));
				$rout = eregi_replace(" +"," ",eregi_replace(" *$","",eregi_replace("^ *","",$run['output'][$i])));
				if($p and $pout==$rout) continue;
				$run['output'][$i] = "<red>".($p?$rout:$run['output'][$i])."</red>";
				$problem['output'][$i] = "<green>".($p?$pout:$problem['output'][$i])."</green>";
				$j++; if($fm==-1) $fm=$i+1;
				}
			if(count($problem['input'])) $problem['input'][0].="<br>";
			if(count($problem['output'])) $problem['output'][0].="<br>";
			if(count($run['output'])) $run['output'][0].="<br>";
			$problem['input'] = str_replace(" ","&nbsp;",implode("<br>",$problem['input']));
			$problem['output'] = str_replace(" ","&nbsp;",implode("<br>",$problem['output']));
			$run['output'] = str_replace(" ","&nbsp;",implode("<br>",$run['output']));
			echo "</table><br><table class='io'><tr><th><a href='?download=input&pid=$problem[pid]'>Program Input</a></th><th><a href='?download=output&pid=$problem[pid]'>Correct Output</a></th><th><a href='?download=output&rid=$rid'>Actual Output</a></th></tr><tr>";
			echo "<td><div id='input'><table><tr><td><code><sno>$index1</sno></code></td><td><code>".$problem['input']."</code></td></tr></table></div></td>";
			echo "<td><div id='output'><table><tr><td><code><sno>$index2</sno></code></td><td><code>".$problem['output']."</code></td></tr></table></div></td>";
			echo "<td><div id='actual'><table><tr><td><code><sno>$index3</code></sno></td><td><code>".$run['output']."</code></td></tr></table></div></td>";
			echo "<tr><td><input type='button' value='Hide Input Output Files' onClick=\"window.location=window.location.search.replace(/[\?\&]io\=[^&]*/,'');\"></td>";
			echo "<td><input type='button' value='Disable Scroll Synchronization' onClick=\"if(scroll_lock) this.value='Enable'; else this.value='Disable'; this.value+=' Scroll Synchronization'; scroll_lock=!scroll_lock; \"></td><td>Matching Lines : ".($k-$j)."/".$k."<br>".($fm!=-1?"First mismatch at line $fm.":"")."</td>";
			echo "</table>";
			}
		else if(($_SESSION["status"]=="Admin" || $run["access"]=="public") and in_array($run["result"],array("AC","WA","PE","RTE"))) echo "</table><br><center><input type='button' value='Display Input Output Files' onClick=\"window.location=window.location.search.replace(/[\?\&]io\=[^&]*/,'')+'&io=yes';\"></center>";
		else echo "</table>";
		return;
		}
	echo "</table>";
	}

	
	
	
	
	
	
	
	
	
function action_submitcode(){
	global $sessionid,$admin,$maxcodesize;
	if($admin["mode"]!="Passive" && $admin["mode"]!="Active" && $_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Code Submission Error : You are not allowed to submit solutions right now!"; return; }
	
	if(!isset($_POST["code_pid"]) || empty($_POST["code_pid"])
	|| !isset($_POST["code_lang"]) || empty($_POST["code_lang"])
	|| !isset($_POST["code_name"]) || empty($_POST["code_name"]))
		{ $_SESSION["message"][] = "Code Submission Error : Insufficient Data"; return; }
		
	$data = mysql_query("SELECT status,languages,pgroup FROM problems WHERE pid='$_POST[code_pid]'");
	if(!is_resource($data) || mysql_num_rows($data)==0){ $_SESSION["message"][] = "Code Submission Error : The specified problem does not exist."; return; }
	$data = mysql_fetch_array($data);
	if($_SESSION["status"]!="Admin" && $data["status"]!="Active"){ $_SESSION["message"][] = "Code Submission Error : The problem specified is not currently active."; return; }
	if($_SESSION["status"]!="Admin" && eregi("^#[0-9]+ CQM\-[0-9]+$",$data["prgoup"]) && $admin["mode"]=="Passive"){ $_SESSION["message"][] = "Code Submission Error : You can no longer submit solutions to this problem."; return; }
	if($_SESSION["status"]!="Admin" && !in_array($_POST["code_lang"],explode(",",$data["languages"]))){ $_SESSION["message"][] = "Code Submission Error : The programming language specified is not allowed for this problem."; return; }
		
	// Lower Priority - Text
	if(strlen($_POST["code_text"])>$maxcodesize){ $_SESSION["message"][] = "Code Submission Error : Submitted code exceeds size limits."; return; }
	$sourcecode = addslashes($_POST["code_text"]);
	// Higher Priority - File
	$ext = file_upload("code_file","sys/temp/".$sessionid."_code","text/plain,text/x-c,text/x-c++src,application/octet-stream,application/x-javascript,application/x-ruby",100*1024);
	if($ext!=-1){
		$sourcecode = addslashes(file_get("sys/temp/".$sessionid."_code.$ext")); unlink("sys/temp/".$sessionid."_code.$ext");
		$_POST["code_name"] = eregi_replace(".(b|c|cpp|java|pl|php|py|rb|txt)$","",basename($_FILES['code_file']['name']));
		}
	//echo "INSERT INTO runs (pid,tid,language,name,code,access,submittime) VALUES ('$_POST[code_pid]','$_SESSION[tid]','$_POST[code_lang]','$_POST[code_name]','$sourcecode','private',".time().")";
	if(!empty($sourcecode)){
		mysql_query("INSERT INTO runs (pid,tid,language,name,code,access,submittime) VALUES ('$_POST[code_pid]','$_SESSION[tid]','$_POST[code_lang]','$_POST[code_name]','$sourcecode','private',".time().")");
		$rid = mysql_insert_id();
		$_SESSION["message"][] = "Code Submission Successful.\nIf the code you submitted is not being evaluated immediately, please contact \"SourceCode\" on DC.";
		$_SESSION["redirect"] = "?display=code&rid=$rid";
		}
	else $_SESSION["message"][] = "Code Submission Error : Cannot submit empty code.";
	return mysql_insert_id();
	}
	
	
	
	
	
function action_rejudge(){
	global $extension,$fullresult;
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Access Denied : You need to be an Administrator to perform this action."; return; }
	$condition = "";
	if(isset($_GET["rid"]) && !empty($_GET["rid"]) && is_numeric($_GET["rid"])) $condition.=" AND rid=$_GET[rid] ";
	if(isset($_GET["tid"]) && !empty($_GET["tid"]) && is_numeric($_GET["tid"])) $condition.=" AND tid=$_GET[tid] ";
	if(isset($_GET["pid"]) && !empty($_GET["pid"]) && is_numeric($_GET["pid"])) $condition.=" AND pid=$_GET[pid] ";
	if(isset($_GET["lan"]) && !empty($_GET["lan"]) && key_exists($_GET["lan"],$extension)) $condition.=" AND language='$_GET[lan]' ";
	if(isset($_GET["res"]) && !empty($_GET["res"]) && key_exists($_GET["res"],$fullresult)) $condition.=" AND result='$_GET[res]' ";
	if((!isset($_GET["all"]) || $_GET["all"]!=1) && $condition==""){ $_SESSION["message"][] = "Run Data Updation Error : Insufficient Data"; return; }
	mysql_query("UPDATE runs SET time=NULL,result=NULL WHERE access!='deleted' $condition");
	}
	
function action_makecodepublic(){
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Access Denied : You need to be an Administrator to perform this action."; return; }
	if(isset($_GET["rid"]) && !empty($_GET["rid"]) && is_numeric($_GET["rid"])) mysql_query("UPDATE runs SET access='public' WHERE rid=".$_GET["rid"]);
	else { $_SESSION["message"][] = "Run Data Updation Error : Insufficient Data"; return; }
	}
	
function action_makecodeprivate(){
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Access Denied : You need to be an Administrator to perform this action."; return; }
	if(isset($_GET["rid"]) && !empty($_GET["rid"]) && is_numeric($_GET["rid"])) mysql_query("UPDATE runs SET access='private' WHERE rid=".$_GET["rid"]);
	else { $_SESSION["message"][] = "Run Data Updation Error : Insufficient Data"; return; }
	}

function action_makecodedisqualified(){
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Access Denied : You need to be an Administrator to perform this action."; return; }
	if(isset($_GET["rid"]) && !empty($_GET["rid"]) && is_numeric($_GET["rid"])){ mysql_query("UPDATE runs SET result='DQ' WHERE rid=".$_GET["rid"]); $_SESSION["message"][] = "Code Disqualification Successful."; }
	else { $_SESSION["message"][] = "Run Data Updation Error : Insufficient Data"; return; }
	}
	
function action_makecodedeleted(){
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Access Denied : You need to be an Administrator to perform this action."; return; }
	if(isset($_GET["rid"]) && !empty($_GET["rid"]) && is_numeric($_GET["rid"])){ mysql_query("UPDATE runs SET access='deleted' WHERE rid=".$_GET["rid"]); $_SESSION["message"][] = "Code Deletion Successful."; }
	else { $_SESSION["message"][] = "Run Data Updation Error : Insufficient Data"; return; }
	}
	
	
?>