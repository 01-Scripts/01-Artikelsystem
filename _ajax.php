<?PHP
/* 
	01-Artikelsystem Copyright 2006-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Bearbeitung von eingehenden Ajax-Requests
	#fv.311#
*/

// Security: Only allow calls from _ajaxloader.php!
if(basename($_SERVER['SCRIPT_FILENAME']) != "_ajaxloader.php") exit;

// Artikel / Seiten löschen
if(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "delarticle" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
	
	    if($_REQUEST['static'] == 0 && $userdata['editarticle'] == 2)
		$mysqli->query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' AND static = '0' LIMIT 1");
	elseif($_REQUEST['static'] == 0 && $userdata['editarticle'] == 1)
		$mysqli->query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' AND uid = '".$userdata['id']."' AND static = '0' LIMIT 1");
	elseif($_REQUEST['static'] == 1 && $userdata['staticarticle'] == 2)
		$mysqli->query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' AND static = '1' LIMIT 1");
	elseif($flag_static == 1 && $userdata['staticarticle'] == 1)
		$mysqli->query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' AND uid = '".$userdata['id']."' AND static = '1' LIMIT 1");
			
	if($mysqli->affected_rows == 1){
		delComments($_REQUEST['id']);
		echo "<script type=\"text/javascript\"> Success_delfade('id".$_REQUEST['id']."'); </script>";
		}
	else
		echo "<script type=\"text/javascript\"> Failed_delfade(); </script>";
	}

// Kategorien löschen
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "delcat" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
	$sqlcat = "SELECT id,catpic FROM ".$mysql_tables['cats']." WHERE id = '".$mysqli->escape_string($_REQUEST['id'])."'";
	$listcat = $mysqli->query($sqlcat);
	while($rowcat = $listcat->fetch_assoc()){
		@unlink($catuploaddir.$rowcat['catpic']);
		$mysqli->query("DELETE FROM ".$mysql_tables['cats']." WHERE id = '".$rowcat['id']."'");
		}

	$newscatidlist = $mysqli->query("SELECT id,newscatid FROM ".$mysql_tables['artikel']."");
	while($row = $newscatidlist->fetch_assoc(){
		$testarray = explode(",",substr($row['newscatid'],1,strlen($row['newscatid'])-2));
		if(is_array($testarray) && count($testarray) > 1){
			unset($testarray[array_search($_REQUEST['id'],$testarray)]);

			$mysqli->query("UPDATE ".$mysql_tables['artikel']." SET newscatid=',".implode(",",$testarray).",' WHERE id='".$mysqli->escape_string($row['id'])."'");
			}
		elseif($row['newscatid'] == ",".$_REQUEST['id'].","){
			$mysqli->query("UPDATE ".$mysql_tables['artikel']." SET newscatid='0' WHERE newscatid=',".$mysqli->escape_string($_REQUEST['id']).",'");
			}
		}
	echo "<script type=\"text/javascript\"> Success_delfade('id".$_REQUEST['id']."'); </script>";
	}
else
	echo "<script type=\"text/javascript\"> Failed_delfade(); </script>";
	
?>