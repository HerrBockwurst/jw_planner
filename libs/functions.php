<?php

function getTitle() {
}

function printURL() {
	global $mysql;
	$result = $mysql->query("SELECT * FROM `config` WHERE `conf` = 'url' LIMIT 1", true);
	echo $result['value'];
}
?>