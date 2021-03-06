<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Artikel: �bersicht, Bearbeiten, Erstellen
	#fv.321#
*/

// Berechtigungsabfragen
if((isset($_REQUEST['action']) && $_REQUEST['action'] == "newarticle" && $userdata['newarticle'] == 1) ||
   (isset($_REQUEST['action']) && $_REQUEST['action'] == "articles" && $userdata['editarticle'] >= 1) ||
   (isset($_REQUEST['action']) && $_REQUEST['action'] == "edit" && ($userdata['editarticle'] >= 1 || $userdata['staticarticle'] >= 1)) ||
   (isset($_REQUEST['action']) && ($_REQUEST['action'] == "newstatic" || $_REQUEST['action'] == "statics") && $userdata['staticarticle'] >= 1))
{

// Variablen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit"){
	$input_field['publish'] 	= "�bernehmen";
	$input_field['save'] 		= "Zwischenspeichern";
	}
else{
	$input_field['publish'] 	= "Ver�ffentlichen";
	$input_field['save'] 		= "Zwischenspeichern";
	}
if(!isset($_REQUEST['who']))	$_REQUEST['who'] 	= "";
	
// Notice: Undefined index: ... beheben
if(!isset($_GET['search'])) 	$_GET['search']		= "";
if(!isset($_GET['sort'])) 		$_GET['sort']		= "";
if(!isset($_GET['orderby'])) 	$_GET['orderby']	= "";
if(!isset($_GET['site'])) 		$_GET['site']		= "";
if(!isset($_GET['catid']))		$_GET['catid']		= "";

$add_filename 	= "&amp;search=".$_GET['search']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."&amp;site=".$_GET['site']."";
$flag_overview 	= FALSE;
	
	
	
	
	
	
// Formular wurde abgeschickt - Daten �berpr�fen
$flag_parsed = FALSE;
if(isset($_POST['do']) && (isset($_POST['save']) || isset($_POST['publish'])) && 
	isset($_POST['textfeld']) && !empty($_POST['textfeld']) &&
	isset($_POST['titel']) && !empty($_POST['titel'])){
	//Auswertung der Fomurlardaten zur Eintragung in die Datenbank

	// Datum / Uhrzeit:
	if(isset($_POST['starttime_date']) && !empty($_POST['starttime_date']))
		$start_date 	= explode(".",$_POST['starttime_date']);
	else
		$start_date	= explode(".",date("d.m.Y"));
	if(isset($_POST['starttime_uhr']) && !empty($_POST['starttime_uhr']))
		$start_uhr 		= explode(".",$_POST['starttime_uhr']);
	else
		$start_uhr		= explode(".",date("G.i"));
		
	$start_mysqldate 	= mktime($start_uhr[0], $start_uhr[1], "0", $start_date[1], $start_date[0], $start_date[2]);
	
	if(isset($_POST['endtime_date']) && !empty($_POST['endtime_date']) && isset($_POST['endtime_uhr']) && !empty($_POST['endtime_uhr'])){
		$ende_date 		= explode(".",$_POST['endtime_date']);
		$ende_uhr 		= explode(".",$_POST['endtime_uhr']);
		$ende_mysqldate = mktime($ende_uhr[0], $ende_uhr[1], "0", $ende_date[1], $ende_date[0], $ende_date[2]);
		}
	else
		$ende_mysqldate = "0";
	
	// Kategorien parsen:
	if(isset($_POST['newscat']) && $_POST['newscat'] != "" && is_array($_POST['newscat']) && !in_array(0,$_POST['newscat'])){
		$newscats_string = ",";
		$newscats_string .= implode(",",$_POST['newscat']);
		$newscats_string .= ",";
		}
	else
		$newscats_string = 0;
		
	// Zusammenfassung
	if(!isset($_POST['zusammenfassung'])) $_POST['zusammenfassung'] = "";
	if(isset($_POST['autozusammen']) && $_POST['autozusammen'] == 1 && $settings['artikeleinleitung'] == 2 || 
	   isset($_POST['autozusammen']) && $_POST['autozusammen'] != 1 && (isset($_POST['zusammenfassung']) && empty($_POST['zusammenfassung']) || !isset($_POST['zusammenfassung'])) && $settings['artikeleinleitung'] == 2){
		$autozusammen = 1;
		$zusammen = "";
		}
	elseif($settings['artikeleinleitung'] >= 1){
		$autozusammen = 0;
		$zusammen = substr(stripslashes($_POST['zusammenfassung']),0,$settings['artikeleinleitungslaenge']);
		}
	else{
		$autozusammen = 0;
		$zusammen = "";
		}
		
	// Text parsen
	$text = stripslashes($_POST['textfeld']);
		
	// Freischaltung
	if($userdata['freischaltung'] == 1 && $settings['artikelfreischaltung'] == 1)
		$frei = 0;
	else
		$frei = 1;
		
	// Verstecken / Zwischenspeichern?
	if(isset($_POST['save']))
		$hide = 1;
	else
		$hide = 0;
		
	// Kommentare de/aktivieren
	if(isset($_POST['comments']) && $_POST['comments'] == 1)
		$comments = 0;
	else
		$comments = 1;
		
	// Hide Headline
	if(!isset($_POST['hide_headline'])) $_POST['hide_headline'] = 0;
	
	// Top
	if(!isset($_POST['top']) || isset($_POST['top']) && (empty($_POST['top']) || !is_numeric($_POST['top']))) $_POST['top'] = 0;
	
	// Signatur ausblenden
	if($_REQUEST['action'] == "newstatic") $_POST['hide_signature'] = 0; 
	elseif(!isset($_POST['hide_signature']) || isset($_POST['hide_signature']) && (empty($_POST['hide_signature']) || !is_numeric($_POST['hide_signature']))) $_POST['hide_signature'] = 0;
	
	// Data2Serialize
	$ser_fieldarray = array();
	if($ser_fields){
		for($x=1;$x<=ANZ_SER_FIELDS;$x++){
			if(isset($_POST['ser_field_'.$x]) && !empty($_POST['ser_field_'.$x]))
				$ser_fieldarray['field_'.$x] = addslashes($_POST['ser_field_'.$x]);
			else
				$ser_fieldarray['field_'.$x] = "";
			}
		}
	}

	
	
	
	
	
	
	
	
	

