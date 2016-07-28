<?php
if(!defined('index')) exit;

class user {
	
	public $uid, $name, $email, $versammlung, $vsid; 
	
	private $perms;
	
	function __construct() {
		global $mysql;
		$this->auth();				
	}
	
	private function auth() {
		global $bob, $mysql;
		
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
			$bob->build('login');
		endif;
		
	}
	
	private function maybeLogin() {
		global $bob;
		if(!defined('datahandler')):
			$bob->build(array(MODUL, 'login'));
			define('stopbob', true);
		endif;
	}
}

$user = new user();