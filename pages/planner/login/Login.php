<?php
class Login extends AppModule {
	private function __construct() {
		$this->ClassPath = 'login';
		$this->PageID = 'login';
		$this->Position = POS_PLANNER;
		$this->IsDefaultPage = TRUE;
		$this->CSSFile = 'style.css';
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Login();
		return $Self;
	}
	

	private function removeBans() {
		$MySQL = MySQL::getInstance();
		$MySQL->where('time', time() - (BANTIME * 60), '<');
		$MySQL->delete('loginfails');
	}
	
	private function Handler_Login() {
		if(!testAjax()) {
			displayString('Login GreetingsJava');
			return;
		}
		
		$this->removeBans();
		
		$MySQL = MySQL::getInstance();
		
		if(empty($_POST['username']) || empty($_POST['password'])) returnErrorJSON(getString('Errors WrongFields')); //Ein Feld leergelassen
	
		$MySQL->where('ip', $_SERVER['REMOTE_ADDR']);
		if($MySQL->count('loginfails') >= MAX_LOGIN_TRY) returnErrorJSON(preg_replace('/{R}/', BANTIME, getString('Errors LoginBan'))); //für Login gesperrt
	
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
			returnErrorJSON(getString('Errors AuthFail')); //Benutzername nicht gefunden
		}
	
		//Passwortcheck
		if(!password_verify($_POST['password'], $Result->password)) {
			$MySQL->insert('loginfails', array(
					'ip' => $_SERVER['REMOTE_ADDR'],
					'time' => time(),
					'user' => $_POST['username']
			));
			returnErrorJSON(getString('Errors AuthFail'));
		}
	
		//Account gesperrt
		if($Result->active != 1) returnErrorJSON(getString('Errors AccountLocked'));
	
		//Ab hier Auth erfolgreich, Session setzen
		if(!$MySQL->insert('sessions', array('sid' => session_id(), 'uid' => strval($Result->uid), 'expire' => time() + SESSIONTIME)))
			returnErrorJSON(getString('Errors sql'));
			
	}
	
	public function myContent() {
		switch(getURL(2)) {
			case 'dologin':
				$this->Handler_Login();
				break;
			default:
				if(User::getMyself()->IsLoggedIn) //Umleiten wenn Benutzer schon eingeloggt ist
					if(testAjax()) echo json_encode(array('redirect' => PROTO.HOME.'/Start')); 
					else header("Location:".PROTO.HOME."/App");
				$html = new HTMLContent('Login.html', $this->ClassPath);				
				$html->replaceLangTags();
				$html->display();
				break;
		}
	}
}