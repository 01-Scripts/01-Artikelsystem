<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Modulspezifische Funktionen
	#fv.321#
*/

/* SYNTAKTISCHER AUFBAU VON FUNKTIONSNAMEN BEACHTEN!!!
	_ModulName_beliebigerFunktionsname()
	Beispiel: 
	if(!function_exists("_example_TolleFunktion")){
		_example_TolleFunktion($parameter){ ... }
		}
*/

// Funktion wird zentral aufgerufen, wenn ein Benutzer gel�scht wird.
/* @param int $userid			UserID des gel�schten Benutzers
   @param string $username		Username des gel�schten Benutzers
   @param string $mail			E-Mail-Adresse des gel�schten Benutzers
   @return TRUE/FALSE
*/
if(!function_exists("_01article_DeleteUser")){
function _01article_DeleteUser($userid,$username,$mail){
global $mysqli,$mysql_tables;

$mysqli->query("UPDATE ".$mysql_tables['artikel']." SET uid='0' WHERE uid='".$mysqli->escape_string($userid)."'");

return TRUE;
}
}


// Funktion wird zentral aufgerufen, wenn das Modul gel�scht werden soll
/*
RETURN: TRUE
*/
if(!function_exists("_01article_DeleteModul")){
function _01article_DeleteModul(){
global $mysqli,$mysql_tables,$modul;

$modul = $mysqli->escape_string($modul);

// MySQL-Tabellen l�schen
$mysqli->query("DROP TABLE `".$mysql_tables['artikel']."`");
$mysqli->query("DROP TABLE `".$mysql_tables['cats']."`");

// Rechte entfernen
$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_newarticle`");
$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_editarticle`");
$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_staticarticle`");
$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_freischaltung`");
$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` DROP `".$modul."_editcats`");

return TRUE;
}
}


// Userstatistiken holen
/* @param int $userid			UserID, zu der die Infos geholt werden sollen

RETURN: Array(
			statcat[x] 		=> "Statistikbezeichnung f�r Frontend-Ausgabe"
			statvalue[x] 	=> "Auszugebender Wert"
			)
  */
if(!function_exists("_01article_getUserstats")){
function _01article_getUserstats($userid){
global $mysqli,$mysql_tables,$modul,$module;

if(isset($userid) && is_integer(intval($userid))){
	$artmenge = 0;
	list($artmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei = '1' AND hide = '0' AND static = '0' AND uid = '".$mysqli->escape_string($userid)."'")->fetch_array(MYSQLI_NUM);
	
	$ustats[] = array("statcat"	=> "Geschriebene Artikel (".$module[$modul]['instname']."):",
						"statvalue"	=> $artmenge);
	return $ustats;
	}
else
	return false;
}
}


// String des Artikels, Beitrags, Bildes etc. dem der �bergebene IdentifizierungsID zugeordnet ist
/* @param int $postid			Beitrags-ID
   @return string				String mit dem entsprechenden Text
*/
if(!function_exists("_01article_getCommentParentTitle")){
function _01article_getCommentParentTitle($postid){
global $mysqli,$mysql_tables;

$list = $mysqli->query("SELECT titel FROM ".$mysql_tables['artikel']." WHERE id='".$mysqli->escape_string($postid)."' LIMIT 1");
while($row = $list->fetch_assoc()){
	return $row['titel'];
	}
}
}


// $form_data mit �bergebenen Post-Werten f�llen
/* @return array			Array $form_data mit $_POST-Werten
*/
if(!function_exists("_01article_getForm_DataArray")){
function _01article_getForm_DataArray(){
global $_POST;

if(!isset($_POST['autozusammen'])) 		$_POST['autozusammen'] 		= 0;
if(!isset($_POST['comments'])) 			$_POST['comments'] 			= 0;
if(!isset($_POST['top'])) 				$_POST['top'] 				= 0;
if(!isset($_POST['hide_signature'])) 	$_POST['hide_signature'] 	= 0;
if(!isset($_POST['id'])) 				$_POST['id'] 				= 0;

$form_data = array("id"				=> $_POST['id'],
				   "starttime_date"	=> $_POST['starttime_date'],
				   "starttime_uhr"	=> $_POST['starttime_uhr'],
				   "endtime_date" 	=> $_POST['endtime_date'],
				   "endtime_uhr" 	=> $_POST['endtime_uhr'],
				   "titel" 			=> $_POST['titel'],
				   "textfeld"		=> $_POST['textfeld'],
				   "autozusammen" 	=> $_POST['autozusammen'],
				   "zusammenfassung"=> $_POST['zusammenfassung'],
				   "comments" 		=> $_POST['comments'],
				   "top"			=> $_POST['top'],
				   "hide_headline"	=> $_POST['hide_headline'],
				   "hide_signature" => $_POST['hide_signature'],
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


// $form_data mit Werten aus der Datenbank / Standardwerten f�llen
/*
* @param array $row
* @return array

RETURN: Array $input_fields mit Standardvorgabewerten / Werten aus DB
*/
if(!function_exists("_01article_fillForm_DataArray")){
function _01article_fillForm_DataArray($row=""){
global $ser_fields,$htmlent_flags,$htmlent_encoding_acp;

if(is_array($row)){
	$form_data = array("id"				=> $row['id'],
					   "starttime_date"	=> date("d.m.Y",$row['utimestamp']),
					   "starttime_uhr"	=> date("G.i",$row['utimestamp']),
					   "newscat"		=> $row['newscatid'],
					   "titel" 			=> $row['titel'],
					   "textfeld"		=> $row['content'],
					   "autozusammen" 	=> $row['autozusammen'],
					   "zusammenfassung"=> $row['zusammenfassung'],
					   "hide_headline"	=> $row['hide_headline'],
					   "top"			=> $row['top'],
					   "uid"			=> $row['uid'],
					   "hide_signature" => $row['hide_signature']
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
				$form_data['ser_field_'.$x] = htmlspecialchars($return['field_'.$x],$htmlent_flags,$htmlent_encoding_acp);
				}
			}
		}

	}
// Array mit Standardwerten f�llen
else{
$form_data = array("starttime_date"	=> date("d.m.Y"),
				   "starttime_uhr"	=> date("G.i"),
				   "endtime_date" 	=> "",
				   "endtime_uhr"	=> "00.00",
				   "titel" 			=> "",
				   "textfeld"		=> "",
				   "autozusammen" 	=> 0,
				   "zusammenfassung"=> "",
				   "comments" 		=> 0,
				   "top"			=> 0,
				   "hide_headline"	=> 1,
				   "hide_signature" => 0
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


// Ausgabe f�r RSS-Feed. RSS-Header-Daten werden global zur Verf�gung gestellt. Siehe 01example.rss
/* @param string $show			Kommentare oder Artikel anzeigen?
*  @param int $entrynrs			Anzahl Eintr�ge, die angezeigt werden sollen
*  @param string $cats
RETURN: RSS-XML-Daten
*/
if(!function_exists("_01article_RSS")){
function _01article_RSS($show,$entrynrs,$cats){
global $mysqli,$mysql_tables,$settings,$modul,$names,$lang,$server_domainname, $htmlent_flags, $htmlent_encoding_acp;

$rssdata = create_RSSFramework($settings['artikelrsstitel'],$settings['artikelrsstargeturl'],$settings['artikelrssbeschreibung'],TRUE);
$write_text = "";

// LIMIT
if(isset($entrynrs) && is_numeric($entrynrs) && $entrynrs > 0)
	$limit = $mysqli->escape_string(strip_tags($entrynrs));
else
	$limit = $settings['artikelrssanzahl'];	
	
$mod = get_html_translation_table(HTML_ENTITIES);
$mod = array_flip($mod);

// Config for htmLawed-function:
$config['cdata'] 			= 1;
$config['clean_ms_char'] 	= 1;
$config['comment'] 			= 1;
$config['safe'] 			= 1;

// RSS-Feed f�r KOMMENTARE
if(isset($show) && $show == "show_commentrssfeed" && $settings['artikelkommentarfeed'] == 1){
	// Newstitel in Array einlesen (um MySQL-Abfragen zu verringern)
	$list = $mysqli->query("SELECT id,titel FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND utimestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0')");
	while($row = $list->fetch_assoc()){
		$arttitel[$row['id']] = $row['titel'];
		}
		
	$list = $mysqli->query("SELECT postid,utimestamp,autor,message FROM ".$mysql_tables['comments']." WHERE modul='".$modul."' AND frei='1' ORDER BY utimestamp DESC LIMIT ".$mysqli->escape_string($settings['artikelrssanzahl'])."");
	while($row = $list->fetch_assoc()){

		if($settings['modrewrite'] == 1)
			$echolink = _01article_echo_ArticleLink($row['postid'],$arttitel[$row['postid']],$row['utimestamp']);
		elseif(substr_count($settings['artikelrsstargeturl'], "?") < 1)
			$echolink = $settings['artikelrsstargeturl']."?".$names['artid']."=".$row['postid']."#01id".$row['postid'];
		else
			$echolink = str_replace("&","&amp;",$settings['artikelrsstargeturl'])."&amp;".$names['artid']."=".$row['postid']."#01id".$row['postid'];	

		$echotext = str_replace("&","&amp;",$row['message']);
		$echotext = bb_code_comment($echotext,1,1,0);
		$echotext = htmLawed($echotext, $config);
		
		$write_text .= "<item>
  <title>Neuer Kommentar zu ".str_replace("&","&amp;",html_entity_decode($row['titel'], $htmlent_flags, "UTF-8"))."</title>
  <link>".$echolink."</link>
  <description><![CDATA[".$echotext."]]></description>
  <author>".str_replace("&","&amp;",utf8_encode($row['autor']))."</author>
  <pubDate>".date("r",$row['utimestamp'])."</pubDate>
  <guid>".$echolink."</guid>
</item>
";
		}
	$return = $rssdata['header'].$write_text.$rssdata['footer'];
	}
// RSS-Feed f�r ARTIKEL
elseif($settings['artikelrssfeedaktiv'] == 1){
	$config['elements'] = "*+iframe";

	if(isset($cats) && !empty($cats) && substr_count($cats, ",") >= 1){
		$cats_array = explode(",",$cats);
				   
		$add2query_cat = " 1=2 ";
		foreach($cats_array as $value){
			$add2query_cat .= " OR newscatid LIKE '%,".$mysqli->escape_string($value).",%' ";
			}
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND utimestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') AND (".$add2query_cat.") ORDER BY utimestamp DESC LIMIT ".$limit."";
		}
	elseif(isset($cats) && !empty($cats))
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND utimestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') AND newscatid LIKE '%,".$mysqli->escape_string($cats).",%' ORDER BY utimestamp DESC LIMIT ".$limit."";
	else
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND utimestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') ORDER BY utimestamp DESC LIMIT ".$limit."";
	
	$list = $mysqli->query($query);
	while($row = $list->fetch_assoc()){

		if($settings['modrewrite'] == 1)
			$echolink = _01article_echo_ArticleLink($row['id'],$row['titel'],$row['utimestamp']);
		elseif(substr_count($settings['artikelrsstargeturl'], "?") < 1)
			$echolink = $settings['artikelrsstargeturl']."?".$names['artid']."=".$row['id']."#01id".$row['id'];
		else
			$echolink = str_replace("&","&amp;",$settings['artikelrsstargeturl'])."&amp;".$names['artid']."=".$row['id']."#01id".$row['id'];

		// Inhalt parsen
		if($settings['artikelrsslaenge'] == "short"){
			// Zusammenfassung only:
			if($row['autozusammen'] == 0 && !empty($row['zusammenfassung']))
				$echotext = htmLawed($row['zusammenfassung'], $config);
			else
				$echotext = substr(htmLawed($row['content'], $config),0,$settings['artikeleinleitungslaenge']);
				
			$echotext .= $lang['weiterlesen'];
			}
		else{
			// kompletter Text
			$echotext = htmLawed($row['content'], $config);
			}

		// Pfade anpassen
		$echotext = str_replace("../01pics/",$settings['absolut_url']."01pics/",$echotext);
		$echotext = str_replace("../01files/",$settings['absolut_url']."01files/",$echotext);
		$echotext = utf8_encode($echotext);
		
		$username_array 	= getUserdatafields($row['uid'],"username,01acp_signatur");
		$username 			= $username_array['username'];
		$signatur 			= "<p>".nl2br(str_replace("&","&amp;",$username_array['signatur']))."</p>";
		
		$write_text .= "<item>
  <title>".str_replace("&","&amp;",html_entity_decode($row['titel'], $htmlent_flags, "UTF-8"))."</title>
  <link>".$echolink."</link>
  <description><![CDATA[".$echotext.$signatur."]]></description>
  <author>".utf8_encode($username)."</author>
  <pubDate>".date("r",$row['utimestamp'])."</pubDate>
  <guid>".$echolink."</guid>
</item>
";
		}
		
	$return = $rssdata['header'].$write_text.$rssdata['footer'];
	}
else{
	$return = $rssdata['header']."<item>Fehler: Der RSS-Feed wurde deaktiviert</item>".$rssdata['footer'];
	}

return $return;
}
}


// Dropdown-Box aus angelegten Kategorien generieren (ohne Select-Tag)
/* @param array $plain_data		Optionaler Parameter. Enth�lt danach einen Array mit den Cat-Namen
 * @return string				Option-Elemente f�r Select-Formularelement
*/
if(!function_exists("_01article_CatDropDown")){
function _01article_CatDropDown(&$plain_data){
global $mysqli,$mysql_tables;

$plain_data = array();

$list = $mysqli->query("SELECT id,name FROM ".$mysql_tables['cats']." ORDER BY sortid,name");
while($row = $list->fetch_assoc()){
	$plain_data[$row['id']] = $row['name'];
	$return .= "<option value=\"".$row['id']."\">".$row['name']."</option>\n";
	}
	
return $return;
}
}


// Sortierungs-Dropdown (Kategorien) generieren
/* @param int $selected			Vorselektierten Wert ggf. �bergeben
   @return string				<option>-Tags
  */
if(!function_exists("_01article_CatSortDropDown")){
function _01article_CatSortDropDown($selected=1){
global $mysqli,$mysql_tables;

$return = "";
$catmenge = 0;
list($catmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['cats']."")->fetch_array(MYSQLI_NUM);

for($x=1;$x<=$catmenge;$x++){
	if($x == $selected) $return .= "<option selected=\"selected\">".$x."</option>\n";
	else $return .= "<option>".$x."</option>\n";
	}

return $return;
}
}


// Add2Query-String f�r Kategorien generieren
/* @params string $catids			Kommaseparierter CatID-String

RETURN: MySQL-Query zum Einf�gen in ... WHERE ... AND ($add2query) ...
  */
if(!function_exists("_01article_CreateCatQuery")){
function _01article_CreateCatQuery($catids=NULL){
global $mysqli;

$add2query_cat = " 1=1 ";

if($catids != NULL){
	if(substr_count($catids, ",") >= 1){
		$cats_array = explode(",",$catids);

		$add2query_cat = " 1=2 ";
		foreach($cats_array as $value){
			$add2query_cat .= " OR newscatid LIKE '%,".$mysqli->escape_string($value).",%' ";
			}
		}
	else
		$add2query_cat = " newscatid LIKE '%,".$mysqli->escape_string($catids).",%' ";
	}
return $add2query_cat;
}
}


// Artikellink als mod_rewrite oder ohne entsprechend generieren und ausgeben
/* @params string	$artid				ArtikelID
 * @params string 	$arttitle			Artikelname (optional; wenn = "" --> wird aus DB geholt)
 * @params int		$timestamp			Datums-Timestamp
 * @params string	$domain				Domain (optional)

RETURN: Entsprechend (mod_rewrite) formatierter Link an den weitere Parameter angeh�ngt werden k�nnen
  */
if(!function_exists("_01article_echo_ArticleLink")){
function _01article_echo_ArticleLink($artid,$arttitle="",$timestamp="",$domain=""){
global $settings,$names,$server_domainname,$filename,$module,$modul;

$artid = strip_tags($artid);

if($settings['modrewrite'] == 1){
	if(empty($artid) || $artid == 0){
		return $filename;
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
			
		return "http://".$domain."/".$adddate._01article_parseMod_rewriteLinks($arttitle).",".$module[$modul]['nr'].",".$artid.".html";
		}
	}
else
	return addParameter2Link($filename,$names['artid']."=".$artid);

}
}


// Artikelnamen aus DB holen
/* @params string $artid			ArtikelID
 * @return string					Artikel-Titel
*/
if(!function_exists("_01article_getArtTitle")){
function _01article_getArtTitle($artid){
global $mysqli,$mysql_tables;

if(is_numeric($artid) && $artid != 0 && !empty($artid)){
	$list = $mysqli->query("SELECT titel FROM ".$mysql_tables['artikel']." WHERE id = '".$mysqli->escape_string($artid)."'");
	$row = $list->fetch_assoc();
	
	return $row['titel'];
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
$string = str_replace("�","ae",$string);
$string = str_replace("�","oe",$string);
$string = str_replace("�","ue",$string);
$string = str_replace("&auml;","ae",$string);
$string = str_replace("&ouml;","oe",$string);
$string = str_replace("&uuml;","ue",$string);
$string = str_replace("&amp;","und",$string);
$string = str_replace(" ","-",$string);
$string = str_replace("/","",$string);
$string = str_replace("\\","",$string);
$string = str_replace(",","_",$string);
$string = str_replace("�","ss",$string);
$string = rawurlencode($string);

return $string;

}
}


// Callback-Funktion zur Ausgabe von Galeriebild-Thumbnails via preg_replace_callback
/* @params string $treffer			Zu parsender Galerie-Bild-String
 * @return string					<div>-Box mit Thumbnails der Galerie-Bilder
*/
if(!function_exists("_01article_callback_GetGalThumbs4Article")){
function _01article_callback_GetGalThumbs4Article($treffer){
global $moduldir,$settings,$mysql_tables,$art2gal_galnr,$mysqli,$module,$instnr,$flag_utf8;
$return = "";

// $treffer[0]: gesamter String {Insert#...GalleryPicsFrom#...}
// $treffer[1]: 1. Match (Anzahl an auszugebenden Thumbnails)
// $treffer[2]: 2. Match (Galid)
if(isset($treffer) && is_array($treffer) && is_numeric($treffer[1]) && is_numeric($treffer[2])){
    $galmodule = array();
    $galmodule = getModuls($module,"01gallery");

    foreach($galmodule as $gm){
    	if($gm['nr'] == $art2gal_galnr)
    		$modul = $gm['idname'];
    }

    if(!empty($modul)){
        @include($moduldir.$modul."/_headinclude.php");
        @include($moduldir.$modul."/_functions.php");
        
        // DB: Einstellungen in Array $settings[] einlesen
        $list = $mysqli->query("SELECT idname,wert FROM ".$mysql_tables['settings']." WHERE is_cat = '0' AND modul = '".$mysqli->escape_string($modul)."'");
        while($row = $list->fetch_assoc()){
        	$settings[$row['idname']] = $row['wert'];
        	}
        
        $galdir = $moduldir.$modul."/".$galdir;
        $galid  = $treffer[2];
        
        // Galerie-Infos aus Datenbank holen
    	$list = $mysqli->query("SELECT id,galtimestamp,galpassword,galeriename,beschreibung,galpic,anzahl_pics FROM ".$mysql_tables['gallery']." WHERE id = '".$mysqli->escape_string($galid)."' AND hide='0' LIMIT 1");
	    if($list->num_rows == 0)
            return "";
    	$galinfo = $list->fetch_assoc();
    	$galverz = $galdir._01gallery_getGalDir($galinfo['id'],$galinfo['galpassword'])."/";
        
	    // Thumbnails auflisten
	    $query = "SELECT id,filename,title,pictext FROM ".$mysql_tables['pics']." WHERE galid = '".$mysqli->escape_string($galid)."' ORDER BY sortorder DESC LIMIT ".$mysqli->escape_string($treffer[1]);

		$return .= "\n\n<div class=\"cssgallery_art2gal\">\n";
	    $list = $mysqli->query($query);
	    if($list->num_rows == 0)
            return "";
            
		while($pics = $list->fetch_assoc()){
			if($flag_utf8)
				$descr = utf8_encode(strip_tags($pics['title'])." - ".strip_tags($pics['pictext']));
			else
				$descr = strip_tags($pics['title'])." - ".strip_tags($pics['pictext']);
			
			if($settings['artikellightbox'] == 1)
				$return .= "<div class=\"thumbnail_art2gal\"><a href=\"".$galverz.$pics['filename']."\" class=\"lightbox\" rel=\"lightbox-art2gal".$galid."set\" title=\"".$descr."\">"._01gallery_getThumb($galverz,$pics['filename'],"_tb",FALSE,$descr)."</a></div>\n";
			else
				$return .= "<div class=\"thumbnail_art2gal\">"._01gallery_getThumb($galverz,$pics['filename'],"_tb",FALSE,$descr)."</div>\n";
			}
		$return .= "</div><br style=\"clear: both;\">\n\n"; 
        }
    }

return $return;
}
}

?>