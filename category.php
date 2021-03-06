<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Artikel: �bersicht, Bearbeiten, Erstellen
	#fv.320#
*/

// Berechtigungsabfragen
if($userdata['editcats'] == 1){

echo "<h1>Kategorien verwalten</h1>";

	
//NEUE NEWSKATEGORIE (mit Upload)
if(isset($_POST['do']) && $_POST['do'] == "newnewscat" && isset($_POST['catname']) && !empty($_POST['catname']) && isset($_FILES['catfile']['name']) && $_FILES['catfile']['name'] != ""){
	// Neue Kategorie (mit Upload)
	$endung = getEndung($_FILES['catfile']['name']);
	if(in_array($endung,$picendungen)) $passpic = TRUE;
	else $passpic = FALSE;

	$time = time();
	if($passpic && $_FILES['catfile']['size'] <= $picsize){
		if(move_uploaded_file($_FILES['catfile']['tmp_name'], $catuploaddir.$time.".".$endung)){
			$sql_insert = "INSERT INTO ".$mysql_tables['cats']." (name,catpic)
						   VALUES ('".$mysqli->escape_string($_POST['catname'])."',
								   '".$time.".".$endung."')";
			$mysqli->query($sql_insert) OR die($mysqli->error);

			echo "<p class=\"meldung_erfolg\">Kategorie wurde erfolgreich angelegt.</p>";
			}
		else{
			echo "<p class=\"meldung_error\">
				  Upload fehlgeschlagen.<br /><br />
				  <a href=\"javascript:history.back()\">Zur&uuml;ck</a>
				  </p>";
			}
		}
	else{
		//Wenn Endung nicht unterst�tzt wird:
		echo "<p class=\"meldung_error\">
			  Dateiendung wird nicht unterst&uuml;tzt oder die gew&auml;hlte Datei ist zu gro&szlig;.<br />
			  Es sind folgende Endungen erlaubt: ".$settings['pic_end']."<br /><br />
			  <a href=\"javascript:history.back()\">Zur&uuml;ck</a>
			  </p>";
		}
	}
elseif(isset($_POST['do']) && $_POST['do'] == "newnewscat" && isset($_POST['catname']) && !empty($_POST['catname']) && isset($_FILES['catfile']['name']) && $_FILES['catfile']['name'] == ""){
	// Neue Kategorie (ohne Upload)
	$sql_insert = "INSERT INTO ".$mysql_tables['cats']." (name,catpic)
				   VALUES ('".$mysqli->escape_string($_POST['catname'])."',
						   '')";
	$mysqli->query($sql_insert) OR die($mysqli->error);

	echo "<p class=\"meldung_erfolg\">Kategorie wurde erfolgreich angelegt.</p>";
	}
	
	
	
	
	
// EDIT DURCHF�HREN:
if(isset($_REQUEST['do']) && $_REQUEST['do'] == "editcat" && isset($_POST['catname']) && !empty($_POST['catname']) && isset($_POST['id']) && !empty($_POST['id']) && is_numeric($_POST['id'])){
	if(isset($_FILES['catfile']['name']) && $_FILES['catfile']['name'] != "" && !isset($_POST['changepic']) OR isset($_FILES['catfile']['name']) && $_FILES['catfile']['name'] != "" && isset($_POST['changepic']) && $_POST['changepic'] == 1){
		
		$endung = getEndung($_FILES['catfile']['name']);
		if(in_array($endung,$picendungen)) $passpic = TRUE;
		else $passpic = FALSE;

		$list = $mysqli->query("SELECT catpic FROM ".$mysql_tables['cats']." WHERE id = '".$mysqli->escape_string($_POST['id'])."' LIMIT 1");
		while($row = $list->fetch_assoc()){
			if(isset($_POST['changepic']) && $_POST['changepic'] == 1 && $row['catpic'] != "" && $passpic)
				@unlink($catuploaddir.$row['catpic']);
			}

		$time = time();
		if($passpic && $_FILES['catfile']['size'] <= $picsize){
			if(move_uploaded_file($_FILES['catfile']['tmp_name'],$catuploaddir.$time.".".$endung)){
				$mysqli->query("UPDATE ".$mysql_tables['cats']." SET name='".$mysqli->escape_string($_POST['catname'])."', catpic='".$time.".".$endung."' WHERE id='".$mysqli->escape_string($_POST['id'])."' LIMIT 1");

				echo "<p class=\"meldung_erfolg\">Kategorie wurde bearbeitet</p>";
				}
			else{
				echo "<p class=\"meldung_error\">
					  Upload fehlgeschlagen.<br /><br />
					  <a href=\"javascript:history.back()\">Zur&uuml;ck</a>
					  </p>";
				}
			}
		else{
			//Wenn Endung nicht unterst�tzt wird:
			echo "<p class=\"meldung_error\">
				  Dateiendung wird nicht unterst&uuml;tzt oder die gew&auml;hlte Datei ist zu gro&szlig;.<br />Es sind folgende Endungen erlaubt: ".$settings['pic_end']."<br /><br />
				  <a href=\"javascript:history.back()\">Zur&uuml;ck</a>
				  </p>";
			}
		}
	else{
		//Wenn am Bild nichts ge�ndert werden soll:
		$mysqli->query("UPDATE ".$mysql_tables['cats']." SET name='".$mysqli->escape_string($_POST['catname'])."' WHERE id='".$mysqli->escape_string($_POST['id'])."' LIMIT 1");
		echo "<p class=\"meldung_erfolg\">Kategorie wurde bearbeitet</p>";
		}
	}	
	
	
	
	
	
	
//Editformular anzeigen
if(isset($_GET['do']) && $_GET['do'] == "editcatform" && isset($_GET['id']) && !empty($_GET['id'])){
    
	//Catpic l�schen
    if(isset($_GET['do2']) && $_GET['do2'] == "delpic" && isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['pic']) && !empty($_GET['pic'])){
        
        $list = $mysqli->query("SELECT catpic FROM ".$mysql_tables['cats']." WHERE id = '".$mysqli->escape_string($_GET['id'])."' LIMIT 1");
		while($row = $list->fetch_assoc()){
			if($row['catpic'] != "")
				@unlink($catuploaddir.$row['catpic']);
			}
		$mysqli->query("UPDATE ".$mysql_tables['cats']." SET catpic='' WHERE id='".$mysqli->escape_string($_GET['id'])."' LIMIT 1");
        $do2deldone = 1;
        }

    $sql = "SELECT * FROM ".$mysql_tables['cats']." WHERE id='".$mysqli->escape_string($_GET['id'])."' LIMIT 1";
    $list = $mysqli->query($sql);
    while($row = $list->fetch_assoc()){
?>
<h2>Kategorie bearbeiten</h2>

<form enctype="multipart/form-data" action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td colspan="2" class="tra"><input type="text" name="catname" value="<?PHP echo stripslashes($row['name']); ?>" size="35" /></td>
    </tr>

    <tr>
        <td align="center" width="50%" class="trb" valign="middle">
            <?PHP
                if($row['catpic'] != ""){
                    $catpicinfo = getimagesize($catuploaddir.$row['catpic']);
                    $picwidth= $catpicinfo[0];
                    $picheight= $catpicinfo[1];
                    picbig(ACP_TB_WIDTH,$picwidth,$picheight);
                    echo "<a href=\"".$catuploaddir.$row['catpic']."\" target=\"_blank\"><img src=\"".$catuploaddir.$row['catpic']."\" border=\"0\" alt=\"Kategoriebild: ".stripslashes($row['name'])."\" width=\"".$picwidth."\" height=\"".$picheight."\" /></a>";
                    echo "<br /><br /><a href=\"".$filename."&amp;do=editcatform&amp;do2=delpic&amp;id=".$_GET['id']."&amp;pic=".$row['catpic']."\"><img src=\"images/icons/icon_delete.gif\" alt=\"Rotes Kreuz\" title=\"Kategoriebild l&ouml;schen\" /> Bild l&ouml;schen</a>";
                    }
                elseif(isset($do2deldone) && $do2deldone == 1)
                    echo "<b>Bild wurde erfolgreich gel&ouml;scht</b>";
                else
                    echo "&nbsp;";
            ?>
        </td>
        <td align="center" class="trb" valign="middle">
            <?PHP
            if($row['catpic'] != ""){
            ?>
            <b>Vorhandenes Bild ersetzen?</b><br />
            <input type="checkbox" name="changepic" value="1" /><br />
            <br />
            <?PHP
            }else{
            ?>
            <b>Neues Bild hochladen:</b><br /><br />
            <?PHP
            }
            ?>
            <input type="file" name="catfile" />
        </td>
    </tr>

    <tr>
        <td colspan="2" class="tra" align="center">
            <input type="hidden" name="id" value="<?PHP echo $row['id']; ?>" />
			<input type="hidden" name="do" value="editcat" />
            <input type="submit" value="Aktualisieren" class="input" />
        </td>
    </tr>
</table>
</form>
<?PHP
        }
    }
