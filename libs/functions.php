<?php
if(!isset($fromIndex)) exit;

function checktime($hour, $min, $sec) {
	if ($hour < 0 || $hour > 23 || !is_numeric($hour)) return false;
	if ($min < 0 || $min > 59 || !is_numeric($min)) return false;
	if ($sec < 0 || $sec > 59 || !is_numeric($sec)) return false;
	return true;
}

function getIP($type='FULL') {
	
	if($type == 'FORWARD'):
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else return "";
	elseif($type == 'REMOTE'):
		return $_SERVER['REMOTE_ADDR'];
	else:
		$ip = $_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip .= "|".$_SERVER['HTTP_X_FORWARDED_FOR'];
	endif;
	return $ip;
}

function getSQLDate($date=NULL) {	
	global $CONFIG;
	if($date != NULL) intval($date);
	if($date == NULL) $date = time();
	$newdate = date("Y-m-d H:i:s",$date);
	return $newdate;
}


function displayMenuLink($langpath, $link) {
	global $lang;
	echo "<a href=\"".getURL().$link."\">".$lang->get($langpath)."</a>";	
}

function displayText($langpath, $before = "", $after = "") {
	global $lang;
	echo $before.$lang->get($langpath).$after;
}

function getLang($langpath) {
	global $lang;
	return $lang->get($langpath);
}

function getIfSet($var, $index=NULL) {
	if($index == NULL):
		if(isset($var)):
			return $var;
		endif;
	else:
		if(isset($var[$index]))
			return $var[$index];
	endif;
}

function printIfSet($var, $index=NULL) {
	echo getIfSet($var, $index);
}

function printURL() {
	/*
	 * Gibt URL mit ECHO aus
	 */
	global $CONFIG;
	if ($CONFIG['ssl'] == true):
		echo "https://".$CONFIG['home'];
	else: 
		echo "http://".$CONFIG['home'];
	endif;
}

function getURL() {
	/*
	 * Gibt Wert als RETURN zur�ck
	 */
	global $CONFIG;
	if ($CONFIG['ssl'] == true):
		return "https://".$CONFIG['home'];
	else:
		return "http://".$CONFIG['home'];
	endif;
}

function getcss() {
	
	if (isset($_COOKIE['nomobile']) && $_COOKIE['nomobile']):
		echo "desktop.css";
		return;
	endif;
	
	//http://detectmobilebrowsers.com/
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))):
		echo "mobile.css";
	else:
		echo "desktop.css";
	endif;
}

function printTitle($withVersion = false) {
	global $CONFIG;
	if($withVersion)
		echo $CONFIG['title']." | ".$CONFIG['version']." - ".$CONFIG['version_count'];
	else
		echo $CONFIG['title'];
}

function getVersArray() {
	global $mysql, $USER;
	$result = $mysql->execute("SELECT `id`, `name` FROM `versammlungen`");
	$versammlungen = array();
	$accessvs = $USER->getSubPerm('admin.useredit.vs.');
	
	if($USER->hasPerm('admin.useredit.global')):
		while($row = $result->fetch_assoc()) $versammlungen[$row['id']] = utf8_encode($row['name']);
	elseif($accessvs != false):
	
		while($row = $result->fetch_assoc()):
		foreach($accessvs as $vs)
			if($row['id'] == $vs) $versammlungen[$row['id']] = utf8_encode($row['name']);
		endwhile;
		if(!in_array($USER->versammlung, $versammlungen)): $versammlungen[$USER->vsid] = $USER->versammlung; endif;
	else:
		$versammlungen[$USER->vsid] = $USER->versammlung;
	endif;
	return $versammlungen;
}
?>