<?php
namespace App;

class UserManagement extends \AModule {
	function __construct() {
		$this->PageID = 'usermanagement';
		$this->ClassPath = 'app/usermanagement';
		$this->MenuItem = new \MenuItem($this->PageID, getString('Menu UserManagement'), MENU_USER, 10);
		$this->CSSFile = 'style.css';
		$this->Scope = SCOPE_DESKTOP_APP;
		$this->Permission = 'admin.useredit';
	}
	
	private function getVersSelect() {
		$String = "";
		foreach(\User::getMyself()->getAccessableVers() AS $VSID => $VSName) {			
			$Selected = $VSID == \User::getMyself()->VSID ? 'selected' : ''; 
			$String .= "<option value=\"{$VSID}\" {$Selected}>{$VSName}</option>";
		}
		
		return $String;
	}
	
	private function getUserCards() {
		$VSID = isset($_POST['vsid']) ? $_POST['vsid'] : \User::getMyself()->VSID;
		$RetVal = array();
		foreach(\UserManager::getUserBy(array('vsid' => $VSID)) AS $cUser) {
			$Active = $cUser->Active == 1 ? getString('Common Yes') : getString('Common No');
			$RetVal[] = array(
					'uid' => $cUser->UID,
					'name' => $cUser->Name,
					'vsname' => $cUser->VSName,
					'active' => $Active,
					'email' => $cUser->Email,
					'role' => $cUser->RoleName,
					'groups' => 3
			);
		}		
		return json_encode($RetVal);
	}
	
	function ContentRequest() {
		switch(getURL(1)) {
			case 'updateuserlist':
				echo $this->getUserCards();
				break;
			case 'add':
				$html = new \HTMLTemplate('AddUser.html', $this->ClassPath);
				$html->replaceLangTag();
				$html->display();
				break;
			default:
				$html = new \HTMLTemplate('UserCards.html', $this->ClassPath);
				$html->replace(array('VERS' => $this->getVersSelect()));
				$html->replace(array('USER' => $this->getUserCards()));
				$html->replaceLangTag();
				$html->display();
		}
		
	}
}