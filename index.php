<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Artikelsystem - Modul-Startseite (acp)
	#fv.321#
*/
?>

<div class="acp_startbox">
<p align="center"><b class="yellow"><?PHP echo $module[$modul]['instname']; ?></b></p>

<div class="acp_innerbox">
	<h4>Informationen</h4>
	<p>
	<b>Modul-Version:</b> <?PHP echo $module[$modul]['version']; ?><br /><br />
	
	<b>Artikel:</b> <?PHP list($artmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei = '1' AND hide = '0' AND static = '0'")->fetch_array(MYSQLI_NUM); echo $artmenge; ?><br />
	<b>statische Seiten:</b> <?PHP list($statmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei = '1' AND hide = '0' AND static = '1'")->fetch_array(MYSQLI_NUM); echo $statmenge; ?><br /><br />
	
	<b>Kategorien:</b> <?PHP list($catmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['cats']."")->fetch_array(MYSQLI_NUM); echo $catmenge; ?><br />
	<?PHP if($settings['comments']){ ?><b>Kommentare:</b> <?PHP list($commentsmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['comments']." WHERE frei = '1' AND modul = '".$modul."'")->fetch_array(MYSQLI_NUM); echo $commentsmenge; ?><br /><?PHP } ?>
	<br />
	
	<?PHP if($userdata['editarticle'] == 2){ ?><a href="_loader.php?modul=<?PHP echo $modul; ?>&amp;loadpage=article&amp;action=articles&amp;search=&amp;sort=asc&amp;orderby=status">&raquo; <?PHP list($artmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei = '0' AND hide = '0' AND static = '0'")->fetch_array(MYSQLI_NUM); echo $artmenge; ?> Artikel freischalten</a><br /><?PHP } ?>
	<?PHP if($userdata['staticarticle'] == 2){ ?><a href="_loader.php?modul=<?PHP echo $modul; ?>&amp;loadpage=article&amp;action=statics&amp;search=&amp;sort=asc&amp;orderby=status">&raquo; <?PHP list($artmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['artikel']." WHERE frei = '0' AND hide = '0' AND static = '1'")->fetch_array(MYSQLI_NUM); echo $artmenge; ?> Seiten freischalten</a><br /><?PHP } ?>
	<?PHP if($settings['comments'] && $userdata['editcomments'] == 1){ ?><a href="comments.php?modul=<?PHP echo $modul; ?>">&raquo; <?PHP list($commentsmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['comments']." WHERE frei = '0' AND modul = '".$modul."'")->fetch_array(MYSQLI_NUM); echo $commentsmenge; ?> Kommentare freischalten</a><?PHP } ?>
	</p>
</div>

<div class="acp_innerbox">
	<h4>5 neueste Artikel</h4>

	<?PHP
	$query = "SELECT id,titel,content FROM ".$mysql_tables['artikel']." WHERE frei = '1' AND hide = '0' AND static = '0' AND utimestamp < '".time()."' AND (endtime = '0' OR endtime >= '".time()."') ORDER BY utimestamp DESC LIMIT 5";
	$list = $mysqli->query($query);
	while($row = $list->fetch_assoc()){
		echo "<p><b><a href=\"_loader.php?modul=".$modul."&amp;loadpage=article&amp;action=edit&amp;id=".$row['id']."&amp;static=0\">".$row['titel']."</a></b><br />
		".substr(strip_tags($row['content']),0,100)."...
		</p>";
		}
	?>
</div>

<br />

<div class="acp_innerbox">
	<h4>Suche</h4>

<?PHP if($userdata['editarticle'] >= 1){ ?>
	<form action="_loader.php" method="get">
		<p><input type="text" name="search" value="Artikel suchen" size="20" onfocus="clearField(this);" onblur="checkField(this);" class="input_search" /> <input type="submit" value="Suchen &raquo;" class="input" /></p>
		<input type="hidden" name="action" value="articles" />
		<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
		<input type="hidden" name="loadpage" value="article" />
	</form>
<?PHP } ?>

<?PHP if($userdata['staticarticle'] >= 1){ ?>
	<form action="_loader.php" method="get">
		<p><input type="text" name="search" value="Seiten suchen" size="20" onfocus="clearField(this);" onblur="checkField(this);" class="input_search" /> <input type="submit" value="Suchen &raquo;" class="input" /></p>
		<input type="hidden" name="action" value="statics" />
		<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
		<input type="hidden" name="loadpage" value="article" />
	</form>
<?PHP } ?>

<?PHP if($catmenge > 0){ ?>
	<form action="_loader.php" method="get">
	<select name="catid" size="1" class="input_select">
		<?PHP echo _01article_CatDropDown(); ?>
	</select>
	<input type="hidden" name="action" value="articles" />
	<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
	<input type="hidden" name="loadpage" value="article" />
	<input type="submit" value="Go &raquo;" class="input" />
	</form>	
<?PHP } ?>
</div>

<div class="acp_innerbox">
	<h4>5 neueste Kommentare</h4>

	<?PHP
	$query = "SELECT postid,utimestamp,autor,message,smilies,bbc FROM ".$mysql_tables['comments']." WHERE modul = '".$modul."' AND frei = '1' ORDER BY utimestamp DESC LIMIT 5";
	$list = $mysqli->query($query);
	while($row = $list->fetch_assoc()){
		echo "<p><a href=\"comments.php?modul=".$modul."&amp;postid=".$row['postid']."\"><img src=\"images/icons/icon_show.gif\" alt=\"Symbol: Auge\" title=\"Kommentar anzeigen\" /></a> Verfasst von <b>".$row['autor']."</b> am <b>".date("d.m.Y",$row['timestamp'])."</b><br />
		".substr(strip_tags(bb_code_comment($row['message'],1,$row['bbc'],0)),0,100)."...
		</p>";
		}
	?>
</div>

</div>