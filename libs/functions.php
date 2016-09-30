<?php
function getURL($int) {
	$url = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	if(!key_exists($int, $url)) return false;
	return $url[$int];
}

function checkURL($int, $search) {
	if(getURL($int) == $search) return true;
	return false;
}

function buildHeader() {
	global $content;
	$content->displayContent('header');
}

function getString($tree) {
	global $lang;
	return $lang->getValue($tree);
}

function displayString($tree) {
	echo getString($tree);
}

function returnErrorJSON($string) {
	echo json_encode(array('error' => $string));
	exit;
}
function loadSidebar() {
	global $content;
	
	echo "<ul>";
		$unsorted = $content->getAllContentBy('type', 'module');
		$sortOrdner = array('calendar', 'messages', 'calendaradmin', 'useradmin', 'system', 'logout');
		$sorted = array();
		
		for($i = 0; $i < count($sortOrdner); $i++) {
			reset($unsorted);
			while($cItem = current($unsorted)) {
				if($cItem->id == $sortOrdner[$i])
					$sorted[] = $cItem;
				next($unsorted);
			}
		}
		foreach($sorted AS $entry) {
			echo "<li data-id=\"$entry->id\">".getString("menu ".$entry->id)."</li>";
		}
	echo "</ul>";
	
}

function getVSAccess($xtraneedle = '') {
	global $user, $mysql;
	$vs = array();

	$vsperms = $user->getSubPerm($xtraneedle.'.vs.');

	if(!$vsperms) {
		$mysql->where('vsid', $user->vsid);
		$mysql->select('versammlungen', null, 1);
		$result = $mysql->fetchRow();
		$vs[$user->vsid] = $result->name;
	}
	else {
		$clearperms = array();
	
		foreach($vsperms AS $perm) {
			$tmp = explode('.', $perm);
			$clearperms[] = $tmp[count($tmp) - 1];
		}
		
		$mysql->select('versammlungen');
		$result = $mysql->fetchAll(); 
		if(!in_array('*', $clearperms)) {
			while($row = current($result)) { 
				if(in_array($row['vsid'], $clearperms)) {
					$vs[$row['vsid']] = utf8_encode($row['name']);
				}
				next($result);
			}
		} else {
			while($row = current($result)) {
				$vs[$row['vsid']] = utf8_encode($row['name']);
				next($result);
			}
		}
	}
	return $vs;
}