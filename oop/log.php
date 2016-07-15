<?php
if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

class log {
	function write($text,$loglevel='info') {
		global $mysql;
		if($loglevel != 'info' && $loglevel != 'warn' && $loglevel != 'error') return false; //Wenn ung�ltiges Loglevel
		
		$mysql->execute("INSERT INTO `log` (`time`, `type`, `text`) VALUES (CURRENT_TIMESTAMP, ?, ?)", "ss", array($loglevel, $text));
	}
}

$log = new log();
?>