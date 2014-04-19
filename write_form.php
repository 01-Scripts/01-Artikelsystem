<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Artikel-Formular
	#fv.320#
*/

// Art2Gal-Support?
if($art2galsupport && $userdata['dateimanager'] == 2)
    echo loadTinyMCE("advanced","","","","top","","filemanager_gal2art,");
else
    echo loadTinyMCE("advanced","","","","top");
?>

<h1><?PHP echo $input_field['site_titel']; ?></h1>

<?php
if(isset($_REQUEST['copyid']) && is_numeric($_REQUEST['copyid']) && $_REQUEST['copyid'] > 0){
?>
<p class="meldung_hinweis"><b>Sie haben diesen Artikel zum Kopieren ausgew&auml;hlt.</b><br />
Beim Abspeichern wird ein neuer Artikel mit dem nachfolgenden Inhalt angelegt -
der bestehende Artikel wird dabei nicht ver&auml;ndert.</p>
<?php
	}
?>

<form action="<?PHP echo $filename.$add_filename; ?>" method="post" name="post">
<?PHP if(isset($rowf['hide']) && $rowf['hide'] == 1){ echo "<p align=\"center\"><input type=\"submit\" class=\"input\" name=\"submit\" value=\"".$input_field['publish']."\" /></p>"; } ?>
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td class="tra" width="190"><b>Datum &amp; Uhrzeit (Start)</b></td>
        <td class="tra" valign="bottom"><input type="text" name="starttime_date" value="<?PHP echo $form_data['starttime_date']; ?>" class="DatePicker" size="10" /> - <input type="text" name="starttime_uhr" value="<?PHP echo $form_data['starttime_uhr']; ?>" size="5" /> Uhr</td>
    </tr>
	
    <tr>
        <td class="tra"><b>Datum &amp; Uhrzeit (Ende)</b></td>
        <td class="tra" valign="bottom"><input type="text" name="endtime_date" value="<?PHP echo $form_data['endtime_date']; ?>" class="DatePicker" size="10" /> - <input type="text" name="endtime_uhr" value="<?PHP echo $form_data['endtime_uhr']; ?>" size="5" /> Uhr</td>
    </tr>
    
<?PHP
$cq = $mysqli->query("SELECT * FROM ".$mysql_tables['cats']." ORDER BY sortid,name");
if($cq->num_rows > 0 && $input_section == "article"){
$hiddencat = "";
?>
    <tr>
        <td class="tra"><b>Kategorie:</b></td>
        <td class="tra">
            <?PHP
			echo "<select name=\"newscat[]\" size=\"3\" multiple=\"multiple\" class=\"input_select\">";
			echo "<option value=\"0\">Kategorie w&auml;hlen...</option>";
			if(isset($form_data['newscat']) && $form_data['newscat'] != "0") $newscatids_array = explode(",",$form_data['newscat']);
			
			while($rowcat = $cq->fetch_assoc()){
				echo "<option value=\"".$rowcat['id']."\"";
				
				if(isset($form_data['newscat']) && $form_data['newscat'] != "0" && in_array($rowcat['id'],$newscatids_array)) echo " selected=\"selected\"";
				
				echo ">".stripslashes($rowcat['name'])."</option>";
				}
			echo "</select>";
            ?>
        </td>
    </tr>
<?PHP
    }
else
    $hiddencat = "<input type=\"hidden\" name=\"newscat\" value=\"0\" />";
?>

    <tr>
        <td class="tra" colspan="2">
			<h2 style="margin:0; float:left;"><?PHP echo $input_field['bezeichnung']; ?>-&Uuml;berschrift</h2>
			<input type="text" name="titel" value="<?PHP echo $form_data['titel']; ?>" style="font-size:14pt; margin-left:18px;" size="49" />
		</td>
    </tr>
	
    <tr>
        <td class="tra" colspan="2">
			<input type="hidden" id="modulid" value="<?php echo $modul; ?>" /><!-- Steuert im Filemanager-Modul des TinyMCE welches Modul den Aufruf getätigt hat (um ein modulspezifisches Popup zu öffnen) -->
            <a name="textarea"></a>
            <textarea name="textfeld" rows="25" cols="100" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;"><?PHP echo $form_data['textfeld']; ?></textarea>
        </td>
    </tr>

