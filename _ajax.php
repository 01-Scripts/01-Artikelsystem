<?PHP
/* 
	01-Artikelsystem Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Bearbeitung von eingehenden Ajax-Requests
	#fv.3010#
*/

if(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "delarticle" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
    if($_REQUEST['static'] == 0 && $userdata['editarticle'] == 2){
			mysql_query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($_REQUEST['id'])."' AND static = '0' LIMIT 1");
			delComments($_REQUEST['id']);
			}
		elseif($_REQUEST['static'] == 0 && $userdata['editarticle'] == 1){
			mysql_query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($_REQUEST['id'])."' AND uid = '".$userdata['id']."' AND static = '0' LIMIT 1");
			delComments($_REQUEST['id']);
			}
		elseif($_REQUEST['static'] == 1 && $userdata['staticarticle'] == 1){
			mysql_query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($_REQUEST['id'])."' AND static = '1' LIMIT 1");
			delComments($_REQUEST['id']);
			}
	
	echo "<script type=\"text/javascript\"> Success_delfade('id".$_REQUEST['id']."'); </script>";
	}
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "delcat" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
	$sqlcat = "SELECT id,catpic FROM ".$mysql_tables['cats']." WHERE id = '".mysql_real_escape_string($_REQUEST['id'])."'";
	$listcat = mysql_query($sqlcat);
	while($rowcat = mysql_fetch_array($listcat)){
		@unlink($catuploaddir.$rowcat['catpic']);
		mysql_query("DELETE FROM ".$mysql_tables['cats']." WHERE id = '".$rowcat['id']."'");
		}

	$newscatidlist = mysql_query("SELECT id,newscatid FROM ".$mysql_tables['artikel']."");
	while($row = mysql_fetch_array($newscatidlist)){
		$testarray = explode(",",substr($row['newscatid'],1,strlen($row['newscatid'])-2));
		if(is_array($testarray) && count($testarray) > 1){
			unset($testarray[array_search($_REQUEST['id'],$testarray)]);

			mysql_query("UPDATE ".$mysql_tables['artikel']." SET newscatid=',".implode(",",$testarray).",' WHERE id='".mysql_real_escape_string($row['id'])."'");
			}
		elseif($row['newscatid'] == ",".$_REQUEST['id'].","){
			mysql_query("UPDATE ".$mysql_tables['artikel']." SET newscatid='0' WHERE newscatid=',".mysql_real_escape_string($_REQUEST['id']).",'");
			}
		}
	echo "<script type=\"text/javascript\"> Success_delfade('id".$_REQUEST['id']."'); </script>";
	}
else
	echo "<script type=\"text/javascript\"> Failed_delfade(); </script>";
	
	
// 01-Artikelsystem Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
?>