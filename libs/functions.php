<?php

function getTitle() {
}

function printURL() {
	global $mysql;
	$result = $mysql->query("SELECT `url` FROM `config` LIMIT 1", true);
	echo $result[0];
}
?>