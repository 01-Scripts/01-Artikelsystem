<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2011 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Frontend-Ausgabe 01-Artikelsystem
	#fv.310#
*/

//Hinweis zum Einbinden des Artikelsystems per include();
/*Folgender PHP-Code n�tig:

<?PHP
$subfolder 		= "01scripts/";
$modul			= "01article/";

include($subfolder."01module/".$modul."01article.php");
?>

*/

$frontp = 1;
$flag_acp = FALSE;
$flag_archiv = "";
if(!isset($flag_utf8))		$flag_utf8 = FALSE;
if(!isset($flag_nocss))		$flag_nocss = FALSE;
if(!isset($flag_second))	$flag_second = FALSE;

@ini_set('session.use_trans_sid',0);

if(isset($subfolder) && !empty($subfolder)){
    if(substr_count($subfolder, "/") < 1){ $subfolder .= "/"; }
	}
elseif(isset($_GET['rss']) && ($_GET['rss'] == "show_rssfeed" || $_GET['rss'] == "show_commentrssfeed"))
   $subfolder = "../../";
else
	$subfolder = "";

// Globale Config-Datei einbinden
include_once($subfolder."01_config.php");
include_once($subfolder."01acp/system/headinclude.php");
if(!$flag_second) include_once($subfolder."01acp/system/functions.php");

$modulvz = $modul."/";
// Modul-Config-Dateien einbinden
include_once($moduldir.$modulvz."_headinclude.php");
include_once($moduldir.$modulvz."_functions.php");

// Variablen
$iconpf 	= $moduldir.$modulvz.$iconpf;			// Verzeichnis mit Icon-Dateien
$tempdir	= $moduldir.$modulvz.$tempdir;			// Template-Verzeichnis

// Language-File einbinden
include_once($tempdir."lang_vars.php");

$filename = $_SERVER['PHP_SELF'];
$sites = 0;
$qt = 0;
$svl2a = "";
$z = false;

// ggf. Zuweisung $show[] -> $_GET[]
if(isset($_REQUEST[$names['catid']]) && isset($show['catid']) && !empty($show['catid']) && $show['catid'] == $_REQUEST[$names['catid']]) $z = true;
if(!isset($_REQUEST[$names['catid']]) && isset($show['catid']) && !empty($show['catid'])) $_REQUEST[$names['catid']] = $show['catid']; 
if(!isset($_REQUEST[$names['search']]) && isset($show['search']) && !empty($show['search'])) $_REQUEST[$names['search']] = $show['search'];
if(!isset($_REQUEST[$names['page']]) && isset($show['page']) && !empty($show['page'])) $_REQUEST[$names['page']] = $show['page'];
if(!isset($_GET[$names['artid']]) && isset($show['artid']) && !empty($show['artid'])) $_GET[$names['artid']] = $show['artid'];

// Notice: Undefined index: ... beheben & strip_tags auf �bergebene Werte anwenden (Anti-XSS)
if(!isset($_REQUEST[$names['search']])) $_REQUEST[$names['search']] = "";
else $_REQUEST[$names['search']]		= strip_tags($_REQUEST[$names['search']]);
if(!isset($_REQUEST[$names['page']])) 	$_REQUEST[$names['page']] 	= "";
else $_REQUEST[$names['page']]			= strip_tags($_REQUEST[$names['page']]);
if(!isset($_REQUEST[$names['catid']])) 	$_REQUEST[$names['catid']] 	= "";
else $_REQUEST[$names['catid']]			= strip_tags($_REQUEST[$names['catid']]);
if(!isset($_REQUEST[$names['cpage']])) 	$_REQUEST[$names['cpage']] 	= "";
else $_REQUEST[$names['cpage']]			= strip_tags($_REQUEST[$names['cpage']]);
if(!isset($_REQUEST[$names['rss']])) 	$_REQUEST[$names['rss']] 	= "";
else $_REQUEST[$names['rss']]			= strip_tags($_REQUEST[$names['rss']]);
if(!isset($_GET[$names['artid']])) 		$_GET[$names['artid']] 		= "";
else $_GET[$names['artid']]				= strip_tags($_GET[$names['artid']]);
if(!isset($_GET[$names['cpage']])) 		$_GET[$names['cpage']] 		= "";
else $_GET[$names['cpage']]				= strip_tags($_GET[$names['cpage']]);
if(!isset($_GET[$names['rss']])) 		$_GET[$names['rss']] 		= "";
else $_GET[$names['rss']]				= strip_tags($_GET[$names['rss']]);
if(!isset($static))						$static						= 0;
if(!isset($_POST['deaktiv_bbc']))		$_POST['deaktiv_bbc']		= 0;
else $_POST['deaktiv_bbc']				= strip_tags($_POST['deaktiv_bbc']);
if(!isset($_GET[$names['page']]) || isset($_GET[$names['page']]) && $_GET[$names['page']] != $_REQUEST[$names['page']]) $_GET[$names['page']] = $_REQUEST[$names['page']];  