//Neue Kategorie anlegen
else{
?>

<h2>Neue Kategorie anlegen</h2>

<form enctype="multipart/form-data" action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td class="tra" style="width: 30%;"><b>Kategoriename</b></td>
        <td class="tra" style="width: 40%;"><b>Kategoriebild</b></td>
        <td class="tra" style="width: 20%;">&nbsp;</td>
    </tr>

    <tr>
        <td class="trb"><input type="text" name="catname" size="30" /></td>
        <td class="trb"><input type="file" name="catfile" /></td>
        <td class="trb" align="right"><input type="submit" value="Anlegen &raquo;" class="input" /><input type="hidden" name="do" value="newnewscat" /></td>
    </tr>
</table>
</form>
<?PHP
    }//Ende: if-Abfrage: Neue Kategorie anlegen-Formular	
	
	
	
if(isset($_POST['sort']) && !empty($_POST['sort'])){
	$list = $mysqli->query("SELECT * FROM ".$mysql_tables['cats']."");
	while($row = $list->fetch_assoc()){
		$mysqli->query("UPDATE ".$mysql_tables['cats']." SET sortid='".$mysqli->escape_string($_POST['cat_'.$row['id']])."' WHERE id='".$row['id']."' LIMIT 1");
		}
	}
?>

<h2>Kategorien</h2>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
		<td class="tra" align="center" style="width: 80px;"><b>Reihenfolge</b></td>
		<td class="tra" align="center" style="width: 25px;"><b>ID</b></td>
        <td class="tra" align="center" style="width: 100px;"><b>Bild</b></td>
        <td class="tra"><b>Kategoriename</b></td>
        <td class="tra" style="width: 25px;"><!--Bearbeiten--></td>
        <td class="tra" align="center" style="width: 25px;"><!--L�schen--><img src="images/icons/icon_trash.gif" alt="M&uuml;lleimer" title="Kategorie l&ouml;schen" /></td>
    </tr>

