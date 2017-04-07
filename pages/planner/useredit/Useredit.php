<?php
class Useredit extends AppModule {
	private function __construct() {
		$this->ClassPath = 'useredit';
		$this->PageID = 'useredit';
		$this->Position = POS_PLANNER;
		$this->CSSFile = 'style.css';
		$this->MenuItem = new MenuItem(getString('Planner Menu Useredit'), PROTO.HOME.'/App/Useredit', 'user', 10);
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Useredit();
		return $Self;
	}
	
	private function getUsers() {
		$Filters = array();
		if(!isset($_POST['versammlungen.name'])) $_POST['versammlungen.name'] = User::getMyself()->VSName; 
		removeFilterInverse($_POST, array('uid', 'name', 'email', 'active', 'role', 'versammlungen.name', 'roles.name'));

		$UserString = '';
		
		foreach(UserManager::getUsersBy($_POST) AS $cRow) {
			$Active = $cRow['active'] == 1 ? getString('Common Yes') : getString('Common No');
			$UserString .= '{uid: "'.$cRow['uid'].'", name: "'.$cRow['name'].'", email: "'.$cRow['email'].'", active: "'.$Active.'", role: "'.$cRow['role_name'].'", vs: "'.$cRow['vs_name'].'"},';
		}
		
		return array('USERS' => substr($UserString, 0, -1));
	}
	
	private function getVers() {
		$String = '';
		foreach(User::getMyself()->getAccessableVers() AS $VSID => $Vers) {
			$Selected = User::getMyself()->VSID == $VSID ? 'selected' : '';
			$String .= '<option value="'.$VSID.'" '.$Selected.'>'.$Vers.'</option>';
		}
		return array('VERS' => $String);
	}
	
	public function myContent() {
		switch(getURL(2)) {
			default:
				$html = new HTMLContent('UserList.html', $this->ClassPath);
				$html->replaceLangTags();
				$html->replace($this->getUsers());
				$html->replace($this->getVers());
				$html->replace(array('VERS' => User::getMyself()->VSName));
				$html->display();
				break;
		}
	}
}