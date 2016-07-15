<?php

checkIndex();

if(!$USER->hasPerm('admin.calendar')): header("Location:".getURL()); exit; endif; ?>

<?php
while(true):
	$cid = $url->value(2); //KalenderID auslesen
	$pid = $url->value(3); //PostID auslesen
	
	$result = $mysql->execute("SELECT `versammlung`, `cid`, `meta` FROM `calendar` WHERE `cid` = ? LIMIT 1", 's', $cid);
	if($result == false || $result->num_rows != 1) break; //Abbruch wenn Kalender nicht existiert
	$result = $result->fetch_assoc();
	
	if($USER->vsid != $result['versammlung']) break; //Abbruch wenn keine Berechtigung
	
	/*
	 * Meta auslesen und erstellen
	 */
	
	if($result['meta'] != "") $meta = json_decode($result['meta'], true);
	else $meta = array();
	if(isset($meta[$pid]))	unset($meta[$pid]);
	if(!$mysql->execute("UPDATE `calendar` SET `meta` = ? WHERE `cid` = ?", 'ss', array(json_encode($meta), $cid)))		
		$log->write("Fehler beim Updaten der Kalendermeta von Kalender '".$cid."': ".$mysql->error(), 'error');
	
	
	header("Location:".getURL()."/".$url->value(0)."/editcal/".$cid);
	exit;

	break;
endwhile;

?>