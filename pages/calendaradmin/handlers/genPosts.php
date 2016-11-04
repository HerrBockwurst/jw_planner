<?php
if(!isset($_POST)) returnErrorJSON('No Data given!');

global $mysql, $user;

$mysql->where('calendar.cid', $_POST['cid']);
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("calendar", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) exit;
if($mysql->fetchRow()->vsid != $user->vsid) exit;

$start = explode(".", $_POST['start']);
$end = explode(".", $_POST['end']);
if(!checkdate($start[1], $start[0], $start[2]) || !checkdate($end[1], $end[0], $end[2])) returnErrorJSON(getString('calendaradmin invalidGenTime'));

$start = strtotime($start[0].".".$start[1].".".$start[2]);
$end = strtotime($end[0].".".$end[1].".".$end[2]) + (60*60*24) - 1;

if($end <= $start) returnErrorJSON(getString('calendaradmin invalidGenTime'));

$startday = intval(date("d", $start)); 
$startmonth =  intval(date("m", $start));
$startyear =  intval(date("Y", $start));

/*
 * Pattern auslesen
 */

$mysql->where('cid', $_POST['cid']);
$mysql->select('pattern');
$result = $mysql->fetchAll();

$pattern = array();

foreach($result AS $currPatt) $pattern[$currPatt['day']][] = array("start" => $currPatt['start'], "end" => $currPatt['end'], "count" => $currPatt['count'], "patt_id" => $currPatt['patt_id'], );

/*
 * Existierende Einträge im Zeitraum auslesen
 */

$mysql->where('cid', $_POST['cid']);
$mysql->where('start', $start, ">=");
$mysql->where('end', $end, "<=");
$mysql->select('posts');

$result = $mysql->fetchAll();
$exclude = array();
foreach($result AS $currVal)
	$exclude[$currVal['start']] = $currVal['end'];

/*
 * Loopen
 */

$insertdata = array();
$someDeleted = false;

while($start < $end) {
	$dow = intval(date("w", $start)) == 0 ? 7 : intval(date("w", $start));
	
	if(!isset($pattern[$dow])) {
		$start = $start + (60*60*24);
		continue;
	}
	
	foreach($pattern[$dow] AS $currPatt) {
		
		$startstamp = $start + ($currPatt['start'] * 60);
		$endstamp = $start + ($currPatt['end'] * 60);
		
		$spacer = 0; $stopIt = false;
		
		while($startstamp + $spacer <= $endstamp) {
			if(isset($exclude[$startstamp + $spacer])) {
				$stopIt = true;
				$someDeleted = true;
			}
			$spacer = $spacer + (15*60);
		}
		
		if($stopIt) continue;
		
		$insertdata[] = array(
				'start' => $startstamp,
				'end' => $endstamp,
				'count' => $currPatt['count'],
				'cid' => intval($_POST['cid']),
				'entrys' => "[]"
		);
	}
	
	$start = $start + (60*60*24);
}

foreach($insertdata AS $currInsert) if(!$mysql->insert('posts', $currInsert)) returnErrorJSON(getString('errors sql'));

if($someDeleted) echo json_encode(array("warn" => getString('calendaradmin someDeleted'), "success" => getString('calendaradmin postsCreated')));
else echo json_encode(array("success" => getString('calendaradmin postsCreated')));