<?php
if(!defined('index')) exit;

class user {
	
	public $uid, $name, $email, $versammlung, $vsid; 
	
	private $perms;
	
	function __construct() {
		global $mysql;
		$this->perms = array();
		$this->auth();
	}
	
	private function auth() {
		global $bob, $mysql;
		
		$this->cleanupSessions();
		
		/*
		 * Wenn keine Session in Sessionvariable, dann Login
		 */
		if(!isset($_SESSION['sid'])):
			$this->maybeLogin();
			return false;
		endif;		
		
		/*
		 * Session von Server abfragen
		 */
		$result = $mysql->execute("SELECT * FROM sessions WHERE sid = ? LIMIT 1", 's', $_SESSION['sid']);
		
		if($result->num_rows != 1):
			unset($_SESSION['sid']);
			$this->maybeLogin();
		endif;
		$result = $result->fetch_assoc();
		
		/*
		 * Session Updaten
		 */
		
		if(!$mysql->execute("UPDATE sessions SET expire=? WHERE uid = ?", 'ss', array(getSQLDate(time() + (SESSIONTIME * 60)), $result['uid']))):
			//TODO Log
		endif;
		
		/*
		 * Userdaten laden
		 */
		$this->loadUserData($result['uid']);
		
	}
	
	private function loadUserData($uid) {
		global $mysql;
		
		$data = $mysql->execute("SELECT u.*, v.name AS vname, p.perms FROM user AS u
								INNER JOIN versammlungen AS v ON (u.vsid = v.vsid)
								INNER JOIN permissions AS p ON (u.uid = p.uid)
								WHERE u.uid = ? LIMIT 1", 's', $uid);
		$data = $data->fetch_assoc();

		
		$this->uid = $data['uid'];
		$this->name = $data['name'];
		$this->email = $data['email'];
		$this->vsid = $data['vsid'];
		$this->versammlung = $data['vname'];
		
		$this->perms = json_decode($data['perms']);
	}
	
	public function hasPerm($string) {
		if(in_array($string, $this->perms)) return true;
		return false;
	}
	
	public function getSubPerm($needle) {
		$retval = array();
		foreach($this->perms AS $perm):
			if(strpos($perm, $needle) !== false):
				$retval[] = $perm;
			endif;
		endforeach;
		if(empty($retval)) return false;
		return $retval;
	}
	
	private function cleanupSessions() {
		global $mysql;
		if(!$mysql->execute("DELETE FROM `sessions` WHERE `expire` <= ?", 's', getSQLDate())):
			//TODO Log
		endif;
	}
	
	private function maybeLogin() {
		global $bob;
		if(!defined('datahandler')):
			$bob->build(array(MODUL, 'login'));
			define('stopbob', true);
		endif;
	}
	
	public function getPerms() {
		return $this->perms;
	}
}

$user = new user();