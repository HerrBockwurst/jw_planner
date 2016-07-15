<?php
	if(!isset($fromIndex)) exit;

	$mysql->execute("DELETE FROM `sessions` WHERE `sid` = ?", "s", session_id());
	header("Location:".getURL());
	exit;
?>