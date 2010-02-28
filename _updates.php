<?PHP
// 3.0.0.2 --> 3.0.0.3
if(isset($_REQUEST['update']) && $_REQUEST['update'] == "3002_zu_3003"){
// Artikel-Tabelle anpassem:
mysql_query("ALTER TABLE `".$mysql_tables['artikel']."` CHANGE `autozusammen` `autozusammen` TINYINT( 1 ) NULL");
mysql_query("ALTER TABLE `".$mysql_tables['artikel']."` CHANGE `top` `top` TINYINT( 1 ) NULL");

// CSS-Eigenschaft überarbeiten
$add2css = "\r\n\r\ndiv.footline_small {
	border-bottom: 1px dotted #999;
	clear: both;
	}
/* Jedes Element (headline,text,footline) hat zusätzlich diese Klasse */
div.inner_box {
	width:100%;
	display:block;
	}";
$list = mysql_query("SELECT id,wert FROM ".$mysql_tables['settings']." WHERE modul = '".mysql_real_escape_string($modul)."' AND idname = 'csscode'");
while($row = mysql_fetch_array($list)){
	mysql_query("UPDATE `".$mysql_tables['settings']."` SET `wert` = '".mysql_real_escape_string(str_replace("border-bottom: 1px dotted #999;","",stripslashes($row['wert'])).$add2css)."' WHERE `id` = '".$row['id']."' LIMIT 1");
	}

// Versionsnummer aktualisieren
mysql_query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.3' WHERE idname = '".mysql_real_escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.0.0.2 nach 3.0.0.3</h2>

<p class="meldung_hinweis"><b>DIESE SEITE DARF NICHT ÜBER F5 NEU GELADEN WERDEN!!!</b></p>

<p class="meldung_erfolg">
	Das Update von Version 3.0.0.2 auf Version 3.0.0.3 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a><br />
	<br />
	<b>Achtung: &Uuml;berarbeitung von CSS-Eigenschaften:</b><br />
	Mit diesem Update wurden einige &Auml;nderungen an den Standard-CSS-Definitionen vorgenommen.
	Sollten Sie den CSS-Code in eine externe .css-Datei ausgelagert haben, m&uuml;ssen Sie nachfolgende neue
	Klassen manuell anf&uuml;gen:<br />
<code>
div.footline_small {<br />
	border-bottom: 1px dotted #999;<br />
	clear: both;<br />
	}<br /><br />
/* Jedes Element (headline,text,footline) hat zusätzlich diese Klasse */<br />
div.inner_box {<br />
	width:100%;<br />
	display:block;<br />
	}<br />
</code><br />

	Au&szlig;erdem muss in der CSS-Klasse <i>.artikel_textbox { ... }</i><br />der bisher enthaltene Inhalt
	(border-bottom: 1px dotted #999;) <b>entfernt werden!</b><br />
	<a href="http://www.01-scripts.de/forum/index.php?page=Thread&threadID=1285" target="_blank">Hier k&ouml;nnen Sie den kompletten original CSS-Code des 01-Artikelsystems V 3.0.0.3 aufrufen</a><br />

	<br />
	<b>Parameter f&uuml;r PHP-include() haben sich ge&auml;ndert:</b><br />
	Die Parameter f&uuml;r den Include des Artikelsystems via PHP haben sich ge&auml;ndert.<br />
	Die bisherigen Parameter funktionieren weiterhin, sollten aber durch die neuen Parameter bei Gelegenheit ersetzt werden.
	Alle Informationen dazu <a href="http://www.01-scripts.de/forum/index.php?page=Thread&threadID=921" target="_blank">finden Sie hier</a>.<br />
	
	<br />
	Weitere Informationen zu allen übrigen Änderungen in der Version 3.0.0.3 finden Sie im
	<a href="http://www.01-scripts.de/down/01article_changelog.txt" target="_blank">Changelog</a> bzw.
	im entsprechenden Newsbeitrag auf <a href="http://www.01-scripts.de" target="_blank">01-Scripts.de</a>.
</p>
<?php
	}
// 3.0.0.1 --> 3.0.0.2
elseif(isset($_REQUEST['update']) && $_REQUEST['update'] == "3001_zu_3002"){

// Neues "Recht" einfügen:
$sql_insert = "INSERT INTO ".$mysql_tables['rights']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,nodelete,hide,in_profile) VALUES 
			( '".mysql_real_escape_string($modul)."', '0', '1', '5', 'editcats', 'Kategorien verwalten', '', 'Ja|Nein', '1|0', '', '0', '0', '0', '0')";
$result = mysql_query($sql_insert) OR die(mysql_error());
mysql_query("ALTER TABLE `".$mysql_tables['user']."` ADD `".mysql_real_escape_string($modul)."_editcats` VARCHAR( 255 ) NOT NULL DEFAULT '0'");
mysql_query("UPDATE `".$mysql_tables['user']."` SET `".mysql_real_escape_string($modul)."_editcats` = '1' WHERE `id` = '".$userdata['id']."' LIMIT 1");
mysql_query("UPDATE `".$mysql_tables['menue']."` SET `rightname` = 'editcats' WHERE `link` = '_loader.php?modul=".mysql_real_escape_string($modul)."&amp;action=&amp;loadpage=category' LIMIT 1");

