<?php

function getTitle() {
}

function printURL() {
	global $mysql;
	$url = $mysql->query("SELECT * FROM `config` WHERE `conf` = 'url' LIMIT 1", true);
	$ssl = $mysql->query("SELECT * FROM `config` WHERE `conf` = 'ssl' LIMIT 1", true);
	if ($ssl['value'] == 'true'):
		echo "https://".$url['value'];
	else: 
		echo "http://".$url['value'];
	endif;
		
	
}

function getcss() {
	$browser = get_browser(null, true);
	print_r($browser);
	var_dump($browser);
	echo $browser['platform'];
}
?>