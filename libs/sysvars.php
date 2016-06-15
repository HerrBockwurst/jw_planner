<?php
$config = array();

function initArray() {
	global $mysql, $config;
	$result = $mysql->query("SELECT * FROM `config`", true);
	while($row = $result->fetch_assoc()):
		$config[$row['conf']] = $row['value'];	
	endwhile;
	$mysql->free();
	var_dump($config);
}

?>