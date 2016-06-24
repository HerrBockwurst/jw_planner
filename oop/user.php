<?php
class user {
	public $username, $uid, $versammlung, $vsid, $email, $profpic;
	private $perms;
	
	function __construct() {
		$this->uid = $_SESSION['uid'];
		$this->perms = array();
		
		global $mysql;
		$result = $mysql->execute("SELECT u.`name` AS `username`, p.`perm`, v.`name` AS `vsname`, v.`anschrift`, v.`id` AS `vsid`, u.`email`, u.`profilbild` 
									FROM `users` AS u
									INNER JOIN `permissions` AS p ON p.`uid` = u.`uid`
									INNER JOIN `versammlungen` AS v ON u.`versammlung` = v.`id`
									WHERE u.`uid` = ?", 's', $this->uid);

		while($row = $result->fetch_assoc()):
			
			/*
			 * Permissions auslesen
			 */
			if(isset($row['perm']) && !in_array($row['perm'], $this->perms)) $this->perms[] = $row['perm'];
			
			/*
			 * Nutzerdaten auslesen
			 */
			if(isset($row['username']) && !isset($this->username)) $this->username = $row['username'];
			if(isset($row['vsname']) && !isset($this->versammlung)) $this->versammlung = $row['vsname'];
			if(isset($row['vsid']) && !isset($this->vsid)) $this->vsid = $row['vsid'];
			if(isset($row['email']) && !isset($this->email)) $this->email = $row['email'];
			if(isset($row['profilbild']) && !isset($this->profpic)) $this->profpic = $row['profilbild'];
			
		endwhile;
		
		
	}
	
	function countPerms() {
		return count($this->perms);		
	}
	
	function hasPerm($perm) {
		if(in_array($perm, $this->perms)) return true;
		else return false;
	}
	
	function addPerm() {
		
	}
	
	function getPerms() {
		return $this->perms;
	}
	
	function getSubPerm($search) {
		$returnval = array();
		foreach($this->perms AS $perm):
			if(strpos($perm, $search) !== false) $returnval[] = substr($perm, strlen($search));
		endforeach;
		if(empty($returnval)) return false;
		return $returnval;
	}
	
	
}