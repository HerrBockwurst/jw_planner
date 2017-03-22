<?php
class Profile extends Module {
	private function __construct() {
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'profile';
		$User = explode(" ", User::getInstance()->Clearname);
		$this->MenuItem = new MenuItem($User[0], 10, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Profile();
			return $Instance;
	}
	
	private function Handler_saveProfile() {
		$Mail = $_POST['mail'];
		$PW = $_POST['pw'];
		if(!validateEmail($Mail)) returnErrorJSON(getString('errors EmailSyntax'));
		
		$Data = array('email' => $Mail);
		if(!empty($PW)) $Data['password'] = password_hash($PW, PASSWORD_DEFAULT);
		User::getInstance()->updateMe($Data);
		
		echo json_encode(array());
	}
	
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'saveProfile':
				$this->Handler_saveProfile();
				break;
			default:
				break;
		}
	}
	
	public function ActionLoad() {
		$Data = array(
				'NAME' => User::getInstance()->Clearname,
				'MAIL' => User::getInstance()->Mail
		);
		
		echo replaceLangTags(replacer(loadHtml('Profile.html', $this->ClassPath), $Data));
	}
	
	public function ActionSite() {
	
	}
}