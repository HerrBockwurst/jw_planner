<?php
	if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

	$mysql->execute("DELETE FROM `sessions` WHERE `sid` = ?", "s", session_id());
	header("Location:".getURL());
	exit;
?>