// NEUEN ARTIKEL / neue statische Seite anlegen (SPEICHERN)
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == "newarticle" || $_REQUEST['action'] == "newstatic")){
	$flag_formular = TRUE;
	$gotget = false;
	
	$input_do						= "save";
	switch($_REQUEST['action']){
	  case "newarticle":
		$input_field['site_titel'] 	= "Neuer Artikel";
		$input_field['bezeichnung'] = "Artikel";
		$input_field['next']		= "Neuen Artikel";
		$input_section				= "article";
		$input_section2				= "articles";
		$input_action 				= "newarticle";
		$flag_static				= 0;
		$_POST['hide_headline']		= 0;
	  break;
	  case "newstatic":
		$input_field['site_titel'] 	= "Neue Seite";
		$input_field['bezeichnung'] = "Seiten";
		$input_field['next']		= "Neue statische Seite";
		$input_section				= "static";
		$input_section2				= "statics";
		$input_action 				= "newstatic";
		$flag_static				= 1;
	  break;
	  }
	
	// Formular wurde abgeschickt - Daten �berpr�fen und in DB eintragen
	if(isset($_POST['do']) && $_POST['do'] == "save" && 
		isset($_POST['textfeld']) && !empty($_POST['textfeld']) &&
		isset($_POST['titel']) && !empty($_POST['titel'])){
		
		// Anderen Autor f�r Artikel setzten
		if(isset($_POST['autor']) && !empty($_POST['autor']) && ($userdata['editarticle'] == 2 && $flag_static == 0 || $flag_static == 1 && $userdata['staticarticle'] == 2))
			$autorid = $mysqli->escape_string($_POST['autor']);
		else
			$autorid = $userdata['id'];
			
		//Eintragung in Datenbank vornehmen:
		$sql_insert = "INSERT INTO ".$mysql_tables['artikel']." (utimestamp,endtime,frei,hide,icon,titel,newscatid,content,autozusammen,zusammenfassung,comments,hide_headline,uid,static,top,hits,hide_signature,serialized_data) VALUES (
						'".$start_mysqldate."',
						'".$ende_mysqldate."',
						'".$frei."',
						'".$hide."',
						'',
						'".$mysqli->escape_string(htmlentities($_POST['titel'],$htmlent_flags,$htmlent_encoding_acp))."',
						'".$mysqli->escape_string($newscats_string)."',
						'".$mysqli->escape_string($text)."',
						'".$autozusammen."',
						'".$mysqli->escape_string($zusammen)."',
						'".$comments."',
						'".$mysqli->escape_string($_POST['hide_headline'])."',
						'".$autorid."',
						'".$flag_static."',
						'".$mysqli->escape_string($_POST['top'])."',
						'0',
						'".$mysqli->escape_string($_POST['hide_signature'])."',
						'".$mysqli->escape_string(serialize($ser_fieldarray))."'
						)";
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		$saved_id = $mysqli->insert_id;
		
		if($saved_id > 0 && $hide == 1){
			// Artikel / Seite wurde NUR zwischengespeichert
			echo "<p class=\"meldung_erfolg\">".$input_field['site_titel']." wurde <b>zwischengespeichert.</b><br />
					Ihre Eingaben wurden noch <b>nicht</b> ver&ouml;ffentlicht!<br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$saved_id."&amp;static=".$flag_static."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<b><a href=\"".$filename."&amp;action=".$input_section2."&amp;do=publish&amp;id=".$saved_id."\">Jetzt ver&ouml;ffentlichen &raquo;</a></b></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		elseif($saved_id > 0 && $hide == 0 && $frei == 1){
			// Artikel / Seite wurde gespeichert UND ver�ffentlicht (sichtbar)
			echo "<p class=\"meldung_erfolg\"><b>".$input_field['site_titel']." wurde hinzugef&uuml;gt</b><br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$saved_id."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<a href=\"".$filename."&amp;action=".$input_action."\">".$input_field['next']." erstellen &raquo;</a></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		elseif($saved_id > 0 && $hide == 0 && $frei == 0){
			// Artikel / Seite wurde gespeichert UND wartet auf die Freischaltung
			echo "<p class=\"meldung_erfolg\"><b>".$input_field['site_titel']." wurde gespeichert und muss nun vor seiner Ver&ouml;ffentlichung freigeschaltet werden.</b><br />
					Benutzer mit entsprechenden Rechten wurden bereits informiert!<br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$saved_id."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<a href=\"".$filename."&amp;action=".$input_action."\">".$input_field['next']." erstellen &raquo;</a></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			
			// E-Mails an Moderatoren verschicken
			$header = "From:".$settings['email_absender']."<".$settings['email_absender'].">\n";
			$email_betreff = $settings['sitename']." - ".$input_field['site_titel']." - bitte freischalten";
			$emailbody = "Es wurde soeben ein neuer Artikel / eine neue Seite erstellt, die von Ihnen �berpr�ft und freigeschaltet werden kann.
Bitte loggen Sie sich dazu in den Administrationsbereich ein
".$settings['absolut_url']."01acp/
und �berpr�fen Sie ihn.\n\n---\nWebmailer";
				
			// Es werden 10 beliebige Benutzer mit den entsprechenden Rechten per E-Mail informiert.
			$list = $mysqli->query("SELECT id,username,mail FROM ".$mysql_tables['user']." WHERE ".$mysqli->escape_string($modul)."_editarticle = '2' AND sperre = '0' AND 01acp_".$mysqli->escape_string($modul)." = '1' ORDER BY rand() LIMIT 10");
			while($row = $list->fetch_assoc()){
		        mail(stripslashes($row['mail']),$email_betreff,$emailbody,$header);
				}
			}
		else
			echo "<p class=\"meldung_error\"><b>Es trat ein unvorhergesehener Fehler auf.<br />
					Bitte beachten Sie die MySQL-Fehlermeldung!</b></p>";
		
		if($saved_id > 0 && $flag_static == 1 && $hide == 0)
			echo "<p class=\"meldung_hinweis\">Die statische Seite wurde mit der ID <b>".$saved_id."</b> gespeichert.
					Sie k&ouml;nnen die Seite &uuml;ber diese ID einbinden.<br />
					Der PHP-Befehl dazu lautet:<br />
					<code>
					&lt;?PHP<br />
					\$subfolder = \"01scripts/\";<br />
					\$modul = \"".$modul."\";<br />
					<br />
					\$show['artid'] = ".$saved_id.";<br />
					include(\$subfolder.\"01module/\".\$modul.\"/01article.php\");<br />
					?&gt;</code>
				  </p>";
		}
	elseif(isset($_POST['do']) && $_POST['do'] == "save"){
		echo "<p class=\"meldung_error\"><b>Fehler: Sie haben nicht alle ben&ouml;tigten Felder
				(Titel und Textfeld) ausgef&uuml;llt!</b></p>";

		$form_data = _01article_getForm_DataArray();
		if(!isset($form_data['username'])) $form_data['username'] = $userdata['username'];
		$gotget = true;
		}

	// Formular ausgeben
	if($flag_formular){
		if(isset($_REQUEST['copyid']) && is_numeric($_REQUEST['copyid']) && $_REQUEST['copyid'] > 0 && !$gotget){
			$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE id = '".$mysqli->escape_string($_REQUEST['copyid'])."' LIMIT 1";

			$list = $mysqli->query($query);
			while($row = $list->fetch_assoc()){
				$form_data =  _01article_fillForm_DataArray($row);
				$form_data['uid']				= $userdata['id'];
				$form_data['username']			= $userdata['username'];
				$form_data['starttime_date']	= date("d.m.Y");
				$form_data['starttime_uhr']		= date("G.i");
				}
			}
		elseif((!isset($form_data) || isset($form_data) && ($form_data['id'] <= 0 || empty($form_data['id']))) && !$gotget){
			// Mit Standardwerten f�llen
			$form_data =  _01article_fillForm_DataArray();
			$form_data['uid']		= $userdata['id'];
			$form_data['username']	= $userdata['username'];
			}
		include_once($modulpath."write_form.php");
		}
	}
	
	
	
	
	
	
	
	
	