//Link-String generieren
$system_link 		= addParameter2Link(_01article_echo_ArticleLink($_GET[$names['artid']]),$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]);
$system_link_index 	= parse_cleanerlinks(addParameter2Link($filename,$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]));
$system_link_rss	= $subfolder."01module/".$modul."/01article.php";





// Ausf�hrung der weiteren Seite wird nach dem Aufruf der n�tigen RSS-Funktionen durch exit; unterbrochen
if(isset($_GET[$names['rss']]) && ($_GET[$names['rss']] == "show_rssfeed" || $_GET[$names['rss']] == "show_commentrssfeed") && 
   $settings['rss_aktiv'] == 1 && ($settings['artikelrssfeedaktiv'] == 1 || $settings['artikelkommentarfeed'] == 1)){
	
	if(!isset($_GET['entries'])) $_GET['entries'] = "";
	else $_GET['entries'] = strip_tags($_GET['entries']);
	
	header("Content-type: text/xml");
	echo _01article_RSS($_GET[$names['rss']],$_GET['entries'],$_REQUEST[$names['catid']]);
	mysql_close();
	exit;
	}
elseif(isset($_GET[$names['rss']]) && ($_GET[$names['rss']] == "show_rssfeed" || $_GET[$names['rss']] == "show_commentrssfeed")){
	echo "Fehler: Der RSS-Feed wurde deaktiviert!";
	exit;
	}
// Ausf�hrung der weiteren Seite wird nach dem Aufruf der n�tigen RSS-Funktionen durch exit; unterbrochen


// externe CSS-Datei / CSS-Eigenschaften?
if(isset($settings['extern_css']) && !empty($settings['extern_css']) && $settings['extern_css'] != "http://" && !$flag_nocss)
	$echo_css = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$settings['extern_css']."\" />";
elseif(isset($settings['csscode']) && !empty($settings['csscode']) && !$flag_nocss)
	$echo_css = "<style type=\"text/css\">
".$settings['csscode']."
</style>";
else $echo_css = "";

// Head 2 einf�gen
include($tempdir."main_top.html");



// ggf. Archiv-Zeit berechnen
if(!empty($settings['archiv_time']) && $settings['archiv_time'] > 0 && is_numeric($settings['archiv_time']))
	$qt = time()-($settings['archiv_time']*24*60*60);

// Alternative Abfrage, wenn nur ein Artikel / eine Seite angezeigt werden soll
if(isset($_GET[$names['artid']]) && !empty($_GET[$names['artid']]) && $_GET[$names['artid']] != "archiv" && $_GET[$names['artid']] > 0 && is_numeric($_GET[$names['artid']])){
    $query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') AND id = '".mysql_real_escape_string($_GET[$names['artid']])."' LIMIT 1";

    //Hits +1:
    mysql_query("UPDATE ".$mysql_tables['artikel']." SET hits = hits+1 WHERE id = '".mysql_real_escape_string($_GET[$names['artid']])."' LIMIT 1");
    }
