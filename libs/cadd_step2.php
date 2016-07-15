<?php
if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

while(true):
	if($_POST['cid'] =='' || $_POST['name'] == '' || !isset($_POST['type'])): //Ausgabe wenn Formular nicht richtig ausgefüllt
		$ERROR['caladd'] = getLang('errors>emptyfields');
		break;
	endif;
	if(!in_array($_POST['type'], array("full", "selective", "once"))): //Fehler wenn Select Feld manipuliert wurde
		$ERROR['caladd'] = getLang('errors>emptyfields');
		break;
	endif;
	
	$cname = str_replace(' ', '-', utf8_decode($_POST['cid'])); // Replaces all spaces with hyphens.
	$cname = str_replace('ä', 'ae', $cname);
	$cname = str_replace('ö', 'oe', $cname);
	$cname = str_replace('ü', 'ue', $cname);
	$cname = str_replace('ß', 'ss', $cname);
	$cname = preg_replace('/[^A-Za-z\-]/', '', $cname);
	$cname = strtolower($cname);
	$cname = $USER->vsid."-".$cname;
	
	$result = $mysql->execute("SELECT `cid` FROM `calendar` WHERE `cid` = ?", 's', $cname);
	if($result->num_rows != 0):
		/*
		 * Error wenn Kalender ID schon vorhanden
		 */
		$ERROR['caladd'] = getLang('errors>cid_not_available');
		break;
	endif;
	
	$result = $mysql->execute("INSERT INTO `calendar` (`cid`, `name`, `type`, `versammlung`) VALUES (?,?,?,?)", 'ssss',
								array($cname,$_POST['name'],$_POST['type'],$USER->vsid));
	

	if($result === false):
		$ERROR['caladd'] = getLang('errors>mysql');
		$log->write("[".$USER->uid."]Fehler beim Hinzufügen vom Kalender: ".$mysql->error(), 'error');
		break;
	endif;	
	
	$SUCCESS['caladd'] = getLang('admin>c_add_success');
	
	break;
endwhile;
?>