// Artikel / Seite bearbeiten
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit" && ($userdata['editarticle'] >= 1 || $userdata['staticarticle'] >= 1) &&
	   isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) &&
	   isset($_REQUEST['static']) && is_numeric($_REQUEST['static'])){
	
	$input_do						= "update";
	switch($_REQUEST['static']){
	  case "0":
		$input_field['site_titel'] 	= "Artikel bearbeiten";
		$input_field['bezeichnung'] = "Artikel";
		$input_field['next']		= "Neuen Artikel";
		$input_section				= "article";
		$input_section2				= "articles";
		$input_action 				= "edit";
		$input_action2 				= "newarticle";
		$flag_static				= 0;
		$_POST['hide_headline']		= 0;
	  break;
	  case "1":
		$input_field['site_titel'] 	= "Statische Seite bearbeiten";
		$input_field['bezeichnung'] = "Seite";
		$input_field['next']		= "Neue statische Seite";
		$input_section				= "static";
		$input_section2				= "statics";
		$input_action 				= "edit";
		$input_action2 				= "newstatic";
		$flag_static				= 1;
	  break;
	  }
	
	// Formular wurde abgeschickt - Daten �berpr�fen und in DB eintragen
	if(isset($_POST['do']) && $_POST['do'] == "update" && 
		isset($_POST['textfeld']) && !empty($_POST['textfeld']) &&
		isset($_POST['titel']) && !empty($_POST['titel'])){
		
		// Benutzerberechtigung �berpr�fen
		if($userdata['editarticle'] == 1 || $userdata['staticarticle'] == 1){
			$list = $mysqli->query("SELECT uid FROM ".$mysql_tables['artikel']." WHERE id = '".$mysqli->escape_string($_POST['id'])."' LIMIT 1");
			$uidrow = $list->fetch_assoc();
			}
		if(($userdata['editarticle'] == 2 || $userdata['editarticle'] == 1 && $userdata['id'] == $uidrow['uid']) && $flag_static == 0 || 
		   $flag_static == 1 && ($userdata['staticarticle'] == 2 || $userdata['staticarticle'] == 1 && $userdata['id'] == $uidrow['uid'])){
			
			
			// Anderen Autor f�r Artikel setzten
			$autorid_q = "";
			if($userdata['editarticle'] == 2 && $flag_static == 0 || $flag_static == 1 && $userdata['staticarticle'] == 2){
				if(isset($_POST['autor']) && (!empty($_POST['autor']) || (int)$_POST['autor'] == 0) && $_POST['autor'] != $_POST['uid'] && ($userdata['editarticle'] == 2 || $userdata['staticarticle'] == 2))
					$autorid_q = "uid = '".$mysqli->escape_string($_POST['autor'])."',";
				}
				
			// Eintragung in Datenbank aktualisieren:
			if($mysqli->query("UPDATE ".$mysql_tables['artikel']." SET 
							utimestamp		= '".$start_mysqldate."',
							endtime			= '".$ende_mysqldate."',
							hide			= '".$hide."',
							titel			= '".$mysqli->escape_string(htmlentities($_POST['titel'],$htmlent_flags,$htmlent_encoding_acp))."',
							newscatid		= '".$mysqli->escape_string($newscats_string)."',
							content			= '".$mysqli->escape_string($text)."',
							autozusammen	= '".$autozusammen."',
							zusammenfassung	= '".$mysqli->escape_string($zusammen)."',
							comments		= '".$comments."',
							hide_headline	= '".$mysqli->escape_string($_POST['hide_headline'])."',".$autorid_q."
							top				= '".$mysqli->escape_string($_POST['top'])."',
							hide_signature	= '".$mysqli->escape_string($_POST['hide_signature'])."',
							serialized_data = '".$mysqli->escape_string(serialize($ser_fieldarray))."'
							WHERE id = '".$mysqli->escape_string($_POST['id'])."'"))
				$saved = TRUE;
			else $saved = FALSE;
			}
		else{
			$flag_loginerror = true;
			}
		
		if($saved && $hide == 1){
			// Artikel / Seite wurde NUR zwischengespeichert
			echo "<p class=\"meldung_erfolg\">".$input_field['bezeichnung']." wurde <b>zwischengespeichert.</b><br />
					Ihre Eingaben wurden noch <b>nicht</b> ver&ouml;ffentlicht!<br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$_POST['id']."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<b><a href=\"".$filename."&amp;action=".$input_section2."&amp;do=publish&amp;id=".$_POST['id']."\">Jetzt ver&ouml;ffentlichen &raquo;</a></b></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		elseif($saved && $hide == 0){
			// Artikel / Seite wurde gespeichert UND ver�ffentlicht (sichtbar)
			echo "<p class=\"meldung_erfolg\"><b>".$input_field['bezeichnung']." wurde gespeichert &amp; ver&ouml;ffentlicht (wenn eine Freischaltung nicht mehr n&ouml;tig ist).</b><br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$_POST['id']."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<a href=\"".$filename."&amp;action=".$input_action2."\">".$input_field['next']." erstellen &raquo;</a></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		else
			echo "<p class=\"meldung_error\"><b>Es trat ein unvorhergesehener Fehler auf.<br />
					Bitte beachten Sie die MySQL-Fehlermeldung!</b></p>";
		}
	elseif(isset($_POST['do']) && $_POST['do'] == "update"){
		echo "<p class=\"meldung_error\"><b>Fehler: Sie haben nicht alle ben&ouml;tigten Felder
				(Titel und Textfeld) ausgef&uuml;llt!</b></p>";

		$form_data = _01article_getForm_DataArray();
		
		include_once($modulpath."write_form.php");
		}
	// Eintrag bearbeiten (Formular anzeigen)
	else{
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE id = '".$mysqli->escape_string($_REQUEST['id'])."'";
		switch($_REQUEST['static']){
		  case "0":
			if($userdata['editarticle'] == 2)
				$query .= " AND static = '0'";
			elseif($userdata['editarticle'] == 1)
				$query .= " AND uid = '".$userdata['id']."' AND static = '0'";
		  break;
		  case "1":
			if($userdata['staticarticle'] == 2)
				$query .= " AND static = '1'";
			elseif($userdata['staticarticle'] == 1)
				$query .= " AND uid = '".$userdata['id']."' AND static = '1'";
		  break;
		  }
		$query .= " LIMIT 1";
		
		// Werte aus DB holen
		$list = $mysqli->query($query);
		while($row = $list->fetch_assoc()){			
			$temp_uname = getUserdatafields($row['uid'],"username");
			$form_data = _01article_fillForm_DataArray($row);
			$form_data['username'] = $temp_uname['username'];
			}
		
		if($form_data['id'] > 0)
			include_once($modulpath."write_form.php");
		else
			$flag_loginerror = true;
		}

	}
	
	
	
	
	
	
	
	
	
// Artikel-�bersicht
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == "articles" || $_REQUEST['action'] == "statics") || $flag_overview){

if($flag_overview) $_REQUEST['action'] = $_REQUEST['who'];

	switch($_REQUEST['action']){
	  case "articles":
		$input_field['site_titel'] 	= "Artikel-&Uuml;bersicht / Artikel bearbeiten";
		$input_field['bezeichnung'] = "Artikel";
		$input_section				= "article";
		$input_action 				= "articles";
		$flag_static				= 0;
	  break;
	  case "statics":
		$input_field['site_titel'] 	= "Statische Seite (&Uuml;bersicht) / Bearbeiten";
		$input_field['bezeichnung']	= "Seite";
		$input_section				= "static";
		$input_action 				= "statics";
		$flag_static				= 1;
	  break;
	  }

	if(!isset($_GET['search'])) 	$_GET['search']		= "";
	if(!isset($_GET['sort'])) 		$_GET['sort']		= "";
	if(!isset($_GET['orderby'])) 	$_GET['orderby']	= "";
	
	$filename2 = $filename."&amp;action=".$input_action."&amp;serach=".$_GET['search']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."";
	
	// Artikel / Seiten freischalten
	if(isset($_GET['do']) && $_GET['do'] == "free" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
		if($userdata['editarticle'] == 2 && $flag_static == 0 || $userdata['staticarticle'] == 2 && $flag_static == 1)
		    $mysqli->query("UPDATE ".$mysql_tables['artikel']." SET frei='1' WHERE id='".$mysqli->escape_string($_GET['id'])."' AND static = '".$flag_static."' LIMIT 1");
		
		echo "<p class=\"meldung_erfolg\"><b>".$input_field['bezeichnung']." wurde freigeschaltet</b></p>";
		}
		
	// Artikel ver�ffentlichen
	if(isset($_GET['do']) && $_GET['do'] == "publish" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
		$mysqli->query("UPDATE ".$mysql_tables['artikel']." SET hide='0' WHERE id='".$mysqli->escape_string($_GET['id'])."' AND uid = '".$userdata['id']."' LIMIT 1");
		echo "<p class=\"meldung_erfolg\"><b>".$input_field['bezeichnung']." wurde ver&ouml;ffentlicht</b></p>";
		}

	// Auflistung
	
	// Kategorien z�hlen um ggf. auszublenden
	list($catmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['cats']."")->fetch_array(MYSQLI_NUM);
?>
<h1><?PHP echo $input_field['site_titel']; ?></h1>
<?PHP if($input_action == "statics"){ ?>
<p><a href="javascript:hide_unhide('includestatic');"><img src="images/icons/kreis_frage.gif" alt="Fragezeichen" title="Statische Seiten einbinden - so gehts!" /> Statische Seiten einbinden (PHP-Code anzeigen)</a></p>
<p class="meldung_hinweis" id="includestatic" style="display:none;">Statische Seiten k�nnen Sie mit folgendem PHP-Code einbinden:<br />
	<code>&lt;?PHP<br />
$subfolder		= "01scripts/"; // Unterverzeichnis<br />
$modul			= "<?PHP echo $modul; ?>";<br />
<br />
$show['<?PHP echo $names['artid']; ?>']	= <b class="red">ID</b> // ID der Seite, die angezeigt werden soll<br />
include($subfolder."01module/".$modul."/01article.php");<br />
?&gt;
	</code>
</p>
<?PHP } ?>
<form action="<?PHP echo $filename; ?>" method="get" style="float:left; margin-right:20px;">
	<input type="text" name="search" value="<?PHP echo $input_field['bezeichnung']; ?> suchen" size="30" onfocus="clearField(this);" onblur="checkField(this);" class="input_search" /> <input type="submit" value="Suchen &raquo;" class="input" />
	<input type="hidden" name="action" value="<?PHP echo $input_action; ?>" />
	<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
	<input type="hidden" name="loadpage" value="article" />
</form>
<?PHP
if($input_action == "articles" && $catmenge > 0){
$cat_data = array();
?>
<form action="<?PHP echo $filename; ?>" method="get">
	<select name="catid" size="1" class="input_select">
		<?PHP echo _01article_CatDropDown($cat_data); ?>
	</select>
	<input type="hidden" name="action" value="articles" />
	<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
	<input type="hidden" name="loadpage" value="article" />
	<input type="submit" value="Go &raquo;" class="input" />
</form>
<?PHP
	}
?>

<?PHP
	if((!isset($_GET['orderby']) || isset($_GET['orderby']) && empty($_GET['orderby'])) && (!isset($_GET['sort']) || isset($_GET['sort']) && empty($_GET['sort'])))
		$sortorder = "DESC";
	elseif(isset($_GET['sort']) && $_GET['sort'] == "desc") $sortorder = "DESC";
	else $sortorder = "ASC";
	
	if(isset($_GET['search']) && !empty($_GET['search']) && is_numeric($_GET['search'])) $where = " WHERE id = '".$mysqli->escape_string($_GET['search'])."' AND static = '".$flag_static."' ";
	elseif(isset($_GET['search']) && !empty($_GET['search'])) $where = " WHERE MATCH (titel,content,zusammenfassung) AGAINST ('".$mysqli->escape_string(parse_uml(str_replace("*","",$_GET['search'])))."') >= ".FULLTEXT_INDEX_SEARCH_SCHWELLE." AND static = '".$flag_static."' ";
	elseif(isset($_REQUEST['catid']) && !empty($_REQUEST['catid']) && is_numeric($_REQUEST['catid'])) $where = " WHERE newscatid LIKE '%,".$mysqli->escape_string($_REQUEST['catid']).",%' ";
	else $where = " WHERE static = '".$flag_static."' ";
	
	if($userdata['editarticle'] == 1 && $flag_static == 0 || $userdata['staticarticle'] == 1 && $flag_static == 1)
		$where .= " AND uid = '".$userdata['id']."' ";
		
	if($userdata['editarticle'] == 2 || $userdata['staticarticle'] == 2)
	    $where .= " AND (hide = '0' OR hide = '1' AND (uid = '".$userdata['id']."' OR uid = '0')) ";
	else
		$where .= " AND (hide = '0' OR hide = '1' AND uid = '".$userdata['id']."') ";

	switch($_GET['orderby']){
	  case "id":
	    $orderby = "id";
	  break;
	  case "status":
	    $orderby = "frei ASC, hide DESC, id";
	  break;
	  case "titel":
	    $orderby = "titel";
	  break;
	  default:
	    $orderby = "top DESC, utimestamp";
	  break;
	  }

	$sites = 0;
	$query = "SELECT * FROM ".$mysql_tables['artikel']."".$where." ORDER BY ".$orderby." ".$sortorder;
	$query = makepages($query,$sites,"site",ACP_PER_PAGE);
	
	// Fehlermeldung bei erfolgloser Suche
	if($sites == 0 && isset($_GET['search']) && !empty($_GET['search']))
		echo "<br /><p class=\"meldung_error\">Es konnten leider kein passenden Eintr&auml;ge zu Ihrer Sucheingabe \"".htmlentities($_GET['search'])."\" gefunden werden!<br />
			Bitte probieren Sie es erneut.</p>";

?>
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
		<td class="tra" align="center" style="width: 50px;"><b>ID</b>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;orderby=id&amp;sort=asc"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren (ASC)" /></a>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;orderby=id&amp;sort=desc"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<?PHP if($input_action == "articles" && $catmenge > 0){ ?>
		<td class="tra" align="center" style="width: 50px;"><b>Cat-ID</b></td>
		<?PHP } ?>
        <td class="tra" style="width: 110px;"><b>Datum / Zeit</b>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=asc&amp;orderby=timestamp"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=desc&amp;orderby=timestamp"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra" style="width: 200px;"><b>Status</b>			
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=asc&amp;orderby=status"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
		</td>
		<td class="tra"><b>Titel</b>			
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=asc&amp;orderby=titel"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=desc&amp;orderby=titel"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra"><b>Benutzer</b></td>
		<td class="tra" style="width: 25px;">&nbsp;<!--Bearbeiten--></td>
		<td class="tra" align="center" style="width: 25px;"><!--L�schen--><img src="images/icons/icon_trash.gif" alt="M&uuml;lleimer" title="Datei l&ouml;schen" /></td>
    </tr>
<?PHP
	if($userdata['editarticle'] == 1 && $flag_static == 0 || $userdata['staticarticle'] == 1 && $flag_static == 1)
		$artuserdata[$userdata['id']] = $userdata;
	else
		$artuserdata = getUserdatafields_Queryless("username");

	// Ausgabe der Datens�tze (Liste) aus DB
	$count = 0;

	$list = $mysqli->query($query);
	while($row = $list->fetch_assoc()){
		if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
		if($row['top'] == 1) $top = "* ";
		else $top = "";
		
		// Status-Bestimmung
		if($row['hide'] == 1)
			$status = "<b class=\"zwischengesp\">Zwischengespeichert</b>
						<a href=\"".$filename2."&amp;do=publish&amp;id=".$row['id']."\"><img src=\"images/icons/ok.gif\" alt=\"gr&uuml;ner Hacken\" title=\"Beitrag jetzt ver&ouml;ffentlichen\" /></a>";
		elseif($row['frei'] == 0){
			$status = "<b class=\"free_wait\">&Uuml;berpr&uuml;fung n&ouml;tig</b>";
			if($userdata['editarticle'] == 2 && $flag_static == 0 || $userdata['staticarticle'] == 2 && $flag_static == 1) $status .="<a href=\"".$filename2."&amp;do=free&amp;id=".$row['id']."\"><img src=\"images/icons/ok.gif\" alt=\"gr&uuml;ner Hacken\" title=\"Beitrag jetzt freischalten\" /></a>";
			}
		elseif($row['endtime'] > 0 && $row['endtime'] > time() && $row['utimestamp'] > time())
			$status = "<b class=\"public\">".date("d.m.y, G:i",$row['utimestamp'])." - ".date("d.m.y, G:i",$row['endtime'])."</b>";
    	elseif($row['endtime'] > 0 && $row['endtime'] > time())
			$status = "<b class=\"public\">Ver&ouml;ffentlicht bis ".date("d.m.Y, G:i",$row['endtime'])."</b>";
		elseif($row['endtime'] > 0 && $row['endtime'] < time())
			$status = "<b class=\"abgelaufen\">Abgelaufen seit ".date("d.m.Y, G:i",$row['endtime'])."</b>";
		elseif($row['endtime'] == 0 && $row['utimestamp'] < time())
			$status = "<b class=\"public\">Ver&ouml;ffentlicht</b>";
		elseif($row['entdime'] == 0 && $row['utimestamp'] > time())
			$status = "<b class=\"public\">Wird ver&ouml;ffentlicht</b>";
		
		// Kategorien
		$cats = "";
		if(isset($row['newscatid']) && $row['newscatid'] != "0" && $input_action == "articles" && $catmenge > 0){
			$newscatids_array = explode(",",substr($row['newscatid'],1,(strlen($row['newscatid'])-2)));
			foreach($newscatids_array as $cat)
				$cats .= "<a href=\"_loader.php?catid=".$cat."&action=articles&modul=".$modul."&loadpage=article\" title=\"".$cat_data[$cat]."\">".$cat."</a>, "; 
			}
		
		echo "    <tr id=\"id".$row['id']."\">
		<td class=\"".$class."\" align=\"center\">".$row['id']."</td>";
		if($input_action == "articles" && $catmenge > 0)
			echo "<td class=\"".$class."\" align=\"center\">".substr($cats,0,(strlen($cats)-2))."</td>";
		echo "
		<td class=\"".$class."\">".$top.date("d.m.Y - G:i",$row['utimestamp'])."</td>
		<td class=\"".$class."\" align=\"center\">".$status."</td>
		<td class=\"".$class."\" title=\"".strip_tags(substr($row['content'],0,300))."\" onmouseover=\"fade_element('copyid_".$row['id']."')\" onmouseout=\"fade_element('copyid_".$row['id']."')\">".$row['titel']." <div class=\"moo_inlinehide\" id=\"copyid_".$row['id']."\"><a href=\"".$filename."&amp;action=new".$input_section."&amp;copyid=".$row['id']."\"><img src=\"".$modulpath."images/icon_copy.gif\" alt=\"Kopieren\" title=\"Artikel zum Kopieren ausw&auml;hlen\" /></a></div></td>
		<td class=\"".$class."\">".$artuserdata[$row['uid']]['username']."</td>
		<td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;action=edit&amp;id=".$row['id']."&amp;catid=".$_REQUEST['catid']."&amp;static=".$row['static'].$add_filename."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Bearbeiten - Stift\" title=\"Eintrag bearbeiten\" style=\"border:0;\" /></a></td>
		<td class=\"".$class."\" align=\"center\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"DiesenEintrag l&ouml;schen\" class=\"fx_opener\" style=\"border:0; float:left;\" align=\"left\" /><div class=\"fx_content tr_red\" style=\"width:60px; display:none;\"><a href=\"#foo\" onclick=\"AjaxRequest.send('modul=".$modul."&ajaxaction=delarticle&id=".$row['id']."&static=".$flag_static."');\">Ja</a> - <a href=\"#foo\">Nein</a></div></td>
	</tr>";		
	}
	
	echo "</table>\n<br />";
	
	echo echopages($sites,"80%","site","action=".$input_action."&amp;search=".$_GET['search']."&amp;catid=".$_REQUEST['catid']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."");	
	}

}else $flag_loginerror = true;

?>