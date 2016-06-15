<?php
$config = array();
$calendar = array();

function initArray() {
	global $mysql, $config;
	$result = $mysql->query("SELECT * FROM `config`", true);
	while($row = $result->fetch_assoc()):
		if($row['value'] == 'true') $row['value'] = true;
		if($row['value'] == 'false') $row['value'] = false;
		$config[$row['conf']] = $row['value'];	
	endwhile;
	$mysql->free();
}

function initCalendar() {
	global $mysql, $calendar;
	$result = $mysql->query("SELECT * FROM `calendar`", true);
	while($row = $result->fetch_assoc()):
	
	endwhile;
	$mysql->free();
}

initArray();

?>