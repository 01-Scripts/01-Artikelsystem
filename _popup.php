<?PHP
/*
	01-Artikelsystem V3 - Copyright 2006-2010 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01article
	Dateiinfo:	Popup-Inhalt
	#fv.3010#
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
?>