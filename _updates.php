<?PHP
// 3.0.0.4 --> 3.1.0
if(isset($_REQUEST['update']) && $_REQUEST['update'] == "3004_zu_310"){

// Update CSS-Code in settings
$add2css = "\r\n\r\n.meldungen_01 {
	line-height:16pt;
	text-align: left;
	font-size:12px;
	background-color: #DDDDDD;

	margin: 20px 0;
	padding: 5px 20px 5px 20px;
	border-top: 2px solid #000;
	border-bottom: 2px solid #000;
	}";
$list = $mysqli->query("SELECT id,wert FROM ".$mysql_tables['settings']." WHERE modul = '".$mysqli->escape_string($modul)."' AND idname = 'csscode'");
while($row = $list->fetch_assoc()){
	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET `wert` = '".$mysqli->escape_string(str_replace(".table_archiv_headline","td.archiv_month { }\n\ntd.archiv_year{ }\n\n.table_archiv_headline",str_replace("width: 800px;","width: 100%;",stripslashes($row['wert']))).$add2css)."' WHERE `id` = '".$row['id']."' LIMIT 1");
	}
	
// #369 Signatur für einzelne Einträge deaktivierbar machen
$mysqli->query("ALTER TABLE `".$mysql_tables['artikel']."` ADD `hide_signature` TINYINT( 1 ) DEFAULT '0' AFTER `hits`");

// #297 Einfacheres Hinzufügen von neuen Feldern
$mysqli->query("ALTER TABLE `".$mysql_tables['artikel']."` ADD `serialized_data` MEDIUMBLOB NULL COMMENT 'use unserialize() to get data back'");

