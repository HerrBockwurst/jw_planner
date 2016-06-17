<?php
class user {
	public $username, $uid, $versammlung, $vsid, $email, $profpic;
	private $perms;
	
	function __construct() {
		$uid = $_SESSION['uid'];
		$this->perms = array();
		
		global $mysql;
		$result = $mysql->execute("SELECT u.`name`, p.`perm`, v.`name`, v.`anschrift`, u.`email`, u.`profilbild` FROM `users` AS u
									INNER JOIN `permissions` AS p ON p.`uid` = u.`uid`
									INNER JOIN `versammlungen` AS v ON u.`versammlung` = v.`id`
									WHERE u.`uid` = ?", 's', $uid);
		while($row = $result->fetch_assoc()):
			//foreach($row as $r) echo utf8_encode($r."<br>");
			
		endwhile;
	}
	
}