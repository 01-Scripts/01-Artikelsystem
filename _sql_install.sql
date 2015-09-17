-- 01-Artikelsystem V3 - Copyright 2006-2015 by Michael Lorer - 01-Scripts.de
-- Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
-- Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

-- Modul:		01article
-- Dateiinfo:	SQL-Befehle für die Erstinstallation des Artikelsystems V3
-- #fv.321#
--  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  *  *

-- --------------------------------------------------------

SET AUTOCOMMIT=0;
START TRANSACTION;

-- --------------------------------------------------------

-- 
-- Neue Einstellungs-Kategorie für Modul anlegen
-- Einstellungen importieren
-- 

INSERT INTO 01prefix_settings (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES 
('#modul_idname#', 1, 1, 1, 'articlesettings','Einstellungen','','','','','','','0','0'),
('#modul_idname#', 0, 1, 1, 'articleperpage','Artikel pro Seite','','text','5','','5','5','0','0'),
('#modul_idname#', 0, 1, 2, 'artikelsuche','Suchfunktion','','aktivieren|deaktivieren','1|0','','1','1','0','0'),
('#modul_idname#', 0, 1, 3, 'artikelfreischaltung','Freischaltung n&ouml;tig?','Ein neuer Artikel muss vor der Ver&ouml;ffentlichung durch einen Benutzer mit den entsprechenden Rechten freigeschaltet werden.','Freischaltung n&ouml;tig|Keine Freischaltung n&ouml;tig','1|0','','0','0','0','0'),
('#modul_idname#', 0, 1, 4, 'artikeleinleitung','Einleitungstext verwenden?','','Erzwingen|Verwenden, wenn vorhanden|Unterdr&uuml;cken (deaktivieren)','1|2|0','','2','2','0','0'),
('#modul_idname#', 0, 1, 5, 'artikeleinleitungslaenge','L&auml;nge des Einleitungstextes','','text','5','Zeichen','1000','1000','0','0'),
('#modul_idname#', 0, 1, 6, 'archiv_time','Alte Beitr&auml;ge ins Archiv verschieben?','Geben Sie an nach wievielen Tagen Beitr&auml;ge ins Archiv verschoben werden sollen.','text','5','Tage','','','0','0'),
('#modul_idname#', 0, 1, 7, 'artikelcomments','Kommentarsystem w&auml;hlen','Zur Nutzung von Disqus muss der Username in den allgemeinen Einstellungen hinterlegt werden.','01ACP Kommentarsystem|Disqus|Kommentare deaktivieren','1|2|0','','1','1','0','0'),
('#modul_idname#', 0, 1, 8, 'artikellightbox','Lightbox zur Bildansicht nutzen?','','Ja|Nein','1|0','','1','1','0','0'),
('#modul_idname#', 0, 1, 9, 'modrewrite','mod_rewrite aktivieren','<a href="javascript:modulpopup(\'#modul_idname#\',\'mod_rewrite_info\',\'\',\'\',\'\',510,450);">Anleitung lesen</a>','Aktivieren|Deaktivieren','1|0','','0','0','0','0'),

('#modul_idname#', 1, 3, 2, 'csssettings','CSS-Einstellungen','','','','','','','0','0'),
('#modul_idname#', 0, 3, 1, 'extern_css','Externe CSS-Datei','Geben Sie einen absoluten Pfad inkl. <b>http://</b> zu einer externen CSS-Datei an.\nIst dieses Feld leer, wird die Datei templates/style.css aus dem Modulverzeichnis verwendet.','text','50','','','','0','0'),

('#modul_idname#', 1, 2, 3, 'artikelrssfeed','RSS-Feed','','','','','','','0','0'),
('#modul_idname#', 0, 2, 1, 'artikelrssfeedaktiv','RSS-Feed für Artikel aktivieren?','','RSS-Feed aktivieren|RSS-Feed deaktivieren','1|0','','1','1','0','0'),
('#modul_idname#', 0, 2, 2, 'artikelkommentarfeed','RSS-Kommentar-Feed aktivieren','','aktivieren|deaktivieren','1|0','','0','0','0','0'),
('#modul_idname#', 0, 2, 3, 'artikelrsstargeturl','Include/ Ziel-URL','Komplette URL der Datei (inkl. http:// und eventueller Parameter) <b>in die Sie das Modul includiert / eingebunden</b> haben.\r\nWenn Sie die eingetragene URL in Ihrem Browser aufrufen, muss das in Ihre Seite integrierte Artikelmodul angezeigt werden.','text','50','','','','0','0'),
('#modul_idname#', 0, 2, 4, 'artikelrsstitel','Titel des RSS-Feeds','','text','25','','','','0','0'),
('#modul_idname#', 0, 2, 5, 'artikelrssanzahl','Anzahl an Eintr&auml;gen','','text','5','','25','25','0','0'),
('#modul_idname#', 0, 2, 6, 'artikelrsslaenge','RSS-Feed k&uuml;rzen?','M&ouml;chten Sie den kompletten Eintrag im Feed bereitstellen oder nur eine Kurzzusammenfassung?','Kompletten Eintrag zeigen|Kurzversion zeigen','all|short','','all','all','0','0'),
('#modul_idname#', 0, 2, 7, 'artikelrssbeschreibung','Kurzbeschreibung','','textarea','5|50','','','','0','0');



-- --------------------------------------------------------

-- 
-- Menüeinträge anlegen
-- 

INSERT INTO 01prefix_menue (name,link,modul,sicherheitslevel,rightname,rightvalue,sortorder,subof,hide) VALUES 
('<b>Neuen Artikel schreiben</b>', '_loader.php?modul=#modul_idname#&amp;action=newarticle&amp;loadpage=article', '#modul_idname#', '1', 'newarticle', '1', '1', '0', '0'),
('Artikel bearbeiten', '_loader.php?modul=#modul_idname#&amp;action=articles&amp;loadpage=article', '#modul_idname#', '1', 'editarticle', '1', '2', '0', '0'),
('Artikel bearbeiten', '_loader.php?modul=#modul_idname#&amp;action=articles&amp;loadpage=article', '#modul_idname#', '1', 'editarticle', '2', '2', '0', '0'),
('Neue statische Seite', '_loader.php?modul=#modul_idname#&amp;action=newstatic&amp;loadpage=article', '#modul_idname#', '1', 'staticarticle', '1', '3', '0', '0'),
('Statische Seiten bearbeiten', '_loader.php?modul=#modul_idname#&amp;action=statics&amp;loadpage=article', '#modul_idname#', '1', 'staticarticle', '1', '4', '0', '0'),
('Neue statische Seite', '_loader.php?modul=#modul_idname#&amp;action=newstatic&amp;loadpage=article', '#modul_idname#', '1', 'staticarticle', '2', '3', '0', '0'),
('Statische Seiten bearbeiten', '_loader.php?modul=#modul_idname#&amp;action=statics&amp;loadpage=article', '#modul_idname#', '1', 'staticarticle', '2', '4', '0', '0'),
('Kategorien verwalten', '_loader.php?modul=#modul_idname#&amp;action=&amp;loadpage=category', '#modul_idname#', '1', 'editcats', '1', '5', '0', '0'),
('Kommentare verwalten', 'comments.php?modul=#modul_idname#', '#modul_idname#', '1', 'editcomments', '1', '6', '0', '0');



-- --------------------------------------------------------

-- 
-- Benutzerrechte und Rechte-Kategorien anlegen
-- 

INSERT INTO 01prefix_rights (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,nodelete,hide,in_profile) VALUES
('#modul_idname#', '1', '1', '1', '01article_userrights', 'Benutzerrechte', NULL , '', '', NULL , NULL , '0', '0', '0'),
('#modul_idname#', '0', '1', '1', 'newarticle', 'Neue Artikel verfassen', '', 'Ja|Nein', '1|0', '', '1', '0', '0', '0'),
('#modul_idname#', '0', '1', '2', 'editarticle', 'Artikel bearbeiten', '', 'Nur eigene Artikel bearbeiten|Alle Artikel bearbeiten &amp; freischalten|Kein Zugriff', '1|2|0', '', '1', '0', '0', '0'),
('#modul_idname#', '0', '1', '3', 'staticarticle', 'Statische Seiten erstellen & bearbeiten', '', 'Nur eigene Seiten|Alle Seiten|Kein Zugriff', '1|2|0', '', '0', '0', '0', '0'),
('#modul_idname#', '0', '1', '4', 'freischaltung', 'Freischaltung von Artikeln &amp; Seiten', 'Artikel und statische Seiten dieses Benutzers m&uuml;ssen vor der Ver&ouml;ffentlichung von einem Moderator freigeschaltet werden.', 'Freischaltung n&ouml;tig|Keine Freischaltung n&ouml;tig', '1|0', '', '1', '0', '0', '0'),
('#modul_idname#', '0', '1', '5', 'editcats', 'Kategorien verwalten', '', 'Ja|Nein', '1|0', '', '0', '0', '0', '0');


ALTER TABLE `01prefix_user` ADD `#modul_idname#_newarticle` tinyint( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `01prefix_user` ADD `#modul_idname#_editarticle` tinyint( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `01prefix_user` ADD `#modul_idname#_staticarticle` tinyint( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `01prefix_user` ADD `#modul_idname#_freischaltung` tinyint( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `01prefix_user` ADD `#modul_idname#_editcats` tinyint( 1 ) NOT NULL DEFAULT '0';

-- 
-- Dem Benutzer, der das Modul installiert hat die entsprechenden Rechte zuweisen
-- 

UPDATE `01prefix_user` SET `#modul_idname#_newarticle` = '1' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;
UPDATE `01prefix_user` SET `#modul_idname#_editarticle` = '2' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;
UPDATE `01prefix_user` SET `#modul_idname#_staticarticle` = '1' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;
UPDATE `01prefix_user` SET `#modul_idname#_freischaltung` = '0' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;
UPDATE `01prefix_user` SET `#modul_idname#_editcats` = '1' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `01prefix_article`
-- 

CREATE TABLE `01modulprefix_article` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `utimestamp` int(15) NOT NULL DEFAULT '0',
  `endtime` int(15) DEFAULT '0',
  `frei` tinyint(1),
  `hide` tinyint(1),
  `icon` varchar(25) NULL,
  `titel` varchar(255),
  `newscatid` varchar(255) DEFAULT '0',
  `content` text,
  `autozusammen` tinyint(1) NULL,
  `zusammenfassung` text,
  `comments` tinyint(1),
  `hide_headline` tinyint(1),
  `uid` int(10),
  `static` tinyint(1),
  `top` tinyint(1) NULL,
  `hits` int(10) DEFAULT '0',
  `hide_signature` tinyint(1) DEFAULT '0',
  `serialized_data` mediumblob COMMENT 'use unserialize() to get data back',
  PRIMARY KEY  (`id`),
  FULLTEXT (`titel`,`content`,`zusammenfassung`) 
) ENGINE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `01prefix_article`
-- Dummy-Eintrag
-- 

INSERT INTO `01modulprefix_article` (`id`, `utimestamp`, `endtime`, `frei`, `hide`, `icon`, `titel`, `newscatid`, `content`, `autozusammen`, `zusammenfassung`, `comments`, `hide_headline`, `uid`, `static`, `hits`) VALUES
(1, 1398074400, 0, 1, 0, '', '01-Artikelsystem V3 erfolgreich installiert', '0', '<p><strong><span style="color: #008000;">Vielen Dank, dass Sie sich f&uuml;r das 01-Artikelsystem entschieden haben!</span></strong><br />Diesen ersten Eintrag k&ouml;nnen Sie l&ouml;schen, nachdem Sie sich in den Administrationsbereich eingeloggt haben.</p>\r\n<p>Bei Fragen oder Problemen rund um das <strong>01-Artikelsystem</strong> oder das <strong>01acp</strong> stehe ich Ihnen gerne im <a href="http://board.01-scripts.de/" target="_blank">01-Supportforum</a> oder <a href="http://www.01-scripts.de/contact.php" target="_blank">per E-Mail</a> zu Verf&uuml;gung.</p>\r\n<p>Bitte beachten Sie die <a href="http://www.01-scripts.de/lizenz.php" target="_blank">g&uuml;ltigen Lizenzbestimmungen</a>! Das <strong>01-Artikelsystem</strong> und das <strong>01acp</strong> werden unter der Creative-Commons-Lizenz "<em><a href="http://creativecommons.org/licenses/by-nc-sa/3.0/de/" target="_blank">Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland</a></em>" ver&ouml;ffentlicht.</p>\r\n<p>Informationen zum Erwerb einer <em>Lizenz zur kommerziellen Nutzung</em> (Gestattet den Einsatz auf kommerziellen Seiten und/oder Firmenseiten) oder eine <em>Non-Copyright-Lizenz</em> (die zum Entfernen des sichtbaren Urheberrechts-Hinweises berechtigt) entnehmen Sie bitte <a href="http://www.01-scripts.de/preise.php" target="_blank">dieser Seite</a>.</p>\r\n<p>MfG,<br />Michael Lorer<br />Web: <a href="http://www.01-scripts.de" target="_blank">http://www.01-scripts.de</a><br />Mail: <a href="mailto:info@01-scripts.de">info@01-scripts.de</a></p>\r\n<p><img style="float: left; margin: 10px; border: 0px none #000000;" title="01-Scripts.de" src="http://www.01-scripts.de/pics/system/01logo.jpg" alt="01-Scripts.de-Logo" width="300" height="40" />Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.</p>', 0, '', 1, 0, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `01prefix_category`
-- 

CREATE TABLE `01modulprefix_articlecategory` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50),
  `catpic` varchar(15) NULL,
  `sortid` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

COMMIT;