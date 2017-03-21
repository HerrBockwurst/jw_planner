<?php
class Login extends Module {
	private function __construct() {
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'login';
		$this->isPublic = TRUE;
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Login();
			return $Instance;
	}
	
	private function Handler_Login() {
		$MySQL = MySQL::getInstance();
		
		if(empty($_POST['username']) || empty($_POST['password'])) returnErrorJSON(getString('errors WrongFields')); //Ein Feld leergelassen
		
		$MySQL->where('ip', $_SERVER['REMOTE_ADDR']);
		if($MySQL->count('loginfails') >= MAX_LOGIN_TRY) returnErrorJSON(preg_replace('/{R}/', BANTIME, getString('errors LoginBan'))); //für Login gesperrt
		
		$MySQL->where('uid', $_POST['username']);
		$MySQL->where('email', $_POST['username'], '=', 'OR');
		$MySQL->select('users', NULL, 1);
		
		$Result = $MySQL->fetchRow();
		
		if(!$Result) {
			$MySQL->insert('loginfails', array(
					'ip' => $_SERVER['REMOTE_ADDR'],
					'time' => time(),
					'user' => $_POST['username']
			));
			returnErrorJSON(getString('errors AuthFail')); //Benutzername nicht gefunden
		}
		
		//Passwortcheck
		$iPasswort = hash('sha512', $_POST['password'].SALT);
		if($iPasswort != $Result->password) {
			$MySQL->insert('loginfails', array(
					'ip' => $_SERVER['REMOTE_ADDR'],
					'time' => time(),
					'user' => $_POST['username']
			));
			returnErrorJSON(getString('errors AuthFail'));
		}
		
		//Account gesperrt
		if($Result->active != 1) returnErrorJSON(getString('errors AccountLocked'));
		
		//Ab hier Auth erfolgreich, Session setzen
		$MySQL->insert('sessions', array('sid' => session_id(), 'uid' => strval($Result->uid), 'expire' => time() + (60*SESSIONTIME)));
	}
	
	public function ActionDataHandler() {
		$this->Handler_Login();
	}
	
	public function ActionLoad() {
		printHtml('Mask.html', $this->ClassPath);
	}
	
	public function ActionSite() {
		
	}
}