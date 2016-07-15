<?php
checkIndex();

while(true):

	$cid = $url->value(2); //KalenderID auslesen

	$result = $mysql->execute("SELECT `versammlung`, `cid` FROM `calendar` WHERE `cid` = ? LIMIT 1", 's', $cid);
	if($result == false || $result->num_rows != 1) break; //Abbruch wenn Kalender nicht existiert, Fehler wird später geworden in editcal.php
	$result = $result->fetch_assoc();
	
	if($USER->vsid != $result['versammlung']) break; //Abbruch wenn keine Berechtigung, Fehler wird später geworfen
	
	
	if(isset($_POST['delete'])):
		/*
		 * TODO Löschen von eingetragenen Terminen
		 */
	
		if(!$mysql->execute("DELETE FROM `calendar` WHERE `cid` = ?", 's', $cid)): //Löschvorgang
			$ERROR['editcal'] = getLang('errors>mysql');
			$log->write("Fehler beim Löschen von Kalender '".$cid."': ".$mysql->error(), 'error');
			break;
		endif;
		
		$SUCCESS['editcal'] = getLang('admin>cal_deleted');
		$noform = true;
		
		break;
			
	endif;
	
	if($_POST['name'] == ''):
		$ERROR['editcal'] = getLang('errors>emptyfields'); //Wenn Feld leergelassen wurde
		break;
	endif;
	
	if(!$mysql->execute("UPDATE `calendar` SET `name` = ? WHERE `cid` = ?", 'ss', array($_POST['name'], $cid))):
		$ERROR['editcal'] = getLang('errors>mysql');
		$log->write("Fehler beim Updaten von Kalender '".$cid."': ".$mysql->error(), 'error');
		break;
	endif;
	
	$SUCCESS['editcal'] = getLang('admin>cal_updated');	
	break;
	
endwhile;
?>