// Archiv-Ansicht
elseif(isset($_GET[$names['artid']]) && $_GET[$names['artid']] == "archiv" && !empty($settings['archiv_time']) && $settings['archiv_time'] > 0 && is_numeric($settings['archiv_time'])){
    $flag_archiv = "&amp;".$names['artid']."=archiv";
	$add2query_cat = _01article_CreateCatQuery($_REQUEST[$names['catid']]);
    $query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static = '0' AND timestamp <= '".$qt."' AND (endtime >= '".time()."' OR endtime = '0') AND (".$add2query_cat.") ORDER BY timestamp DESC";

    //Eintr�ge pro Seite f�r Archiv-Funktion �ndern:
    $settings['articleperpage'] = 30;
	makepages($query,$sites,$names['page'],$settings['articleperpage']);
    }
// Alle Artikel anzeigen
else{
	if(!empty($settings['archiv_time']) && $settings['archiv_time'] > 0 && is_numeric($settings['archiv_time']))
		$qt_query = "AND (timestamp>'".$qt."' OR top = '1' OR endtime >= '".time()."') ";
	else $qt_query = "";
	
    if(isset($_REQUEST[$names['search']]) && !empty($_REQUEST[$names['search']]) && $settings['artikelsuche'] == 1){
		if($flag_utf8)
			$parsed_searchstring = mysql_real_escape_string(parse_uml(utf8_decode(str_replace("*","%",$_REQUEST[$names['search']]))));
		else
			$parsed_searchstring = mysql_real_escape_string(parse_uml(str_replace("*","%",$_REQUEST[$names['search']])));
		$add2query_cat = _01article_CreateCatQuery($_REQUEST[$names['catid']]);
        $add2query = "AND (titel LIKE '%".$parsed_searchstring."%' OR text LIKE '%".$parsed_searchstring."%' OR zusammenfassung LIKE '%".$parsed_searchstring."%') AND (".$add2query_cat.")";
		}
    elseif(isset($_REQUEST[$names['catid']]) && !empty($_REQUEST[$names['catid']])){
        $add2query_cat = _01article_CreateCatQuery($_REQUEST[$names['catid']]);
		$add2query = $qt_query."AND (".$add2query_cat.")";
		}
    else
        $add2query = $qt_query;

	$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime='0') ".$add2query." ORDER BY top DESC,timestamp DESC";
	makepages($query,$sites,$names['page'],$settings['articleperpage']);		
	}

// List > 0?
$list = mysql_query($query);
echo "<!-- 2559ad821dde361560dbf967c3406f51 -->";
if(mysql_num_rows($list) == 0) $iderror = 1;









// ARCHIV
if(isset($_GET[$names['artid']]) && $_GET[$names['artid']] == "archiv" && !empty($settings['archiv_time']) && $settings['archiv_time'] > 0 && is_numeric($settings['archiv_time'])){
    include($tempdir."archiv_top.html");
    
	$round = 1;
	while($row = mysql_fetch_assoc($list)){
        if($round == 1){
        	$archiv_year = date("Y",$row['timestamp']);
			$archiv_month = date("n",$row['timestamp']);
			
			include($tempdir."archiv_top_year.html");
			include($tempdir."archiv_top_month.html");
		}
		
		if($archiv_year != date("Y",$row['timestamp'])){
			$archiv_year = date("Y",$row['timestamp']);
			include($tempdir."archiv_top_year.html");
			}
		if($archiv_month != date("n",$row['timestamp'])){
			$archiv_month = date("n",$row['timestamp']);
			include($tempdir."archiv_top_month.html");
			}
		
		$datum = date("d.m.y",$row['timestamp']).", ".date("G:i",$row['timestamp']);
        $titel = stripslashes($row['titel']);
		
        $username_array = getUserdatafields($row['uid'],"username");
		$autor = stripslashes($username_array['username']);
		
        include($tempdir."archiv_bit.html");
        $round++;
        }
		
    include($tempdir."archiv_bottom.html");
	$iderror = 0;
    }
	

	
	
	
	
	
	
	
	
