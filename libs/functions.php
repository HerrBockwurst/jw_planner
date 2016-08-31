<?php
if(!defined('index')) exit;

function getURL($int) {
	$url = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	if(!key_exists($int, $url)) return false;
	return $url[$int]; 
}

function checkURL($int, $search) {
	if(getURL($int) == $search) return true;
	return false;
}

function displayString($tree) {
	global $lang;
	echo $lang->get($tree);
	return;
}

function getString($tree) {
	global $lang;
	return strval($lang->get($tree));
}

function registerModul($data) {
	global $ModulHandler;
	$ModulHandler->registerModul($data);
}

function checkModul($name) {
	global $ModulHandler;
	return $ModulHandler->check($name);
}

function loadModules() {
	/*
	 * modules Ordner durchsuchen und index in jeden unterordner starten
	 */
	if($handle = opendir('modules')):
		while(false !== ($entry = readdir($handle))):
			if($entry != '.' && $entry != '..' && is_dir('modules/'.$entry)):
				if(file_exists('modules/'.$entry.'/register.php'))
					require_once 'modules/'.$entry.'/register.php';
			endif;
		endwhile;
		closedir($handle);
	endif;
}
function destruct($var) {
	$var->__destruct();
}

function addDataHandler($path) {
	global $DataHandler;
	if(!isset($DataHandler)) $DataHandler = array();
	
	if($path[0] == 1) $path[0] = 'modules/'; 
	elseif($path[0] == 2) $path[0] = 'pages/';
	
	$DataHandler[$path[1]] = $path[0].$path[2];
}

function getDataHandler($id) {
	global $DataHandler;
	if(!key_exists($id, $DataHandler)) return false;
	
	require_once $DataHandler[$id];
}

function displayHandlerURL($name) {
	echo PROTO.HOME.'/ajax/datahandler/'.$name;
}

function getSQLDate($date=NULL) {
	if($date != NULL) intval($date);
	if($date == NULL) $date = time();
	$newdate = date("Y-m-d H:i:s",$date);
	return $newdate;
}

function getVSArray() {
	global $user, $mysql; 
	$vs = array();
	
	$vsperms = $user->getSubPerm('.vs.');
	
	if(!$vsperms): $vs[$user->vsid] = $user->versammlung;
	else:
		$clearperms = array();
		
		foreach($vsperms AS $perm):
			$tmp = explode('.', $perm);
			$clearperms[] = $tmp[count($tmp) - 1];
		endforeach;
		
		$result = $mysql->execute("SELECT * FROM versammlungen");
		$result = $result->fetch_all(MYSQLI_ASSOC);
		
		while($row = current($result)):
				if(!in_array($row['vsid'], $clearperms) && !in_array('*', $clearperms)):
				unset($result[key($result)]);
			else:
				$vs[$row['vsid']] = utf8_encode($row['name']);
				next($result);
			endif;
		endwhile;
	
	endif;
	
	return $vs;
}

function deleteOldPosts() {
	global $mysql;
	
	$result = $mysql->execute("SELECT pid FROM posts WHERE expire <= ?", 'i', time());
	if($result->num_rows == 0) return;
	$result = $result->fetch_all(MYSQLI_ASSOC);
	
	foreach($result AS $row):
		$mysql->execute("DELETE FROM posts WHERE pid = ?", 'i', $row['pid']);
		$mysql->execute("DELETE FROM entrys WHERE pid = ?", 'i', $row['pid']);
	endforeach;
}

function in_array_r($needle, $haystack, $strict = false) {
	foreach ($haystack as $item) {
		if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
			return true;
		}
	}

	return false;
}

function getTooltip($pid) {
	global $mysql, $user;
	
	$tooltip = $mysql->execute("SELECT e.*, u.name FROM entrys AS e INNER JOIN user AS u ON (e.uid = u.uid) WHERE pid = ?", 'i', $pid);
	if($tooltip->num_rows == 0):
		$tooltipstring = getString('calendar>no_entrys_applied');
	else:
		$tooltip = $tooltip->fetch_all(MYSQLI_ASSOC);
		$tooltipstring = "";
		foreach($tooltip AS $entry):
			$tooltipstring .= "
						<div class=\"tooltip_count_row floatbreak relative\">
							<div class=\"pic\">".strtoupper(substr($entry['name'], 0, 1))."</div>
							<div class=\"text\">".$entry['name']."</div>
							";
		if($user->hasPerm('calendar.admin') || $entry['uid'] == $user->uid):
			$tooltipstring .= "
			<div class=\"deleteentry clickable\" onclick=\"deleteentry(".$entry['eid'].")\">
				<img src=\"".PROTO.HOME."/images/postdelete.png\" />
			</div>";
		endif;
		$tooltipstring .= "</div>";
		endforeach;
	endif;

	return strval($tooltipstring); 
}