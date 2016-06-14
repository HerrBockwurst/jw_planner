<?php

function getTitle() {
}

function printURL() {
	global $mysql;
	$result = $mysql->doQuery("SELECT * FROM `config` WHERE `conf` = 'url' LIMIT 1", true);
	//echo $result[0];
}
?>