// NORMALE AUSGABE
else{
    // Alle Kategorien in einen mehrdimensionalen Array einlesen
	$listcat = mysql_query("SELECT * FROM ".$mysql_tables['cats']." ORDER BY sortid,name");
	while($rowcat = mysql_fetch_assoc($listcat)){
		$category[$rowcat['id']]['id'] 		= $rowcat['id'];
		$category[$rowcat['id']]['name']	= htmlentities(stripslashes($rowcat['name']));
		$category[$rowcat['id']]['catpic']	= stripslashes($rowcat['catpic']);
		}
	
	//DB-Daten zur Ausgabe auswerten:
	while($row = mysql_fetch_assoc($list)){
        $datum 		= date("d.m.y",$row['timestamp']);
        $uhrzeit 	= date("G:i",$row['timestamp']);

        //Catid & Catimage auslesen
        if($row['newscatid'] != "0"){
            $c = 0;
			$catimg = "";
			$catname = "";
            $newscatids_array = explode(",",substr($row['newscatid'],1,strlen($row['newscatid'])-2));
            foreach($newscatids_array as $newscatid_s){
				if(isset($category[$newscatid_s]['name'])){
                    if(isset($category[$newscatid_s]['catpic']) && $category[$newscatid_s]['catpic'] != "")
                        $catimg .= "<a href=\"".addParameter2Link($filename,$names['catid']."=".$newscatid_s)."\"><img src=\"".$catuploaddir.$category[$newscatid_s]['catpic']."\" alt=\"Kategorie: ".$category[$newscatid_s]['name']."\" title=\"Kategorie: ".$category[$newscatid_s]['name']."\" align=\"left\" /></a> ";
                    else
                        $catimg .= "";
						
                    if($c > 0) $catname .= ", ";
                    $catname .= "<a href=\"".addParameter2Link($filename,$names['catid']."=".$newscatid_s)."\" class=\"catlink\">".$category[$newscatid_s]['name']."</a>";
                    $c++;
					}
                }
            }
        else{ $catimg = ""; $catname = ""; }

		$more = 0;
		// Normalen, kompletten Haupttext anzeigen
		if((isset($_GET[$names['artid']]) && !empty($_GET[$names['artid']]) ||
		   $settings['artikeleinleitung'] == 0 ||
		   $settings['artikeleinleitung'] == 2 && $row['autozusammen'] == 0 && empty($row['zusammenfassung'])) &&
		   (!isset($_REQUEST[$names['search']]) || isset($_REQUEST[$names['search']]) && empty($_REQUEST[$names['search']]))){

	        $artikeltext = stripslashes($row['text']);
			$artikeltext = str_replace("../01pics/",$picuploaddir,$artikeltext);
			$artikeltext = str_replace("../01files/",$attachmentuploaddir,$artikeltext);
			}
		// Zusammenfassung anzeigen
		else{
	        if($row['autozusammen'] == 0 && !empty($row['zusammenfassung'])){
	            $artikeltext = stripslashes($row['zusammenfassung']);
				$artikeltext = str_replace("../01pics/",$picuploaddir,$artikeltext);
				$artikeltext = str_replace("../01files/",$attachmentuploaddir,$artikeltext);
	            }
	        else
				$artikeltext = "<p>".substr(strip_tags(stripslashes($row['text'])),0,$settings['artikeleinleitungslaenge']).$lang['weiterlesen']."</p>";

			// Weiterlesen-Link nur einbinden, wenn Text l�nger als Zusammenfassung oder eigener Text eingegeben wurde
			if($row['autozusammen'] == 0 && !empty($row['zusammenfassung']) || 
			   strlen($artikeltext) < strlen(stripslashes($row['text'])))
				$more = 1;
			else $more = 0;
			}


        // Anzahl bisheriger Kommentare ermitteln
        if($settings['comments'] == 1){ 
			$comments = 0;
			list($comments) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['comments']." WHERE frei = '1' AND modul = '".mysql_real_escape_string($modul)."' AND postid = '".$row['id']."'"));
			}

        // Weitere Variablen f�r die Template-Ausgabe aufbereiten
        $username_array = getUserdatafields($row['uid'],"username,01acp_signatur");
		$username = stripslashes($username_array['username']);
		if(!empty($username_array['signatur']) && $more == 0 && $row['hide_signature'] == 0)
			$signatur = "<p class=\"signatur\">".nl2br(stripslashes(str_replace("&","&amp;",$username_array['signatur'])))."</p>";
		else
			$signatur = "";
		
        $titel = stripslashes($row['titel']);
		$static = $row['static'];
        $system_link_row = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($row['id'],stripslashes($row['titel']),$row['timestamp']),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]));
        
		// Get serialized data
		if($ser_fields){
			$return_temp = unserialize($row['serialized_data']);

			if(empty($return_temp)){
				for($x=1;$x<=ANZ_SER_FIELDS;$x++){
					$row['ser_field_'.$x] = "";
					}
				}
			else{
				for($x=1;$x<=ANZ_SER_FIELDS;$x++){
					$row['ser_field_'.$x] = htmlspecialchars(stripslashes($return_temp['field_'.$x]));
					}
				}
			}

		// artikel_self-Link
        $artikeltext = str_replace("{artikel_self}",$system_link_row,$artikeltext);

        // ggf. Links zum vorherigen / n�chsten Eintrag generieren
        if(isset($_GET[$names['artid']]) && !empty($_GET[$names['artid']]) && $_GET[$names['artid']] != "archiv" && $row['static'] == 0){
			$add2query_cat = _01article_CreateCatQuery($_REQUEST[$names['catid']]);
		
			$listnext = mysql_query("SELECT id,timestamp,titel FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp > '".$row['timestamp']."' AND timestamp <= '".time()."' AND (endtime>='".time()."' OR endtime = '0') AND (".$add2query_cat.") ORDER BY timestamp LIMIT 1");
            $rownext = mysql_fetch_assoc($listnext);
            $next_link = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($rownext['id'],stripslashes($rownext['titel']),$rownext['timestamp']),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpartikel"));
            $next_titel = stripslashes($rownext['titel']);
			
            $listprev = mysql_query("SELECT id,timestamp,titel FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp < '".$row['timestamp']."' AND (endtime>='".time()."' OR endtime = '0') AND (".$add2query_cat.")  ORDER BY timestamp DESC LIMIT 1");
            $rowprev = mysql_fetch_assoc($listprev);
            $prev_link = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($rowprev['id'],stripslashes($rowprev['titel']),$rowprev['timestamp']),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpartikel"));
            $prev_titel = stripslashes($rowprev['titel']);
            }

        // Template einbinden
        include($tempdir."artikel.html");


		
		
		
		
		
		
		
		
		
		
		// KOMMENTAR-AUSGABE & FORMULAR, etc.
		if(isset($_GET[$names['artid']]) && !empty($_GET[$names['artid']]) && $_GET[$names['artid']] > 0 && 
		   is_numeric($_GET[$names['artid']]) && $_GET[$names['artid']] != "archiv" && 
		   $settings['comments'] == 1 && $settings['artikelcomments'] == 1 && $row['comments'] == 1){
            //Template einbinden
            include($tempdir."comments_head.html");

            // Anti-XSS
            if(isset($_POST['send_comment']))	$_POST['send_comment']		= strip_tags($_POST['send_comment']);
            if(isset($_POST['modul_comment']))	$_POST['modul_comment']		= strip_tags($_POST['modul_comment']);
            if(isset($_POST['autor']))			$_POST['autor']				= filter_var($_POST['autor'],FILTER_SANITIZE_SPECIAL_CHARS);
            if(isset($_POST['email']))			$_POST['email']				= strip_tags($_POST['email']);
            if(isset($_POST['url']))			$_POST['url']				= strip_tags($_POST['url']);
            if(isset($_POST['comment']))		$_POST['comment']			= strip_tags($_POST['comment']);
            if(isset($_POST['antispam']))		$_POST['antispam']			= strip_tags($_POST['antispam']);
            if(isset($_POST['deaktiv_bbc']))	$_POST['deaktiv_bbc']		= strip_tags($_POST['deaktiv_bbc']);
            if(isset($_POST['uid']))			$_POST['uid']				= strip_tags($_POST['uid']);
			
			$message = 0;
            // Neuen Kommentar hinzuf�gen
            if(isset($_POST['send_comment']) && $_POST['send_comment'] == 1 &&
			   isset($_POST['modul_comment']) && $_POST['modul_comment'] == $modul){
				if($flag_utf8)
				    $message = insert_Comment(htmlentities(utf8_decode($_POST['autor'])),$_POST['email'],$_POST['url'],utf8_decode($_POST['comment']),$_POST['antispam'],$_POST['deaktiv_bbc'],$row['id'],$_POST['uid']);
				else
					$message = insert_Comment($_POST['autor'],$_POST['email'],$_POST['url'],$_POST['comment'],$_POST['antispam'],$_POST['deaktiv_bbc'],$row['id'],$_POST['uid']);
				}

            // KOMMENTARE AUSGEBEN
            $nr = 1;
            $comment_query = "SELECT * FROM ".$mysql_tables['comments']." WHERE modul='".$modul."' AND postid='".$row['id']."' AND frei='1' ORDER BY timestamp ".mysql_real_escape_string($comment_desc)."";
            
			// Seiten-Funktion
            if($settings['comments_perpage'] > 0){
                $comment_sc = mysql_num_rows(mysql_query($comment_query));
                $comment_sites = ceil($comment_sc/$settings['comments_perpage']);    //=Anzahl an Seiten
				
                if(isset($_GET[$names['cpage']]) && $_GET[$names['cpage']] == "last" && $comment_sites > 1){
                    $_GET[$names['cpage']] = $comment_sites;
                    $commentsstart = $comment_sites*$settings['comments_perpage']-$settings['comments_perpage'];
                    $comment_query .= " LIMIT ".mysql_real_escape_string($commentsstart).",".mysql_real_escape_string($settings['comments_perpage'])."";
                    $nr = $commentsstart+1;
                    }
                elseif(isset($_GET[$names['cpage']]) && !empty($_GET[$names['cpage']]) && $_GET[$names['cpage']] <= $comment_sites && $comment_sites > 1){
                    $commentsstart = $_GET[$names['cpage']]*$settings['comments_perpage']-$settings['comments_perpage'];
                    $comment_query .= " LIMIT ".mysql_real_escape_string($commentsstart).",".mysql_real_escape_string($settings['comments_perpage'])."";
                    $nr = $commentsstart+1;
                    }
                else
                    $comment_query .= " LIMIT ".mysql_real_escape_string($settings['comments_perpage'])."";
                }

            $clist = mysql_query($comment_query);
            while($crow = mysql_fetch_assoc($clist)){
                
				// URL
                if(!empty($crow['url']) && $crow['url'] != "http://"){
                    if(substr_count($crow['url'], "http://") < 1)
						$url = "http://".stripslashes($crow['url']);
					else
						$url = stripslashes($crow['url']); 
                    }
                else $url = "";

                // Weitere Variablen f�r die Template-Ausgabe aufbereiten
                $datum = date("d.m.y - G:i",$crow['timestamp']);
                $autorenname = stripslashes($crow['autor']);
                $comment_id = $crow['id'];

                // BB-Code & Smilies
                $comment = stripslashes($crow['comment']);
                if($crow['bbc'] == 1 && $settings['comments_bbc'] == 1 && $crow['smilies'] == 1 && $settings['comments_smilies'] == 1)
					$comment = bb_code_comment($comment,1,1,1);
				elseif($crow['bbc'] == 1 && $settings['comments_bbc'] == 1 && ($crow['smilies'] == 0 || $settings['comments_smilies'] == 0))
					$comment = bb_code_comment($comment,1,1,0);
				elseif(($crow['bbc'] == 0 || $settings['comments_bbc'] == 0) && $crow['smilies'] == 1 && $settings['comments_smilies'] == 1)
					$comment = bb_code_comment($comment,1,0,1);
				else 
					$comment = nl2br($comment);

                // Template ausgeben
                include($tempdir."commentbit.html");

                $nr++;
                }

            //Seiten (Kommentare) ausgeben
            if($comment_sites > 1 && $settings['comments_perpage'] > 0){
                if(isset($_GET[$names['cpage']]) && $_GET[$names['cpage']] > 1){
                    $c_sz = $_GET[$names['cpage']]-1;
                    if($c_sz > 1)
						$c_szl1 = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($_GET[$names['artid']]),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['cpage']."=1&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpcomments"));
                    $c_szl2 = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($_GET[$names['artid']]),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['cpage']."=".$c_sz."&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpcomments"));
                    }
                else{ $c_szl1 = ""; $c_szl2 = ""; }
                
				if(!isset($_GET[$names['cpage']]) || isset($_GET[$names['cpage']]) && empty($_GET[$names['cpage']]))
                    {
                    $comment_current = 1;
                    if($comment_sites > 1) $c_sv = 2;
                    }
                else{
                    $comment_current = $_GET[$names['cpage']];
                    $c_sv = $_GET[$names['cpage']]+1;
                    }
                
				if(isset($_GET[$names['cpage']]) && $_GET[$names['cpage']] < $comment_sites || !isset($_GET[$names['cpage']]) && $comment_sites > 1){
                    $c_svl1 = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($_GET[$names['artid']]),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['cpage']."=".$c_sv."&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpcomments"));
                    if($c_sv != $comment_sites)
						$c_svl2 = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($_GET[$names['artid']]),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['cpage']."=".$comment_sites."&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpcomments"));
                    }
                else{ $c_svl1 = ""; $c_svl2 = ""; }
                }

            //Template ausgeben
            include($tempdir."comments_end.html");

            //Unterschiedliche Sprungmarken nach Absenden des Kommentarformulars
            if(!isset($jumpto_id)) $jumpto_id = "";
			if($settings['commentfreischaltung'] == 0) $jumpto = "01comment".$jumpto_id; else $jumpto = "01jumpcomments";
            if($comment_desc == "") $jumpto_csite = "last"; else $jumpto_csite = "1";
			
            $system_link_form = parse_cleanerlinks(addParameter2Link(_01article_echo_ArticleLink($_GET[$names['artid']]),$names['page']."=".$_REQUEST[$names['page']]."&amp;".$names['cpage']."=".$jumpto_csite."&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpcomments_add"));


            if($row['comments'] == 1 && $settings['comments'] == 1){
                mt_srand((double)microtime()*1000000); 
				$zahl = mt_rand(1, 9999999999999);
				$uid = md5(time().$_SERVER['REMOTE_ADDR'].$zahl.$_GET[$names['artid']]);
				//Template ausgeben
                include($tempdir."comments_add.html");
                }
            }
        unset($icon);
        unset($catimg);
        unset($catname);
        }
    }//ENDE: "Normale News-Ausgabe"