<?PHP
$count = 0;
$list = $mysqli->query("SELECT * FROM ".$mysql_tables['cats']." ORDER BY sortid,name");
while($row = $list->fetch_assoc()){
    if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }

    //Bild verkleinern
    if($row['catpic'] != ""){
        $catpicinfo = getimagesize($catuploaddir.$row['catpic']);
        $picwidth= $catpicinfo[0];
        $picheight= $catpicinfo[1];
        picbig(ACP_TB_WIDTH,$picwidth,$picheight);
        $catpic = "<a href=\"".$catuploaddir.$row['catpic']."\" target=\"_blank\"><img src=\"".$catuploaddir.$row['catpic']."\" alt=\"Kategoriebild: ".stripslashes($row['name'])."\" width=\"".$picwidth."\" height=\"".$picheight."\" /></a>";
        }
    else
        $catpic = "&nbsp;";

    echo "<tr id=\"id".$row['id']."\">
              <td align=\"center\" class=\"".$class."\"><select name=\"cat_".$row['id']."\" size=\"1\" class=\"input_select\">"._01article_CatSortDropDown($row['sortid'])."</select></td>
			  <td align=\"center\" class=\"".$class."\">".$row['id']."</td>
              <td align=\"center\" class=\"".$class."\">".$catpic."</td>
              <td align=\"left\" class=\"".$class."\">".stripslashes($row['name'])."</td>
              <td align=\"center\" class=\"".$class."\"><a href=\"".$filename."&amp;do=editcatform&amp;id=".$row['id']."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Stift\" title=\"Kategorie bearbeiten\" /></a></td>
			  <td class=\"".$class."\" align=\"center\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"Eintrag l&ouml;schen\" class=\"fx_opener\" style=\"border:0; float:left;\" align=\"left\" /><div class=\"fx_content tr_red\" style=\"width:60px; display:none;\"><a href=\"#foo\" onclick=\"AjaxRequest.send('modul=".$modul."&ajaxaction=delcat&id=".$row['id']."');\">Ja</a> - <a href=\"#foo\">Nein</a></div></td>
          </tr>";
    }

echo "<tr>
	<td align=\"center\"><input type=\"submit\" name=\"sort\" value=\"Sortieren\" class=\"input\" /></td>
	<td colspan=\"4\">&nbsp;</td>
</tr>";
?>
</table>
</form>
<br />

<?PHP
}else $flag_loginerror = true;

?>