<?PHP
if($settings['artikeleinleitung'] >= 1 && $settings['artikeleinleitungslaenge'] >= 1 && $flag_static == 0){
?>
    <tr>
        <td class="tra" colspan="2" align="left" style="padding-left:18px;">
            <input type="checkbox" name="autozusammen" value="1"<?PHP if($form_data['autozusammen'] == 1){ echo " checked=\"checked\""; } ?> onclick="hide_unhide_tr('zeditor');" /> <b>Einleitungstext automatisch generieren</b><br />
            oder eigenen Text eingeben:
        </td>
    </tr>

    <tr id="zeditor" style="display:table-row;">
        <td class="tra" colspan="2">
            <textarea name="zusammenfassung" id="zusammenfassung" rows="10" cols="100" onkeyup="checkLen('zusammenfassung');" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;"><?PHP echo $form_data['zusammenfassung']; ?></textarea>
            <p class="small">
			<!--<input type="text" value="0" id="zcounter" name="zcounter" size="4" style="border:0; text-align:right;" class="small" /> von -->maximal <b><?PHP echo $settings['artikeleinleitungslaenge']; ?> Zeichen</b>
			</p>
        </td>
    </tr>
<?PHP
    }
?>
    <tr>
        <td class="trb" colspan="2"><h2>Weitere Optionen</h2></td>
    </tr>
<?PHP
	if($flag_static == 0){
?>
    <tr>
        <td class="trb" colspan="2"><input type="checkbox" name="top" value="1"<?PHP if($form_data['top'] == 1){ echo " checked=\"checked\""; } ?> /> Artikel oben anpinnen</td>
    </tr>
    <tr>
        <td class="trb" colspan="2"><input type="checkbox" name="hide_signature" value="1"<?PHP if($form_data['hide_signature'] == 1){ echo " checked=\"checked\""; } ?> /> Signatur bei diesem Artikel nicht anzeigen</td>
    </tr>
<?PHP
		}
	if($settings['comments'] == 1 && $settings['artikelcomments'] == 1){
?>
    <tr>
        <td class="trb" colspan="2"><input type="checkbox" name="comments" value="1"<?PHP if($form_data['comments'] == 1){ echo " checked=\"checked\""; } ?> /> Kommentare in diesem Beitrag <b>de</b>aktivieren?</td>
    </tr>
<?PHP
		}
	if($flag_static == 1){
?>
    <tr>
        <td class="trb" colspan="2"><input type="checkbox" name="hide_headline" value="1"<?PHP if($form_data['hide_headline'] == 1){ echo " checked=\"checked\""; } ?> /> Headline (Autor, Datum &amp; Uhrzeit, etc.) ausblenden?</td>
    </tr>
<?PHP
		}
?>
    <tr>
        <td class="trb" colspan="2">
			<b>Autor:</b> <?PHP echo $form_data['username']; ?>
			<?PHP if($userdata['editarticle'] == 2 && $flag_static == 0 || $flag_static == 1 && $userdata['staticarticle'] == 2){ echo "[ <a href=\"javascript:fade_element('change_autor');\">Autor ändern</a> ]"; ?>
			<div id="change_autor" class="moo_hide" style="float:right; margin-right:40%;">
				<b>Neuer Autor:</b>
				<select name="autor" size="1" class="input_select">
					<?PHP echo create_UserDropDown(TRUE,$form_data['uid']); ?>
				</select>
			</div>
			<?PHP } ?>
		</td>
    </tr>
	
    <tr>
        <td class="tra"><input type="reset" class="input" value="Reset" /></td>
        <td class="tra" align="right">
		<?PHP if($form_data['uid'] == $userdata['id'] || $form_data['uid'] == 0){ ?>
            <input type="submit" class="input" name="save" value="<?PHP echo $input_field['save']; ?>" />
		<?PHP } ?>
            <input type="submit" class="input" name="publish" value="<?PHP echo $input_field['publish']; ?>" />
            <input type="hidden" name="do" value="<?PHP echo $input_do; ?>" />
            <input type="hidden" name="id" value="<?PHP if(isset($form_data['id'])){ echo $form_data['id']; } ?>" />
            <input type="hidden" name="uid" value="<?PHP echo $form_data['uid']; ?>" />
            <input type="hidden" name="static" value="<?PHP echo $flag_static; ?>" />
            <input type="hidden" name="action" value="<?PHP echo $input_action; ?>" />
            <input type="hidden" name="who" value="<?PHP echo $input_section2; ?>" />
        </td>
    </tr>

</table>
</form>

<br />
<?PHP
if($form_data['autozusammen'] == 1)
	echo "
<script type=\"text/javascript\">
hide_unhide_tr('zeditor');
</script>";

?>