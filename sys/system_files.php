<?php

function file_get($target,$default=-1){
	if(!file_exists($target)) return $default;
	$f = fopen($target,"r");
	$data="";
	while(!feof($f))$data.=fgets($f);
	return $data;
	}
	
function file_set($target,$data){
	$f = fopen($target,"w");
	fputs($f,$data);
	return 0;
	}

	
function folder_get($target,$type=0){
	if(!is_dir($target)) return -1;
	$f=opendir($target);
	$list=array();
	while($e=readdir($f)){
		if($e=="."||$e=="..") continue;
		if( ($type==0 || $type==2) && file_exists("$target/$e")) $list[]=$e;
		if( ($type==1 || $type==2) && is_dir("$target/$e")) $list[]=$e;
		}
	closedir($f);
	return $list;
	}
	
function file_upload($file,$targetid,$allowedtypes,$allowedsize){
	$fileempty = empty($_FILES[$file]);
	$fileerror = $_FILES[$file]['error'];
	$filename = basename($_FILES[$file]['name']);
	$filetype = $_FILES[$file]["type"];
	$filesize = $_FILES[$file]["size"];
	$extension = substr($filename, strrpos($filename, '.') + 1);
	$filetmpname = $_FILES[$file]['tmp_name'];
	if(strtolower($extension)=="jpeg") $extension="jpg";
	if( (!$fileempty) && ($fileerror == 0) )
		if( in_array($filetype,explode(",",$allowedtypes)) )
			if($filesize<$allowedsize){
				if(file_exists("$targetid.$extension")) unlink("$targetid.$extension");
				if(move_uploaded_file($filetmpname,"$targetid.$extension")) return $extension;
				else $str = "File Upload Error : Could not move file from temporary location!";
				}
			else $str = "File Upload Error : Filesize exceeds limits!";
		else $str = "File Upload Error : Filetype not allowed! $filetype";
	//else $str = "File Upload Error : File-Data Empty / File-Error Non-Zero!";
	if($fileerror==2) $str = "File Upload Error : Filesize exceeds limits!";
	if(isset($str)) $_SESSION["message"][] = $str;
	return -1;
	}
	
function filter($str){
	$str=eregi_replace("\\\\","&#92;",$str);
	$str=stripslashes($str);
	$str=eregi_replace('"', "&#34;",$str);
	$str=eregi_replace("'", "&#39;",$str);
	$str=eregi_replace("<", "&#60;",$str);
	$str=eregi_replace(">", "&#62;",$str);
	$str=eregi_replace("/", "&#47;",$str);
	$str=eregi_replace("\r","" ,$str);
	$str=eregi_replace("\n","<br>" ,$str);
	return $str;
	}
	
function unfilter($str){
	$str=eregi_replace("&#92;","\\",$str);
	$str=stripslashes($str);
	$str=eregi_replace("&#34;",'"',$str);
	$str=eregi_replace("&#39;","'",$str);
	$str=eregi_replace("&#60;","<",$str);
	$str=eregi_replace("&#62;",">",$str);
	$str=eregi_replace("&#47;","/",$str);
	$str=eregi_replace("<br>","\n" ,$str);
	return $str;
	}
  
function listout($array){
	if(!is_array($array)) $array = array($array);
	foreach($array as $i=>$j){
		$str.="$i=>";
		if(!is_array($j)) $str.=$j;
		else { $t = array(); foreach($j as $k) $t[]=$k; $str.=implode($t,","); }
		$str.="<br>";
		}
	return $str;
	}
  
function display_filesize($bytes){
	if($bytes<1024) return $bytes." B"; $bytes=ceil($bytes/1024);
	if($bytes<1024) return $bytes." KB"; $bytes=ceil($bytes/1024);
	if($bytes<1024) return $bytes." MB"; $bytes=ceil($bytes/1024);
	return $bytes." GB";
	}
  
function fdate($t=-1){
	if($t==-1) $t=time();
	return date("d F Y, l, H:i:s",(int)$t);
	}
  
function paginate($pageurl,$total,$perpage){
	$totalpages = max(1,ceil($total/$perpage));
	//echo "[".$total.":".$perpage."]<br>";
	$pageurl = eregi_replace("\&page=[^\&]*","",eregi_replace("\?page=[^\&]*","",$pageurl));
	if(isset($_GET["page"]) && is_numeric($_GET["page"])) $page = min(max(1,$_GET["page"]),$totalpages); else $page = 1;
	$prev = min(max(1,$page-1),$totalpages); $next = min(max(1,$page+1),$totalpages);
	$pagenav = ($page==1?"First Page":"<a href='?$pageurl&page=1'>First Page</a>")." | ";
	$pagenav.= ($prev==$page?"Previous Page":"<a href='?$pageurl&page=$prev'>Previous Page</a>")." | ";
	$pagenav.= "$page/$totalpages | ";
	$pagenav.= ($next==$page?"Next Page":"<a href='?$pageurl&page=$next'>Next Page</a>")." | ";
	$pagenav.= ($prev==$totalpages?"Last Page":"<a href='?$pageurl&page=$totalpages'>Last Page</a>");
	return array($page,$pagenav);
	}

  
?>