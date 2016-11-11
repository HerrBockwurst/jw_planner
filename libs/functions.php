<?php
function stringToColorCode($str) {
	$code = dechex(crc32($str));
	$code = substr($code, 0, 6);
	$code = hexdec(substr($code ,0,2)).",".hexdec(substr($code ,2,2)).",".hexdec(substr($code ,4,2)).",0.5";
	
	return $code;
}
function repUmlaute($str): string {
	$string = $str;
	$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´");
	$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "");
	while(current($search)) { 
		$string = preg_replace('/'.utf8_encode(current($search)).'/', $replace[key($search)], $string);
		next($search);
	}
	return $string;
}

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
		$sortOrdner = array('calendar', 'messages', 'calendaradmin', 'useradmin', 'groups', 'system', 'feedback', 'logout');
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

function createTime($string): int {
	/*
	 * Gibt Minuten seit Mitternacht zurück
	 */
	$a = explode(":", $string);
	$h = intval($a[0]);
	$m = intval($a[1]);
	
	return ($h * 60) + $m;
}

function getPatternByDay($day, $cid) {
	global $mysql;
	
	$mysql->where('cid', $cid);
	$mysql->where('day', $day);
	$mysql->orderBy('start');
	$mysql->select('pattern');
	
	$result = $mysql->fetchAll();
	
	foreach($result AS $currPatt) {
		$patt = $currPatt['patt_id'];
		$start = substr("0".floor($currPatt['start']/60), -2).":".substr("0".$currPatt['start']%60, -2);
		$end = substr("0".floor($currPatt['end']/60), -2).":".substr("0".$currPatt['end']%60, -2);
		$count = $currPatt['count']." ".substr(getString('common count'), 0, 1).".";
		echo <<<EOF
<div class="post clickable floatbreak" data-patt="$patt">
	<div style="float: left;">
		<span>$start</span>
		<span>$end</span>
	</div>
	<span style="line-height: 32px">$count</span>
</div>
EOF;
	}
}
