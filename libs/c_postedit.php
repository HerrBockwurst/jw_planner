<?php
if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

while(true):
	$cid = $url->value(2); //KalenderID auslesen
	
	$result = $mysql->execute("SELECT `versammlung`, `cid`, `meta` FROM `calendar` WHERE `cid` = ? LIMIT 1", 's', $cid);
	if($result == false || $result->num_rows != 1) break; //Abbruch wenn Kalender nicht existiert, Fehler wird später geworden in editcal.php
	$result = $result->fetch_assoc();
	
	if($USER->vsid != $result['versammlung']) break; //Abbruch wenn keine Berechtigung, Fehler wird später geworfen

	
	/*
	 * Meta auslesen und erstellen
	 */
	
	if($result['meta'] != "") $meta = json_decode($result['meta'], true);
	else $meta = array();
	
	/*
	 * Prüfen, ob Select Felder manipuliert wurden
	 */
	
	$validdata = array("type" => array("weekly","monthly"),
						"vis" => array("week", "month"),
						"tage" => array("monday","tuesday","wednesday","thursday","friday","saturday","sunday"),
						"m_every" => array("first","second","third","fourth") );
	
	if(!in_array($_POST['type'], $validdata['type']) ||
		!in_array($_POST['visibility2'], $validdata['vis']) ||
		!in_array($_POST['w_tag'], $validdata['tage']) ||
		!in_array($_POST['m_tag'], $validdata['tage']) ||
		!in_array($_POST['m_every'], $validdata['m_every']) ):
		$ERROR['postserror'] = getLang('errors>emptyfields');
		break;
	endif;
	
	/*
	 * Startdatum auf Gültigkeit prüfen
	 * Format: Array(T,M,J)
	 */
	$startdate = explode('.', $_POST['startdate']);
	
	if(count($startdate) != 3): $ERROR['postserror'] = getLang('errors>invalidNumFormat'); break; endif;
	
	foreach($startdate AS $key => $val) $startdate[$key] = intval(preg_replace('/\D/', '', $val));
	
	if(!checkdate($startdate[1],$startdate[0],$startdate[2])): //Format Monat, Tag, Jahr
		$ERROR['postserror'] = getLang('errors>invalidNumFormat');
		break;
	endif;
	
	/*
	 * Visbility
	 * Format Timestamp
	 */
	
	$vis = array(intval(preg_replace('/\D/', '', $_POST['visibility1'])), $_POST['visibility2']);
	if($vis[0] < 1 || $vis[0] > 4): //Prüfen ob Wert zwischen 1 und 4 liegt
		$ERROR['postserror'] = getLang('errors>posts_TooMuchVis');
		break;
	endif;
	
	$multipl = array("week" => 604800, "month" =>2592000);
	$vis = $vis[0] * $multipl[$vis[1]]; 
	
	/*
	 * Wenn Wöchtenlich
	 */
	
	if($_POST['type'] == 'weekly'):
		
		/*
		 * Startzeit prüfen
		 */
		
		if(strpos($_POST['w_from'], ":")): $from = explode(":", $_POST['w_from']);
		elseif(strpos($_POST['w_from'], ".")): $from = explode(".", $_POST['w_from']);
		else:
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
			
		foreach($from AS $key => $val) $from[$key] = intval(preg_replace('/\D/', '', $val));
		
		if(!checktime($from[0], $from[1], 0)):
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
		
		foreach($from AS $key=>$cfrom):
			$from[$key] = strval($cfrom); //Rückkonvertierung zu String
			if(strlen($from[$key]) == 1) $from[$key] = "0".$from[$key];
		endforeach;
		
		
		/*
		 * Endzeit Prüfen
		 */
		
		if(strpos($_POST['w_to'], ":")): $to = explode(":", $_POST['w_to']);
		elseif(strpos($_POST['w_to'], ".")): $to = explode(".", $_POST['w_to']);
		else:
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
			
		foreach($to AS $key => $val) $to[$key] = intval(preg_replace('/\D/', '', $val));
		
		if(!checktime($to[0], $to[1], 0)):
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
		
		foreach($to AS $key=>$cto):
			$to[$key] = strval($cto); //Rückkonvertierung zu String
			if(strlen($to[$key]) == 1) $to[$key] = "0".$to[$key];
		endforeach;
		
		/*
		 * Alles Gut, Erstellen des Meta eintrags
		 */
		
		$meta[uniqid(NULL, true)] = array("type" => "weekly",
						"startdate" => strtotime(implode(".", $startdate)),
						"visibility" => $vis,
						"patternA" => $_POST['w_tag'],
						"start" => implode(":", $from),
						"end" => implode(":", $to)
						);
		
		if(!$mysql->execute("UPDATE `calendar` SET `meta` = ? WHERE `cid` = ?", 'ss', array(json_encode($meta), $cid))):
			$ERROR['postserror'] = getLang('errors>mysql');
			$log->write("Fehler beim Updaten der Kalendermeta von Kalender '".$cid."': ".$mysql->error(), 'error');
			break;
		endif;
		
		$SUCCESS['posts'] = getLang('admin>post_added');
		
	else:
		/*
		 * Startzeit prüfen
		 */
		
		if(strpos($_POST['m_from'], ":")): $from = explode(":", $_POST['m_from']);
		elseif(strpos($_POST['m_from'], ".")): $from = explode(".", $_POST['m_from']);
		else:
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
			
		foreach($from AS $key => $val) $from[$key] = intval(preg_replace('/\D/', '', $val));
		
		if(!checktime($from[0], $from[1], 0)):
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
		
		foreach($from AS $key=>$cfrom):
			$from[$key] = strval($cfrom); //Rückkonvertierung zu String
			if(strlen($from[$key]) == 1) $from[$key] = "0".$from[$key];
		endforeach;
		
		
		/*
		 * Endzeit Prüfen
		 */
		
		if(strpos($_POST['m_to'], ":")): $to = explode(":", $_POST['m_to']);
		elseif(strpos($_POST['m_to'], ".")): $to = explode(".", $_POST['m_to']);
		else:
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
			
		foreach($to AS $key => $val) $to[$key] = intval(preg_replace('/\D/', '', $val));
		
		if(!checktime($to[0], $to[1], 0)):
			$ERROR['postserror'] = getLang('errors>invalidNumFormat');
			break;
		endif;
		
		foreach($to AS $key=>$cto):
			$to[$key] = strval($cto); //Rückkonvertierung zu String
			if(strlen($to[$key]) == 1) $to[$key] = "0".$to[$key];
		endforeach;
		
		/*
		 * Alles OK, Meta eintragen
		 */
	
		$meta[uniqid(NULL, true)] = array("type" => "monthly",
						"startdate" => strtotime(implode(".", $startdate)),
						"visibility" => $vis,
						"patternA" => $_POST['m_every'],
						"patternB" => $_POST['m_tag'],
						"start" => implode(":", $from),
						"end" => implode(":", $to)
						);
	
		if(!$mysql->execute("UPDATE `calendar` SET `meta` = ? WHERE `cid` = ?", 'ss', array(json_encode($meta), $cid))):
			$ERROR['postserror'] = getLang('errors>mysql');
			$log->write("Fehler beim Updaten der Kalendermeta von Kalender '".$cid."': ".$mysql->error(), 'error');
			break;
		endif;
		
		$SUCCESS['posts'] = getLang('admin>post_added');
	
		
	endif;
		
	
	break;
endwhile;
?>