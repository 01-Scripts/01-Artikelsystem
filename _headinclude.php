<?PHP
/*
	01-Artikelsystem V3 - Copyright 2006-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01article
	Dateiinfo: 	Modulspezifische Grundeinstellungen, Variablendefinitionen etc.
				Wird automatisch am Anfang jeden Modulaufrufs automatisch includiert.
	#fv.321#
*/

// Modul-Spezifische MySQL-Tabellen
$mysql_tables['artikel'] 	= "01_".$instnr."_".$module[$modul]['nr']."_article";
$mysql_tables['cats'] 		= "01_".$instnr."_".$module[$modul]['nr']."_articlecategory";

$addJSFile 	= "java.js";			// Zus�tzliche modulspezifische JS-Datei (im Modulverzeichnis!)
$addCSSFile = "modul.css";			// Zus�tzliche modulspezifische CSS-Datei (im Modulverzeichnis!)
$mootools_use = array("moo_core","moo_more","moo_calendar","moo_slideh","moo_request");

// Welche PHP-Seiten sollen abh�ngig von $_REQUEST['loadpage'] includiert werden?
$loadfile['index'] 		= "index.php";			// Standardseite, falls loadpage invalid ist
$loadfile['article'] 	= "article.php";
$loadfile['category']	= "category.php";

// Weitere Pfadangaben
$imagepf 	= "images/";						// Pfad zum Bild-Verzeichnis
$tempdir	= "templates/";						// Template-Verzeichnis

// Weitere Variablen
$comment_desc	= "DESC";				 		// Sortierreihenfolge der Kommentare
$ser_fields     = FALSE;                 		// Einfaches Hinzuf�gen von Feldern aktivieren
$art2galsupport = TRUE;							// Unterst�tzung zum vereinfachten Hinzuf�gen von Galerie-Bildern in Artikel aktivieren?
$art2gal_galnr  = 1;							// Index-Nummer der 01-Gallery-Installation von der Bilder in Artikel eingef�gt werden d�rfen.
$server_domainname = $_SERVER['SERVER_NAME'];	// ggf. Abweichender Domainname f�r mod_rewrite (OHNE http://)

define('CSS_CACHE_DATEI', $tempdir.'style.css');
define('ANZ_SER_FIELDS',25);            		// Max. Anzahl an zus�tzlichen Feldern f�r $ser_fields
define('FULLTEXT_INDEX_SEARCH_SCHWELLE',1.5);	// Schwellenwert f�r Suchergebnisse vgl. http://dev.mysql.com/doc/refman/5.1/de/fulltext-search.html
define('ANZ_PP_ARCHIV',30);						// Anzahl Artikel pro Seite in der Archivansicht

// Language-Variablen
$lang['weiterlesen'] = " ...";
$lang['galpics_rss'] = "{Diese Bilder sind im RSS-Feed leider nicht verf&uuml;gbar}";

// Variablennamen-Deklaration
$names['artid']		= "artid";
$names['search']	= "search";
$names['catid']		= "catid";
$names['page']		= "page";
$names['cpage']		= "cpage";
$names['rss']		= "rss";					//Bei �nderung ist zus�tzlich eine manuelle �nderung in 01article.php Zeile 35 n�tig!

?>