<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2011 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Modulspezifische Funktionen
	#fv.310#
*/

/* SYNTAKTISCHER AUFBAU VON FUNKTIONSNAMEN BEACHTEN!!!
	_ModulName_beliebigerFunktionsname()
	Beispiel: 
	if(!function_exists("_example_TolleFunktion")){
		_example_TolleFunktion($parameter){ ... }
		}
*/

// Globale Funktionen - nötig!

// Funktion wird zentral aufgerufen, wenn ein Benutzer gelöscht wird.
/* @param int $userid			UserID des gelöschten Benutzers
   @param string $username		Username des gelöschten Benutzers
   @param string $mail			E-Mail-Adresse des gelöschten Benutzers
   @return TRUE/FALSE
*/
if(!function_exists("_01article_DeleteUser")){
function _01article_DeleteUser($userid,$username,$mail){
global $mysql_tables;

mysql_query("UPDATE ".$mysql_tables['artikel']." SET uid='0' WHERE uid='".mysql_real_escape_string($userid)."'");

return TRUE;
}
}

// Funktion wird zentral aufgerufen, wenn das Modul gelöscht werden soll
/*
RETURN: TRUE
*/
if(!function_exists("_01article_DeleteModul")){
function _01article_DeleteModul(){
global $mysql_tables,$modul;

$modul = mysql_real_escape_string($modul);

// MySQL-Tabellen löschen
mysql_query("DROP TABLE `".$mysql_tables['artikel']."`");
mysql_query("DROP TABLE `".$mysql_tables['cats']."`");

// Rechte entfernen
mysql_query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_newarticle`");
mysql_query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_editarticle`");
mysql_query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_staticarticle`");
mysql_query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_freischaltung`");
mysql_query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_editcats`");

return TRUE;
}
}







// String des Artikels, Beitrags, Bildes etc. dem der übergebene IdentifizierungsID zugeordnet ist
/* @param int $postid			Beitrags-ID
   @return string				String mit dem entsprechenden Text
*/
if(!function_exists("_01article_getCommentParentTitle")){
function _01article_getCommentParentTitle($postid){
global $mysql_tables;

//return "SELECT titel FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($postid)."' LIMIT 1";
$list = mysql_query("SELECT titel FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($postid)."' LIMIT 1");
while($row = mysql_fetch_assoc($list)){
	return stripslashes($row['titel']);
	}
}
}







// $form_data mit übergebenen Post-Werten füllen
/* @return array			Array $form_data mit $_POST-Werten
*/
if(!function_exists("_01article_getForm_DataArray")){
function _01article_getForm_DataArray(){
global $_POST;

$form_data = array("id"				=> $_POST['id'],
				   "starttime_date"	=> $_POST['starttime_date'],
				   "starttime_uhr"	=> $_POST['starttime_uhr'],
				   "endtime_date" 	=> $_POST['endtime_date'],
				   "endtime_uhr" 	=> $_POST['endtime_uhr'],
				   "icon" 			=> $_POST['icon'],
				   "titel" 			=> stripslashes($_POST['titel']),
				   "textfeld"		=> stripslashes($_POST['textfeld']),
				   "autozusammen" 	=> $_POST['autozusammen'],
				   "zusammenfassung"=> stripslashes($_POST['zusammenfassung']),
				   "comments" 		=> $_POST['comments'],
				   "top"			=> $_POST['top'],
				   "hide_headline"	=> $_POST['hide_headline'],
				   "uid"			=> $_POST['uid'],
				   "autor"			=> $_POST['autor']
				  );
if(is_array($_POST['newscat']))
	$form_data['newscat'] = implode(",",$_POST['newscat']);
else
	$form_data['newscat'] = "";

return $form_data;
}
}







// $form_data mit Werten aus der Datenbank / Standardwerten füllen
/*
* @param array $row
* @return array

RETURN: Array $input_fields mit Standardvorgabewerten / Werten aus DB
*/
if(!function_exists("_01article_fillForm_DataArray")){
function _01article_fillForm_DataArray($row=""){
global $ser_fields;

if(is_array($row)){
	$form_data = array("id"				=> $row['id'],
					   "starttime_date"	=> date("d.m.Y",$row['timestamp']),
					   "starttime_uhr"	=> date("G.i",$row['timestamp']),
					   "newscat"		=> $row['newscatid'],
					   "icon" 			=> $row['icon'],
					   "titel" 			=> stripslashes($row['titel']),
					   "textfeld"		=> stripslashes($row['text']),
					   "autozusammen" 	=> $row['autozusammen'],
					   "zusammenfassung"=> stripslashes($row['zusammenfassung']),
					   "hide_headline"	=> $row['hide_headline'],
					   "top"			=> $row['top'],
					   "uid"			=> $row['uid']
					  );
	
	if($row['comments'] == 1) $form_data['comments'] = 0;
	else $form_data['comments'] = 1;
	
	if($row['endtime'] > 0){
		$form_data['endtime_date'] = date("d.m.Y",$row['endtime']);
		$form_data['endtime_uhr'] = date("G.i",$row['endtime']);
		}
	else{
		$form_data['endtime_date'] = "";
		$form_data['endtime_uhr'] = "00.00";
		}
		
	// Get serialized data
	if($ser_fields){
		$return = unserialize($row['serialized_data']);

		if(empty($return)){
			for($x=1;$x<=ANZ_SER_FIELDS;$x++){
				$form_data['ser_field_'.$x] = "";
				}
			}
		else{
			for($x=1;$x<=ANZ_SER_FIELDS;$x++){
				$form_data['ser_field_'.$x] = htmlspecialchars(stripslashes($return['field_'.$x]));
				}
			}
		}

	}
// Array mit Standardwerten füllen
else{
$form_data = array("starttime_date"	=> date("d.m.Y"),
				   "starttime_uhr"	=> date("G.i"),
				   "endtime_date" 	=> "",
				   "endtime_uhr"	=> "00.00",
				   "icon" 			=> "",
				   "titel" 			=> "",
				   "textfeld"		=> "",
				   "autozusammen" 	=> 0,
				   "zusammenfassung"=> "",
				   "comments" 		=> 0,
				   "top"			=> 0,
				   "hide_headline"	=> 1
				  );

	if($ser_fields){
		for($x=1;$x<=ANZ_SER_FIELDS;$x++){
			$form_data['ser_field_'.$x] = "";
			}
		}
	}

return $form_data;
}
}







// Ausgabe für RSS-Feed. RSS-Header-Daten werden global zur Verfügung gestellt. Siehe 01example.rss
/* @param string $show			Kommentare oder Artikel anzeigen?
*  @param int $entrynrs			Anzahl Einträge, die angezeigt werden sollen
*  @param string $cats
RETURN: RSS-XML-Daten
*/
if(!function_exists("_01article_RSS")){
function _01article_RSS($show,$entrynrs,$cats){
global $mysql_tables,$settings,$modul,$names,$lang,$server_domainname;

$rssdata = create_RSSFramework($settings['artikelrsstitel'],$settings['artikelrsstargeturl'],$settings['artikelrssbeschreibung']);
$write_text = "";

// LIMIT
if(isset($entrynrs) && is_numeric($entrynrs) && $entrynrs > 0)
	$limit = mysql_real_escape_string($entrynrs);
else
	$limit = $settings['artikelrssanzahl'];

	
$mod = get_html_translation_table(HTML_ENTITIES);
$mod = array_flip($mod);

// RSS-Feed für KOMMENTARE
if(isset($show) && $show == "show_commentrssfeed" && $settings['artikelkommentarfeed'] == 1){
	// Newstitel in Array einlesen (um MySQL-Abfragen zu verringern)
	$list = mysql_query("SELECT id,titel FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0')");
	while($row = mysql_fetch_assoc($list)){
		$arttitel[$row['id']] = stripslashes($row['titel']);
		}
		
	$list = mysql_query("SELECT postid,timestamp,autor,comment FROM ".$mysql_tables['comments']." WHERE modul='".$modul."' AND frei='1' ORDER BY timestamp DESC LIMIT ".mysql_real_escape_string($settings['artikelrssanzahl'])."");
	while($row = mysql_fetch_assoc($list)){

		if($settings['modrewrite'] == 1)
			$echolink = _01article_echo_ArticleLink($row['postid'],$arttitel[$row['postid']],$row['timestamp']);
		elseif(substr_count($settings['artikelrsstargeturl'], "?") < 1)
			$echolink = $settings['artikelrsstargeturl']."?".$names['artid']."=".$row['postid']."#01id".$row['postid'];
		else
			$echolink = str_replace("&","&amp;",$settings['artikelrsstargeturl'])."&amp;".$names['artid']."=".$row['postid']."#01id".$row['postid'];	

		$echotext = stripslashes(str_replace("&","&amp;",$row['comment']));
		$echotext = bb_code_comment($echotext,1,1,0);
		
		$write_text .= "<item>
  <title>Neuer Kommentar zu ".str_replace("&","&amp;",strtr($arttitel[$row['postid']],$mod))."</title>
  <link>".$echolink."</link>
  <description><![CDATA[".$echotext."]]></description>
  <author>".stripslashes(str_replace("&","&amp;",$row['autor']))."</author>
  <pubDate>".date("r",$row['timestamp'])."</pubDate>
  <guid>".$echolink."</guid>
</item>
";
		}
	$return = $rssdata['header'].$write_text.$rssdata['footer'];
	}
// RSS-Feed für ARTIKEL
elseif($settings['artikelrssfeedaktiv'] == 1){
	if(isset($cats) && !empty($cats) && substr_count($cats, ",") >= 1){
		$cats_array = explode(",",$cats);
				   
		$add2query_cat = " 1=2 ";
		foreach($cats_array as $value){
			$add2query_cat .= " OR newscatid LIKE '%,".mysql_real_escape_string($value).",%' ";
			}
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') AND (".$add2query_cat.") ORDER BY timestamp DESC LIMIT ".$limit."";
		}
	elseif(isset($cats) && !empty($cats))
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') AND newscatid LIKE '%,".mysql_real_escape_string($cats).",%' ORDER BY timestamp DESC LIMIT ".$limit."";
	else
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') ORDER BY timestamp DESC LIMIT ".$limit."";
	
	$list = mysql_query($query);
	while($row = mysql_fetch_assoc($list)){

		if($settings['modrewrite'] == 1)
			$echolink = _01article_echo_ArticleLink($row['id'],stripslashes($row['titel']),$row['timestamp']);
		elseif(substr_count($settings['artikelrsstargeturl'], "?") < 1)
			$echolink = $settings['artikelrsstargeturl']."?".$names['artid']."=".$row['id']."#01id".$row['id'];
		else
			$echolink = str_replace("&","&amp;",$settings['artikelrsstargeturl'])."&amp;".$names['artid']."=".$row['id']."#01id".$row['id'];
	
		// Inhalt parsen
		if($settings['artikelrsslaenge'] == "short"){
			// Zusammenfassung only:
			if($row['autozusammen'] == 0 && !empty($row['zusammenfassung']))
				$echotext = stripslashes($row['zusammenfassung']);
			else
				$echotext = stripslashes(substr($row['text'],0,$settings['artikeleinleitungslaenge']));
				
			$echotext .= $lang['weiterlesen'];
			}
		else{
			// kompletter Text
			$echotext = stripslashes($row['text']);
			}

		// Pfade anpassen
		$echotext = str_replace("../01pics/",$settings['absolut_url']."01pics/",$echotext);
		$echotext = str_replace("../01files/",$settings['absolut_url']."01files/",$echotext);
		
		$username_array 	= getUserdatafields($row['uid'],"username,01acp_signatur");
		$username 			= stripslashes($username_array['username']);
		$signatur 			= "<p>".nl2br(stripslashes(str_replace("&","&amp;",$username_array['signatur'])))."</p>";
		
		$write_text .= "<item>
  <title>".str_replace("&","&amp;",strtr(stripslashes($row['titel']),$mod))."</title>
  <link>".$echolink."</link>
  <description><![CDATA[".$echotext.$signatur."]]></description>
  <author>".$username."</author>
  <pubDate>".date("r",$row['timestamp'])."</pubDate>
  <guid>".$echolink."</guid>
</item>
";
		}
		
	$return = $rssdata['header'].$write_text.$rssdata['footer'];
	}
else{
	$return = $rssdata['header']."<item>Fehler: der RSS-Feed wurde deaktiviert</item>".$rssdata['footer'];
	}

return $return;
}
}








// Dropdown-Box aus angelegten Kategorien generieren (ohne Select-Tag)
/* @return string				Option-Elemente für Select-Formularelement
*/
if(!function_exists("_01article_CatDropDown")){
function _01article_CatDropDown(){
global $mysql_tables;

$list = mysql_query("SELECT id,name FROM ".$mysql_tables['cats']." ORDER BY sortid,name");
while($row = mysql_fetch_assoc($list)){
	$return .= "<option value=\"".$row['id']."\">".stripslashes($row['name'])."</option>\n";
	}
	
return $return;
}
}








// Sortierungs-Dropdown (Kategorien) generieren
/* @param int $selected			Vorselektierten Wert ggf. übergeben
   @return string				<option>-Tags
  */
if(!function_exists("_01article_CatSortDropDown")){
function _01article_CatSortDropDown($selected=1){
global $mysql_tables;

$return = "";
$catmenge = 0;
list($catmenge) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['cats'].""));

for($x=1;$x<=$catmenge;$x++){
	if($x == $selected) $return .= "<option selected=\"selected\">".$x."</option>\n";
	else $return .= "<option>".$x."</option>\n";
	}

return $return;
}
}







// Aus CSS-Eigenschaften aus der DB eine CSS-Datei schreiben / cachen
/* @param string $zieldatei
   @return true
*/
if(!function_exists("_01article_CreateCSSCache")){
function _01article_CreateCSSCache($zieldatei){
global $settings;

$cachefile = fopen($zieldatei,"w");
$wrotez = fwrite($cachefile, $settings['csscode']);
fclose($cachefile);

return TRUE;
}
}







// Userstatistiken holen
/* @param int $userid			UserID, zu der die Infos geholt werden sollen

RETURN: Array(
			statcat[x] 		=> "Statistikbezeichnung für Frontend-Ausgabe"
			statvalue[x] 	=> "Auszugebender Wert"
			)
  */
if(!function_exists("_01article_getUserstats")){
function _01article_getUserstats($userid){
global $mysql_tables,$modul,$module;

if(isset($userid) && is_integer(intval($userid))){
	$artmenge = 0;
	list($artmenge) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei = '1' AND hide = '0' AND static = '0' AND uid = '".mysql_real_escape_string($userid)."'"));
	
	$ustats[] = array("statcat"	=> "Geschriebene Artikel (".$module[$modul]['instname']."):",
						"statvalue"	=> $artmenge);
	return $ustats;
	}
else
	return false;
}
}








// Add2Query-String für Kategorien generieren
/* @params string $catids			Kommaseparierter CatID-String

RETURN: MySQL-Query zum Einfügen in ... WHERE ... AND ($add2query) ...
  */
if(!function_exists("_01article_CreateCatQuery")){
function _01article_CreateCatQuery($catids=NULL){
$add2query_cat = " 1=1 ";

if($catids != NULL){
	if(substr_count($catids, ",") >= 1){
		$cats_array = explode(",",$catids);

		$add2query_cat = " 1=2 ";
		foreach($cats_array as $value){
			$add2query_cat .= " OR newscatid LIKE '%,".mysql_real_escape_string($value).",%' ";
			}
		}
	else
		$add2query_cat = " newscatid LIKE '%,".mysql_real_escape_string($catids).",%' ";
	}
return $add2query_cat;
}
}








// Artikellink als mod_rewrite oder ohne entsprechend generieren und ausgeben
/* @params string	$artid				ArtikelID
 * @params string 	$arttitle			Artikelname (optional; wenn = "" --> wird aus DB geholt)
 * @params int		$timestamp			Datums-Timestamp
 * @params string	$domain				Domain (optional)

RETURN: Entsprechend (mod_rewrite) formatierter Link an den weitere Parameter angehängt werden können
  */
if(!function_exists("_01article_echo_ArticleLink")){
function _01article_echo_ArticleLink($artid,$arttitle="",$timestamp="",$domain=""){
global $mysql_tables,$settings,$names,$server_domainname;

if($settings['modrewrite'] == 1){
	if(empty($artid) || $artid == 0){
		return $_SERVER['PHP_SELF'];
		}
	else{
		if(empty($domain)) $domain = $server_domainname;
		
		// ggf. Artikeltitel holen
		if($arttitle == "")
			$arttitle = _01article_getArtTitle($artid);
			
		// Timestamp vorhanden & verwenden?
		/*
		if(!empty($timestamp) && is_numeric($timestamp) && $timestamp > 0)
		    $adddate = date("Y/m/d/",$timestamp);
		else*/ $adddate = "";
			
		return "http://".$domain."/".$adddate._01article_parseMod_rewriteLinks($arttitle).",".$artid.".html";
		}
	}
else
	return addParameter2Link($_SERVER['PHP_SELF'],$names['artid']."=".$artid);

}
}








// Artikelnamen aus DB holen
/* @params string $artid			ArtikelID
 * @return string					Artikel-Titel
*/
if(!function_exists("_01article_getArtTitle")){
function _01article_getArtTitle($artid){
global $mysql_tables;

if(is_numeric($artid) && $artid != 0 && !empty($artid)){
	$list = mysql_query("SELECT titel FROM ".$mysql_tables['artikel']." WHERE id = '".mysql_real_escape_string($artid)."'");
	$row = mysql_fetch_assoc($list);
	
	return stripslashes($row['titel']);
	}
else return "";
}
}








// Artikelnamen aus DB holen
/* @params string $string			Zu parsender Link-String
 * @return string					geparstert Link-String
*/
if(!function_exists("_01article_parseMod_rewriteLinks")){
function _01article_parseMod_rewriteLinks($string){

$string = strtolower($string);
$string = str_replace("ä","ae",$string);
$string = str_replace("ö","oe",$string);
$string = str_replace("ü","ue",$string);
$string = str_replace("&auml;","ae",$string);
$string = str_replace("&ouml;","oe",$string);
$string = str_replace("&uuml;","ue",$string);
$string = str_replace("&amp;","und",$string);
$string = str_replace(" ","-",$string);
$string = str_replace(",","_",$string);
$string = str_replace("ß","ss",$string);
$string = rawurlencode($string);

return $string;

}
}

?>