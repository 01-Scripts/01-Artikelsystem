<?PHP
// 3.2.0 --> 3.2.1
if(isset($_REQUEST['update']) && $_REQUEST['update'] == "320_zu_321"){
	// 01article #720 - Disqus-Support hinzuf�gen
	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET 
	`name` = 'Kommentarsystem w&auml;hlen', 
    `exp` = 'Zur Nutzung von Disqus muss der Username in den allgemeinen Einstellungen hinterlegt werden.', 
    `formename` =  '01ACP Kommentarsystem|Disqus|Kommentare deaktivieren', 
    `formwerte` =  '1|2|0'
    WHERE `idname` = 'artikelcomments' AND modul = '".$mysqli->escape_string($modul)."' LIMIT 1");

	// 01article #726 - CSS-Code aus Datenbank/Settings in Datei auslagern
	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET 
	`exp` = 'Geben Sie einen absoluten Pfad inkl. <b>http://</b> zu einer externen CSS-Datei an.\nIst dieses Feld leer, wird die Datei templates/style.css aus dem Modulverzeichnis verwendet.'
	WHERE `modul` = '".$mysqli->escape_string($modul)."' AND `idname` = 'extern_css' LIMIT 1");
	$mysqli->query("DELETE FROM ".$mysql_tables['settings']." WHERE `modul` = '".$mysqli->escape_string($modul)."' AND `idname` = 'csscode' LIMIT 1");

	// Versionsnummer aktualisieren
	$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.2.1' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.2.0 nach 3.2.1</h2>

<div class="meldung_erfolg">
	Das Update von Version 3.2.0 auf Version 3.2.1 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<b>Achtung:</b><br />
	Mit diesem Update wurde der CSS-Code zur Gestaltung des Artikelsystems in eine separate Datei ausgelagert
	und kann nicht mehr im 01ACP in den Einstellungen direkt bearbeitet werden.<br />
	Der CSS-Code befindet sich nun in der Datei <i>01module/01article/templates/style.css</i> und kann
	dort ggf. bearbeitet werden.<br />
	<br />

	<b>Mit dem Update wurde unter anderem folgendes verbessert:</b>
	<ul>
		<li>Verwendung von <a href="https://www.google.com/recaptcha/admin" target="_blank">reCAPTCHA</a> als Spamschutz-Alternative (<a href="http://www.01-scripts.de/forum/index.php?page=Thread&amp;threadID=1846" target="_blank">Anleitung</a>)</li>
		<li><a href="https://disqus.com/" target="_blank">Disqus</a> als Kommentarsystem integriert (<a href="http://www.01-scripts.de/forum/index.php?page=Thread&amp;threadID=1847" target="_blank">Anleitung</a>)</li>
		<li>Bearbeiten von Artikeln verbessert</li>
		<li>Art2Gal-Funktion verbessert</li>
		<li>Diverse Fehler behoben. Siehe <a href="http://www.01-scripts.de/down/01article_changelog.txt" target="_blank">changelog.txt</a></li>
	</ul>
	<p><a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a></p>
</div>
<?PHP
}
// 3.1.0 --> 3.2.0
elseif(isset($_REQUEST['update']) && $_REQUEST['update'] == "310_zu_320"){

	// #316 Artikelsystem und Bildergalerie verbinden
	$add2css = "\r\n\r\n/* SLIMBOX */\r\n\r\n#lbOverlay {\r\n	position: fixed;\r\n	z-index: 9999;\r\n	left: 0;\r\n	top: 0;\r\n	width: 100%;\r\n	height: 100%;\r\n	background-color: #000;				/* Overlay-Hintergrundfarbe der Lightbox-Abdunklung */\r\n	cursor: pointer;\r\n}\r\n\r\n#lbCenter, #lbBottomContainer {\r\n	position: absolute;\r\n	z-index: 9999;\r\n	overflow: hidden;\r\n	background-color: #fff;				/* Hintergrundfarbe des Untertitel-Bereichs */\r\n}\r\n\r\n#lbImage {\r\n	position: absolute;\r\n	left: 0;\r\n	top: 0;\r\n	border: 10px solid #fff;			/* Bildrahmenfarbe um das in der Lightbox ge�ffnete Bild herum */\r\n	background-repeat: no-repeat;\r\n}\r\n\r\n#lbPrevLink, #lbNextLink {\r\n	display: block;\r\n	position: absolute;\r\n	top: 0;\r\n	width: 50%;\r\n	outline: none;\r\n}\r\n\r\n#lbPrevLink {\r\n	left: 0;\r\n}\r\n#lbNextLink {\r\n	right: 0;\r\n}\r\n\r\n/* Untertitel-Textdefinition */\r\n#lbBottom {\r\n	font-family: Verdana, Arial, Geneva, Helvetica, sans-serif;\r\n	font-size: 10px;\r\n	color: #666;\r\n	line-height: 1.4em;\r\n	text-align: left;\r\n	border: 10px solid #fff;\r\n	border-top-style: none;\r\n}\r\n\r\n#lbCloseLink {\r\n	display: block;\r\n	float: right;\r\n	width: 66px;\r\n	height: 22px;\r\n	margin: 5px 0;\r\n	outline: none;\r\n}\r\n\r\n#lbCaption, #lbNumber {\r\n	margin-right: 71px;\r\n}\r\n#lbCaption {\r\n	font-weight: bold;\r\n}\r\n\r\n
\r\n\r\n/* Formatierte Ausgabe von Galerie-Thumbnails im Artikelsystem */
/* Die Breite muss mit der f�r die Galerie voreingestellten Thumbnail-Breite �bereinstimmen */
.thumbnail_art2gal{
    float: left;
    width: 100px; /* Thumbnail-Breite */
    border: 1px solid #999;
    margin: 0 10px 10px 0; /* Abstand zwischen den einzelnen Thumbnails */
    padding: 5px; /* Abstand zwischen Bild und Rand */
}

/* div um die Thumbnails innerhalb von Artikeln */
div.cssgallery_art2gal {}";
	$list = $mysqli->query("SELECT id,wert FROM ".$mysql_tables['settings']." WHERE modul = '".$mysqli->escape_string($modul)."' AND idname = 'csscode'");
	while($row = $list->fetch_assoc()){
		$mysqli->query("UPDATE ".$mysql_tables['settings']." SET `wert` = '".$row['wert'].$add2css."' WHERE `id` = '".$row['id']."' LIMIT 1");
		}

	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET `formwerte` = '1|0' WHERE `idname` = 'artikellightbox' AND modul = '".$mysqli->escape_string($modul)."' LIMIT 1");

	// Spaltenname 'timestamp' umbenennen in 'utimestamp' #692
	$mysqli->query("ALTER TABLE ".$mysql_tables['artikel']." CHANGE `timestamp` `utimestamp` INT( 15 ) NOT NULL DEFAULT '0'");
	// Spaltenname 'text' umbenennen in 'content' #692
	$mysqli->query("ALTER TABLE ".$mysql_tables['artikel']." CHANGE `text` `content` TEXT NULL DEFAULT NULL");

	// Versionsnummer aktualisieren
	$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.2.0' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.1.0 nach 3.2.0</h2>

<div class="meldung_erfolg">
	Das Update von Version 3.1.0 auf Version 3.2.0 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<b>Achtung: &Uuml;berarbeitung von CSS-Eigenschaften:</b><br />
	Mit diesem Update wurden zwei neue CSS-Definitionen hinzugef&uuml;gt.
	Sollten Sie den CSS-Code in eine externe .css-Datei ausgelagert haben, m&uuml;ssen Sie folgende neuen
	CSS-Klassen manuell hinzuf&uuml;gen:<br />
	<a href="https://gist.github.com/01-Scripts/ede65f4ff6a182fa320f" target="_blank">Neuen CSS-Code auf Github aufrufen</a>
	<br />
	Folgende Dateien und Verzeichnisse werden nach dem Update nicht mehr ben&ouml;tigt und k&ouml;nnen gel&ouml;scht werden:
	<ul>
		<li>Verzeichnis <i>01module/01article/images/icons/</i></li>
	</ul>

	<b>Mit dem Update wurde unter anderem folgendes verbessert:</b>
	<ul>
		<li>Direkte Verwendung von Bildern aus der <a href="http://www.01-scripts.de/01gallery.php" target="_blank">01-Gallery</a> innerhalb des Artikelsystems m&ouml;glich.</li>
		<li>mod_rewrite-Funktionalit&auml;t auch bei mehreren Installationen</li>
		<li>Verbesserungen beim RSS-Feed</li>
		<li>UTF8-Kompatibilit&auml;t wesentlich verbessert</li>
		<li>Spamschutz-Funkton verbessert</li>
		<li>Diverse weitere Bugfixes. Siehe <a href="http://www.01-scripts.de/down/01article_changelog.txt" target="_blank">changelog.txt</a></li>
	</ul>
	<p><a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a></p>
</div>

<?PHP if($settings['modrewrite'] == 1) { ?>
<div class="meldung_error">
	<b>Achtung:</b> F�r die Nutzung der mod_rewrite-Funktion hat sich die Syntax ge�ndert.<br />
	Bitte passen Sie ihre <i>.htaccess</i>-Datei an:<br />
	<br />
	Bisherige Syntax:<br />
	<code>RewriteRule ^(.*),([0-9]+).html$ /<b>includepage.php</b>?<?php echo $names['artid']; ?>=$2#01id$2 [L]</code><br />
	<b>Neue Syntax</b>:<br />
	<code>RewriteRule ^(.*),<b class="red"><?php echo $module[$modul]['nr']; ?></b>,([0-9]+).html$ /<b>includepage.php</b>?<?php echo $names['artid']; ?>=$2#01id$2 [L]</code>
</div>
<?PHP } 

}

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
	
