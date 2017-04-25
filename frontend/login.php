<?php
namespace Frontend;

class Login extends \AModule {
	function __construct() {
		$this->PageID = 'login';
		$this->ClassPath = 'frontend';
	}
	
	private function doLogin() {
		//Daten nicht vollständig übergeben
		if(!isset($_POST['username']) || !isset($_POST['password'])) returnErrorJSON(getString('Errors WrongFields'));
		
		//Teste Bans
		if(\SessionManager::getBans() >= MAX_LOGIN_TRY) returnErrorJSON(preg_replace('/{R}/', BANTIME, getString('Errors LoginBan'))); //Benutzer nicht gefunden.
		
		$User = new \User($_POST['username']);
		if($User->Valid == FALSE) returnErrorJSON(getString('Errors AuthFail')); //Benutzer nicht gefunden.		
		
		if(!password_verify($_POST['password'], $User->Password)) returnErrorJSON(getString('Errors AuthFail')); //Passwort Stimmt nicht.
		
		\SessionManager::addSession($User->UID);
		echo json_encode(array('redirect' => PROTO.'app.'.HOME));
	}
	
	function ContentRequest() {
		switch(getURL(1)) {
			case 'dologin':
				$this->doLogin();
				break;
			default:
				$HTML = new \HTMLTemplate("loginmask.html", "{$this->ClassPath}/html");
				$HTML->replaceLangTag();
				$HTML->display();
				break;
		}
		
	}
}