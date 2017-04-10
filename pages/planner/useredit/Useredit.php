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
	
	private function getUsers() { //Für sofortige Erstellung des UserData Arrays in UserList.html
		$UserString = '';
		$VSID = User::getMyself()->VSID;
		
		foreach(UserManager::getUsersBy(array('vsid' => $VSID)) AS $cRow) {
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
	
	private function getUsersByVers() { //Für nachträgliche Änderung des UserData Arrays in UserList.html
		$VSID = isset($_POST['vsid']) ? $_POST['vsid'] : User::getMyself()->VSID;
		if(!User::getMyself()->hasVSAccess($VSID)) returnErrorJSON(getString('errors noPerm')); //Keine Rechte für VS
		
		$UserString = array();
		
		foreach(UserManager::getUsersBy(array('vsid' => $VSID)) AS $cRow) {
			$Active = $cRow['active'] == 1 ? getString('Common Yes') : getString('Common No');
			if(is_null($cRow['role_name'])) $cRow['role_name'] = getString('Useredit NoRole');
			$UserString[] = array(
					'uid' => $cRow['uid'],
					'name' => $cRow['name'],
					'email' => $cRow['email'],
					'active' => $Active,
					'role' => $cRow['role_name'],
					'vs' => $cRow['vs_name']
			);			
		}
		
		$UserString = $UserString;
		echo json_encode($UserString);
	}
	
	public function myContent() {
		switch(getURL(2)) {
			case 'getusersbyvers':
				$this->getUsersByVers();
				break;
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