// Ausgabe der Seiten
if($sites > 1){
    $option_sites = "";
	for($o=1;$o<=$sites;$o++){
        $option_sites .= "<option>".$o."</option>\n";
        }

    if(isset($_REQUEST[$names['page']]) && $_REQUEST[$names['page']] > 1){
        $sz = $_REQUEST[$names['page']]-1;

        if($sz != 1)
			$szl1 = parse_cleanerlinks(addParameter2Link($filename,$names['page']."=1&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']].$flag_archiv."#01jumpartikel"));
		else $szl1 = "";

		$szl2 = parse_cleanerlinks(addParameter2Link($filename,$names['page']."=".$sz."&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']].$flag_archiv."#01jumpartikel"));
        }
    else{ $szl1 = ""; $szl2 = ""; }
	
    if(!isset($_REQUEST[$names['page']]) || isset($_REQUEST[$names['page']]) && empty($_REQUEST[$names['page']])){
        $current = 1;
        if($sites > 1){ $sv = 2; }
        }
    else{
        $current = $_REQUEST[$names['page']];
        $sv = $_REQUEST[$names['page']]+1;
        }
    
	if(isset($_REQUEST[$names['page']]) && $_REQUEST[$names['page']] < $sites || !isset($_REQUEST[$names['page']]) && $sites > 1){
        
		$svl1 = parse_cleanerlinks(addParameter2Link($filename,$names['page']."=".$sv."&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']].$flag_archiv."#01jumpartikel")); 
        if($sv != $sites) 
			$svl2 = parse_cleanerlinks(addParameter2Link($filename,$names['page']."=".$sites."&amp;".$names['search']."=".$_REQUEST[$names['search']]."&amp;".$names['catid']."=".$_REQUEST[$names['catid']].$flag_archiv."#01jumpartikel"));
        }
    else{
		$svl1 = "";
		$svl2 = "";

		if(isset($_REQUEST[$names['page']]) && $_REQUEST[$names['page']] == $sites &&
		   !empty($settings['archiv_time']) && $settings['archiv_time'] > 0 && $_GET[$names['artid']] != "archiv")
			$svl2a = "<a href=\"".parse_cleanerlinks(addParameter2Link($filename,$names['artid']."=archiv&amp;".$names['catid']."=".$_REQUEST[$names['catid']]."#01jumpartikel"))."\">Archiv &raquo;</a>";
		}
    }

// Link: Zur�ck zur �bersicht
if(isset($_REQUEST[$names['search']]) && !empty($_REQUEST[$names['search']]))
    $ssearch = 1;
elseif(isset($_REQUEST[$names['catid']]) && !empty($_REQUEST[$names['catid']]) && $sites != 0)
	$ssearch = 2;

// Ansicht �ndern: Suche erfolglos, Zur�ck
if($sites == 0 && isset($_REQUEST[$names['search']]) && !empty($_REQUEST[$names['search']]) ||
   isset($sc) && $sc == 0 && isset($_REQUEST[$names['search']]) && !empty($_REQUEST[$names['search']]))
    $searcherror = 1;

// Seitenzahl ausblenden, wenn nur eine Seite
if($sites == 1) $sites = "";

// Kategorien vorhanden? --> Ausgeben
if(isset($category) && count($category) > 0){
	$listcats = "";
	foreach($category as $cat){
		// Sind Eintr�ge dieser Kategorie zugeordnet?
		if($qt > 0)
			list($newsvorhanden) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static = '0' AND timestamp <= '".time()."' AND timestamp>'".$qt."' AND (endtime >= '".time()."' OR endtime='0') AND newscatid LIKE '%,".$cat['id'].",%'"));
		else
			list($newsvorhanden) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static = '0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime='0') AND newscatid LIKE '%,".$cat['id'].",%'"));
		
		if($newsvorhanden > 0)
			$listcats .= "<option value=\"".$cat['id']."\">".$cat['name']." (".$newsvorhanden.")</option>\n";

		$newsvorhanden = 0;    
		}
	}

//Template einbinden
include($tempdir."pages.html");

//Template einbinden
include($tempdir."main_bottom.html");

$tempdir	= "templates/";
$iconpf		= "images/icons/";
$query		= "";

?>