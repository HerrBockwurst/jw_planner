<?php
class user {
	public $username, $uid, $versammlung, $vsid;
	private $perms;
	
	function __construct() {
		$uid = $_SESSION['uid'];
		
		global $mysql;
		//$result = $mysql->execute("SELECT `users`.`name` FROM `users` INNER JOIN `` ON")
	}
	
}