// #369 Signatur f�r einzelne Eintr�ge deaktivierbar machen
$mysqli->query("ALTER TABLE `".$mysql_tables['artikel']."` ADD `hide_signature` TINYINT( 1 ) DEFAULT '0' AFTER `hits`");

// #297 Einfacheres Hinzuf�gen von neuen Feldern
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

// #427 Passende Men�eintr�ge
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

// #427 Berechtigung f�r User mit Level 10 richtig setzen 1 --> 2
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

// CSS-Eigenschaft �berarbeiten
$add2css = "\r\n\r\ndiv.footline_small {
	border-bottom: 1px dotted #999;
	clear: both;
	}
/* Jedes Element (headline,text,footline) hat zus�tzlich diese Klasse */
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

<p class="meldung_hinweis"><b>DIESE SEITE DARF NICHT �BER F5 NEU GELADEN WERDEN!!!</b></p>

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
/* Jedes Element (headline,text,footline) hat zus�tzlich diese Klasse */<br />
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
	Weitere Informationen zu allen �brigen �nderungen in der Version 3.0.0.3 finden Sie im
	<a href="http://www.01-scripts.de/down/01article_changelog.txt" target="_blank">Changelog</a> bzw.
	im entsprechenden Newsbeitrag auf <a href="http://www.01-scripts.de" target="_blank">01-Scripts.de</a>.
</p>
<?php
	}
// 3.0.0.1 --> 3.0.0.2
elseif(isset($_REQUEST['update']) && $_REQUEST['update'] == "3001_zu_3002"){

// Neues "Recht" einf�gen:
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

// Umbenennung .small -> .small01acp durchf�hren
$list = $mysqli->query("SELECT id,wert FROM ".$mysql_tables['settings']." WHERE modul = '".$mysqli->escape_string($modul)."' AND idname = 'csscode'");
while($row = $list->fetch_assoc()){
	$mysqli->query("UPDATE `".$mysql_tables['settings']."` SET `wert` = '".$mysqli->escape_string(str_replace(".small",".small01acp",stripslashes($row['wert']))."\r\n\r\n.lightbox {\r\n\r\n}")."' WHERE `id` = '".$row['id']."' LIMIT 1");
	}

// Versionsnummer aktualisieren
$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.2' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.0.0.1 nach 3.0.0.2</h2>

<p class="meldung_hinweis"><b>DIESE SEITE DARF NICHT �BER F5 NEU GELADEN WERDEN!!!</b></p>

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
	Weitere Informationen zu allen �brigen �nderungen in der Version 3.0.0.2 finden Sie im 
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