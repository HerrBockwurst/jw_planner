<?php

function getTitle() {
}

function printURL() {
	global $mysql;
	$url = $mysql->query("SELECT * FROM `config` WHERE `conf` = 'url' LIMIT 1", true);
	$ssl = $mysql->query("SELECT * FROM `config` WHERE `conf` = 'ssl' LIMIT 1", true);
	echo $ssl['value'];
	if(boolval($ssl['value']))
		echo "https://".$url['value'];
	else 
		echo "http://".$url['value'];
	
}

function getcss() {
	
}
?>