// Artikel-Tabelle anpassen:
mysql_query("ALTER TABLE `".$mysql_tables['artikel']."` ADD `top` TINYINT( 1 ) NOT NULL AFTER `static`");

// Artikel-Kategorie-Tabelle anpassen:
mysql_query("ALTER TABLE `".$mysql_tables['cats']."` ADD `sortid` INT( 5 ) NOT NULL DEFAULT '1'");

// Neue Einstellungen anlegen:
$sql_insert = "INSERT INTO ".$mysql_tables['settings']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES 
			('".mysql_real_escape_string($modul)."','0','1','7','artikelcomments','Kommentare f&uuml;r Modul aktivieren','Kommentarsystem muss zus&auml;tzlich in den allgemeinen 01ACP-Einstellungen aktiviert sein.','Ja|Nein','1|0','','1','".$settings['comments']."','0','0'),
			('".mysql_real_escape_string($modul)."','0','1','8','artikellightbox','Lightbox zur Bildansicht nutzen?','','Ja|Nein','1|0','','1','0','0','0');";
$result = mysql_query($sql_insert) OR die(mysql_error());

// Umbenennung .small -> .small01acp durchführen
$list = mysql_query("SELECT id,wert FROM ".$mysql_tables['settings']." WHERE modul = '".mysql_real_escape_string($modul)."' AND idname = 'csscode'");
while($row = mysql_fetch_array($list)){
	mysql_query("UPDATE `".$mysql_tables['settings']."` SET `wert` = '".mysql_real_escape_string(str_replace(".small",".small01acp",stripslashes($row['wert']))."\r\n\r\n.lightbox {\r\n\r\n}")."' WHERE `id` = '".$row['id']."' LIMIT 1");
	}

// Versionsnummer aktualisieren
mysql_query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.2' WHERE idname = '".mysql_real_escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.0.0.1 nach 3.0.0.2</h2>

<p class="meldung_hinweis"><b>DIESE SEITE DARF NICHT ÜBER F5 NEU GELADEN WERDEN!!!</b></p>

<p class="meldung_erfolg">
	Das Update von Version 3.0.0.1 auf Version 3.0.0.2 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a><br />
	<br />
	<b>Wichtige Hinweise zur erfolgreichen Nutzung einiger neuer Features:</b><br />
	<b>Lightbox:</b><br />
	Bilder die &uuml;ber den Adminbereich hochgeladen wurden und in einen Beitrag eingebunden werden,
	k&ouml;nnen ab jetzt in einer sog. <i>Lightbox</i> (statt in einem neuen Browserfenster)
	ge&ouml;ffnet werden.<br />
	Diese Funktion kann in den Einstellungen aktiviert werden.<br />
	Bitte beachten Sie: Diese Funktion wirkt sich nur auf Bilder aus, die NACH dem Update (ggf. erneut)
	in einen Beitrag eingebunden werden!<br />
	<a href="http://www.01-scripts.de/board/thread.php?threadid=1012" target="_blank">Weitere Informationen
	zur erfolgreichen Nutzung der Lightbox finden Sie hier</a>.<br />
	<b>Kommentarsystem:</b><br />
	Die Kommentarfunktion kann nun f&uuml;r jedes Modul extra in den Einstellungen de/aktiviert werden.<br />
	<b>Kategorieverwaltung:</b><br />
	F&uuml;r die Verwaltung von Artikelkategorien steht nun eine eigene Berechtigung f&uuml;r jeden Benutzer zur
	Verf&uuml;gung.<br />
	<b>Autor &auml;ndern:</b><br />
	Geschriebene Artikel k&ouml;nnen von Benutzern, die alle Artikel bearbeiten d&uuml;rfen, anderen Benutzern zugewiesen werden.<br />
	<b>Umbennenung der CSS-Klasse .small:</b><br />
	Die CSS-Klasse .small, die f&uuml;r Ausgabe von <i>kleinem</i> Text zust&auml;ndig war wurde aus Gr&uuml;nden der
	Kompatibilit&auml;t in <b>.small01acp</b> umbenannt. Sollten Sie eine eigene CSS-Datei verwenden m&uuml;ssen Sie
	den Klassennamen dort ggf. anpassen!<br />
	<br />
	Weitere Informationen zu allen übrigen Änderungen in der Version 3.0.0.2 finden Sie im 
	<a href="http://www.01-scripts.de/down/01article_changelog.txt" target="_blank">Changelog</a> bzw.
	im entsprechenden Newsbeitrag auf <a href="http://www.01-scripts.de" target="_blank">01-Scripts.de</a>.
</p>
<?PHP
	}
// 3.0.0.0 --> 3.0.0.1
elseif(isset($_REQUEST['update']) && $_REQUEST['update'] == "3000_zu_3001"){

mysql_query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.1' WHERE idname = '".mysql_real_escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.0.0.0 nach 3.0.0.1</h2>

<p class="meldung_erfolg">
	Das Update von Version 3.0.0.0 auf Version 3.0.0.1 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a>
</p>
<?PHP
	}
?>