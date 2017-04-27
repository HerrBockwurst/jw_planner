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
		$String = "";
		foreach(\UserManager::getUserBy(array('vsid' => $VSID)) AS $cUser) {
			$String .= "
				<div class=\"UM-Card\">
					<div>{$cUser->Name}<span>({$cUser->UID})</span></div>
					<div>[(Common Versammlung)]: <span>{$cUser->VSName}</span></div>
					<div>[(Common Active)]: <span>{$cUser->Active}</span></div>
					<div>[(Common Email)]: <span>{$cUser->Email}</span></div>
					<div>[(Common Role)]: <span>{$cUser->RoleName}</span></div>
					<div>[(Common Groups)]: <span>3</span></div>
				</div>";
		}		
		return $String;
	}
	
	function ContentRequest() {
		switch(getURL(1)) {
			default:
				$html = new \HTMLTemplate('UserCards.html', $this->ClassPath);
				$html->replace(array('VERS' => $this->getVersSelect()));
				$html->replace(array('USER' => $this->getUserCards()));
				$html->replaceLangTag();
				$html->display();
		}
		
	}
}