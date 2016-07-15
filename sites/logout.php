<?php
	checkIndex();

	$mysql->execute("DELETE FROM `sessions` WHERE `sid` = ?", "s", session_id());
	header("Location:".getURL());
	exit;
?>