// #427 Right-Anpassung: Nur eigene statische Seiten bearbeitbar machen
$mysqli->query("UPDATE `".$mysql_tables['rights']."` SET `formename` = 'Nur eigene Seiten|Alle Seiten|Kein Zugriff',
`formwerte` = '1|2|0' WHERE `idname` = 'staticarticle' AND `modul` = '".$mysqli->escape_string($modul)."';");

// #427 Darstellung harmonisieren
$mysqli->query("UPDATE `".$mysql_tables['rights']."` SET `formename` = 'Nur eigene Artikel bearbeiten|Alle Artikel bearbeiten &amp; freischalten|Kein Zugriff'
WHERE `idname` = 'editarticle' AND `modul` = '".$mysqli->escape_string($modul)."';");
$mysqli->query("UPDATE `".$mysql_tables['rights']."` SET `name` = 'Freischaltung von Artikeln &amp; Seiten', 
`exp` = 'Artikel und statische Seiten dieses Benutzers m&uuml;ssen vor der Ver&ouml;ffentlichung von einem Moderator freigeschaltet werden.',
`formename` = 'Freischaltung n&ouml;tig|Keine Freischaltung n&ouml;tig'
WHERE `idname` = 'freischaltung' AND `modul` = '".$mysqli->escape_string($modul)."';");

// #427 Passende Menüeinträge
$mysqli->query("INSERT INTO `".$mysql_tables['menue']."` (
`id` ,
`name` ,
`link` ,
`modul` ,
`sicherheitslevel` ,
`rightname` ,
`rightvalue` ,
`sortorder` ,
`subof` ,
`hide` ) VALUES
(NULL , 'Neue statische Seite', '_loader.php?modul=".$mysqli->escape_string($modul)."&amp;action=newstatic&amp;loadpage=article', '".$mysqli->escape_string($modul)."', '1', 'staticarticle', '2', '3', '0', '0'),
(NULL , 'Statische Seiten bearbeiten', '_loader.php?modul=".$mysqli->escape_string($modul)."&amp;action=statics&amp;loadpage=article', '".$mysqli->escape_string($modul)."', '1', 'staticarticle', '2', '4', '0', '0');");

// #427 Berechtigung für User mit Level 10 richtig setzen 1 --> 2
$mysqli->query("UPDATE `".$mysql_tables['user']."` SET `".$mysqli->escape_string($modul)."_staticarticle` = '2' WHERE `id` = '".$userdata['id']."' OR `level` = '10'");

// #427 Darstellung harmonisieren
$mysqli->query("UPDATE `".$mysql_tables['menue']."` SET `name` = 'Statische Seiten bearbeiten' WHERE `name` = 'Statische Seiten' AND `modul` = '".$mysqli->escape_string($modul)."';");

// #474 Volltext-Index anlegen
$mysqli->query("ALTER TABLE `".$mysql_tables['artikel']."` ADD FULLTEXT (titel,text,zusammenfassung);");

// Neue Einstellungen anlegen (modrewrite; wenn noch nicht vorhanden)
$list = $mysqli->query("SELECT idname FROM ".$mysql_tables['settings']." WHERE modul = '".$mysqli->escape_string($modul)."' AND idname = 'modrewrite' LIMIT 1");
$row_s = $list->fetch_assoc();
if(!isset($row_s['idname']) || isset($row_s['idname']) && $row_s['idname'] != "modrewrite"){
	$sql_insert = "INSERT INTO ".$mysql_tables['settings']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES
				('".$mysqli->escape_string($modul)."','0','1','9','modrewrite','mod_rewrite aktivieren','<a href=\"javascript:modulpopup(\'".$mysqli->escape_string($modul)."\',\'mod_rewrite_info\',\'\',\'\',\'\',510,450);\">Anleitung lesen</a>','Aktivieren|Deaktivieren','1|0','','0','0','0','0');";
	$result = $mysqli->query($sql_insert) OR die($mysqli->error);
	}


// Versionsnummer aktualisieren
$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.1.0' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.0.0.4 nach 3.1.0</h2>

<p class="meldung_erfolg">
	Das Update von Version 3.0.0.4 auf Version 3.1.0 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a><br />
	<br />
	<b>Achtung: &Uuml;berarbeitung von CSS-Eigenschaften:</b><br />
	Mit diesem Update wurden einige &Auml;nderungen an den Standard-CSS-Definitionen vorgenommen.
	Sollten Sie den CSS-Code in eine externe .css-Datei ausgelagert haben, m&uuml;ssen Sie folgende neue
	CSS-Klassen manuell hinzuf&uuml;gen:<br />
<code>
.meldungen_01 {<br />
	line-height:16pt;<br />
	text-align: left;<br />
	font-size:12px;<br />
	background-color: #DDDDDD;<br />
<br />
	margin: 20px 0;<br />
	padding: 5px 20px 5px 20px;<br />
	border-top: 2px solid #000;<br />
	border-bottom: 2px solid #000;<br />
	}<br />
</code><br />
	Es wurden dar&uuml;ber hinaus noch einige weitere kleinere &Auml;nderungen an den
	Standard-CSS-Definitionen vorgenommen, die aus Kompatibilit&auml;tsgr&uuml;nden allerdings
	durch dieses Update nicht automatisch eingef&uuml;gt wurden.<br />
	Der f&uuml;r die Version 3.1.0 komplette aktuelle CSS-Code kann
	<a href="https://gist.github.com/733783/ea48189f8bb7437e0bb5cbcb4d829d6f40950a72" target="_blank">hier eingesehen werden</a>.
</p>
<?PHP
	}
// 3.0.0.3 --> 3.0.0.4
if(isset($_REQUEST['update']) && $_REQUEST['update'] == "3003_zu_3004"){
// Versionsnummer aktualisieren
$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.4' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.0.0.3 nach 3.0.0.4</h2>

<p class="meldung_erfolg">
	Das Update von Version 3.0.0.3 auf Version 3.0.0.4 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a>
</p>
<?PHP
	}
// 3.0.0.2 --> 3.0.0.3
elseif(isset($_REQUEST['update']) && $_REQUEST['update'] == "3002_zu_3003"){
// Artikel-Tabelle anpassem:
$mysqli->query("ALTER TABLE `".$mysql_tables['artikel']."` CHANGE `autozusammen` `autozusammen` TINYINT( 1 ) NULL");
$mysqli->query("ALTER TABLE `".$mysql_tables['artikel']."` CHANGE `top` `top` TINYINT( 1 ) NULL");

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
$list = $mysqli->query("SELECT id,wert FROM ".$mysql_tables['settings']." WHERE modul = '".$mysqli->escape_string($modul)."' AND idname = 'csscode'");
while($row = $list->fetch_assoc()){
	$mysqli->query("UPDATE `".$mysql_tables['settings']."` SET `wert` = '".$mysqli->escape_string(str_replace("border-bottom: 1px dotted #999;","",stripslashes($row['wert'])).$add2css)."' WHERE `id` = '".$row['id']."' LIMIT 1");
	}

// Versionsnummer aktualisieren
$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.3' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
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
	<a href="https://gist.github.com/733783/4a8a3c57a039f30e8960c14601f6984b45942b99" target="_blank">Hier k&ouml;nnen Sie den kompletten original CSS-Code des 01-Artikelsystems V 3.0.0.3 aufrufen</a><br />

	<br />
	<b>Parameter f&uuml;r PHP-include() haben sich ge&auml;ndert:</b><br />
	Die Parameter f&uuml;r den Include des Artikelsystems via PHP haben sich ge&auml;ndert.<br />
	Die bisherigen Parameter funktionieren weiterhin, sollten aber durch die neuen Parameter bei Gelegenheit ersetzt werden.
	Alle Informationen dazu <a href="http://www.01-scripts.de/forum/index.php?page=Thread&amp;threadID=921" target="_blank">finden Sie hier</a>.<br />
	
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
			( '".$mysqli->escape_string($modul)."', '0', '1', '5', 'editcats', 'Kategorien verwalten', '', 'Ja|Nein', '1|0', '', '0', '0', '0', '0')";
$result = $mysqli->query($sql_insert) OR die($mysqli->error);
$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` ADD `".$mysqli->escape_string($modul)."_editcats` VARCHAR( 255 ) NOT NULL DEFAULT '0'");
$mysqli->query("UPDATE `".$mysql_tables['user']."` SET `".$mysqli->escape_string($modul)."_editcats` = '1' WHERE `id` = '".$userdata['id']."' LIMIT 1");
$mysqli->query("UPDATE `".$mysql_tables['menue']."` SET `rightname` = 'editcats' WHERE `link` = '_loader.php?modul=".$mysqli->escape_string($modul)."&amp;action=&amp;loadpage=category' LIMIT 1");

// Artikel-Tabelle anpassen:
$mysqli->query("ALTER TABLE `".$mysql_tables['artikel']."` ADD `top` TINYINT( 1 ) NOT NULL AFTER `static`");

// Artikel-Kategorie-Tabelle anpassen:
$mysqli->query("ALTER TABLE `".$mysql_tables['cats']."` ADD `sortid` INT( 5 ) NOT NULL DEFAULT '1'");

// Neue Einstellungen anlegen:
$sql_insert = "INSERT INTO ".$mysql_tables['settings']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES 
			('".$mysqli->escape_string($modul)."','0','1','7','artikelcomments','Kommentare f&uuml;r Modul aktivieren','Kommentarsystem muss zus&auml;tzlich in den allgemeinen 01ACP-Einstellungen aktiviert sein.','Ja|Nein','1|0','','1','".$settings['comments']."','0','0'),
			('".$mysqli->escape_string($modul)."','0','1','8','artikellightbox','Lightbox zur Bildansicht nutzen?','','Ja|Nein','1|0','','1','0','0','0');";
$result = $mysqli->query($sql_insert) OR die($mysqli->error);

// Umbenennung .small -> .small01acp durchführen
$list = $mysqli->query("SELECT id,wert FROM ".$mysql_tables['settings']." WHERE modul = '".$mysqli->escape_string($modul)."' AND idname = 'csscode'");
while($row = $list->fetch_assoc()){
	$mysqli->query("UPDATE `".$mysql_tables['settings']."` SET `wert` = '".$mysqli->escape_string(str_replace(".small",".small01acp",stripslashes($row['wert']))."\r\n\r\n.lightbox {\r\n\r\n}")."' WHERE `id` = '".$row['id']."' LIMIT 1");
	}

// Versionsnummer aktualisieren
$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.2' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
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

$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.1' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
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