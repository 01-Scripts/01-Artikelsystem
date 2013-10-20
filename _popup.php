<?PHP
/*
	01-Artikelsystem V3 - Copyright 2006-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01article
	Dateiinfo:	Popup-Inhalt
	#fv.311#
*/

// Mod_rewirte-Info ausgeben
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "mod_rewrite_info"){
?>
<h1>mod_rewrite <i>("sprechende Links")</i></h1>

<p>Um die <a href="http://de.wikipedia.org/wiki/Rewrite-Engine" target="_blank">mod_rewrite</a>-Funktionalit&auml;t des 01-Artikelsystems nutzen zu k&ouml;nnen,
muss ihr Server/Webspace folgende Voraussetzungen erf&uuml;llen:</p>

<ul>
	<li>Nutzung von .htaccess-Dateien gestattet</li>
	<li>mod_rewrite-Unterst&uuml;tzung vorhanden &amp; erlaubt</li>
</ul>

<p>Wenn noch nicht vorhanden, legen Sie im Hauptverzeichnis Ihres Servers eine Textdatei mit Namen
<i>.htaccess</i> an .<br />
Achten Sie dabei auf die genaue Bezeichnung (es wird KEINE Endung verwendet)!</p>

<p>Kopieren Sie folgenden Code in die Datei</p>

<code>
RewriteEngine On<br />
RewriteRule ^(.*),([0-9]+).html$ /<b>includepage.php</b>?<?php echo $names['artid']; ?>=$2#01id$2 [L]
</code>

<p><b>includepage.php</b> durch den entsprechenden Dateinamen ersetzen.</p>

<p>Speichern Sie die Datei und aktivieren Sie die mod_rewrite-Funktion in den Einstellungen.<br />
Danach sollten sog. "sprechende" Links zur Verf&uuml;gung stehen.</p>

<?PHP
	}

// Normales TinyMCE-UPLOADER-POPUP
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "tiny_uploader"){
	if(!isset($_REQUEST['type'])) $_REQUEST['type'] = $_REQUEST['var1'];
	if(!isset($_REQUEST['formname'])) $_REQUEST['formname'] = $_REQUEST['var2'];
	if(!isset($_REQUEST['formfield'])) $_REQUEST['formfield'] = $_REQUEST['var3'];
	$_REQUEST['returnvalue'] = "tinymce";
	
	$filename = $filename."?action=tiny_uploader&amp;";
	include_once("system/uploader.php");
	}

// Verbindung 01-Artikelsystem & 01-Gallery
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "art2gal" && $art2galsupport && $userdata['dateimanager'] == 2){

    $modul_save = $modul;
    $galmodul = array();
    $galmodule = getModuls($galmodul,"01gallery"); 
    
    if(count($galmodul) > 0){
        $modul = $galmodul[$art2gal_galnr];
        include_once($moduldir.$galmodule[$galmodul[$art2gal_galnr]]['idname']."/_headinclude.php");
        include_once($moduldir.$galmodule[$galmodul[$art2gal_galnr]]['idname']."/_functions.php");
        $modul = $modul_save;
?>        

<h1>Bilder aus Bildergalerie einfügen</h1>
        
<p>
<input type="text" name="pics_anzahl" size="3" value="5" /> Bilder aus
<select name="galid" id="sel_galid" size="1">
    <option value="0">Bildergalerie</option>
    <?PHP _01gallery_getGallerysRek(0,0,-1,"_01gallery_echoGalinfo_select","","",FALSE); ?>
</select>
<input type="button" value="Einf&uuml;gen" class="input" onclick="FileDialog.insertgalpics();" />
</p>

<p>
	<b>Bitte beachten:</b> Die Bilder sind im Editor nicht sichtbar.
	Es wird ein Text in folgendem Format im Editor eingefügt:<br />
	<code>{Insert#5GalleryPicsFrom#2}</code><br />
	Dieser Text wird während der Ausgabe an der entsprechenden Stelle durch Thumbnails der Bildergalerie ersetzt.<br />
	<br />
	<b>Bitte nehmen Sie an diesem Textbaustein keine Änderung vor!</b><br />
	<br />
	Entfernen Sie den Textbaustein um die Thumbnails aus dem Artikel wieder zu entfernen.
</p>

<?PHP
		}
	else{
		echo "<h1>Fehler</h1>
		<p>Um diese Funktion nutzen zu können muss das Modul 01-Gallery installiert sein.</p>";
		}
	}
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "art2gal" && $art2galsupport && $userdata['dateimanager'] != 2){
	echo "<h1>Zugriff verweigert</h1>
	<p>Sie haben keine Berechtigung diese Funktion zu nutzen.<br />Bitte wenden Sie sich ggf. an den Administrator.</p>";
	}
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "art2gal" && !$art2galsupport){
	echo "<h1>Fehler</h1>
	<p>Diese Funktion wurde vom Administrator deaktiviert.</p>";
	}
?>