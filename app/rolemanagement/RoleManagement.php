<?php
namespace App;

class RoleManagement extends \AModule {
	function __construct() {
		$this->PageID = 'rolemanagement';
		$this->ClassPath = 'app/rolemanagement';
		$this->MenuItem = new \MenuItem($this->PageID, getString('Menu RoleManagement'), MENU_USER, 20);
		$this->CSSFile = 'style.css';
		$this->Scope = SCOPE_DESKTOP_APP;
		$this->Permission = 'admin.rolemanager';
	}
	
	private function getVersSelect($PreSelected = NULL) {
		$String = "";
		foreach(\User::getMyself()->getAccessableVers() AS $VSID => $VSName) {
			$Selected = ($VSID == \User::getMyself()->VSID && is_null($PreSelected)) || $VSID == $PreSelected ? 'selected' : '';
			$String .= "<option value=\"{$VSID}\" {$Selected}>{$VSName}</option>";
		}
		
		return $String;
	}
	
	private function getRoles($Vers = NULL) {
		$Vers = is_null($Vers) ? \User::getMyself()->VSID : $Vers;
		if(!\User::getMyself()->hasVSAccess($Vers)) returnErrorJSON(getString('Errors noPerm'));
		
		$Roles = \RoleManager::getRoles($Vers);
		if(empty($Roles)) return getString('Admin noRolesAssigned');
		
		$String = "";
		
		foreach($Roles AS $cRole) 
			$String .= '<div class="RoleListEntry" data-rid="'.$cRole['rid'].'">'.$cRole['name'].'</div>';
		
		return $String;
	}
	
	private function getRoleData() {
		$Role = $_POST['rid'];
		
		$RoleData = \RoleManager::getRole($Role);
		$Permissions = json_decode($RoleData->entry);		
		
		$PermString = "";
		
		foreach(\User::getMyself()->getClearedPerms() AS $cPerm) {
			$Selected = in_array($cPerm, $Permissions) ? 'checked' : '';
			$PermString .= '<label><input type="checkbox" value="'.$cPerm.'" '.$Selected.' />'.getString('Permission '.$cPerm).'</label>';
		}
		
		$UserString = "";
		
		foreach(\UserManager::getUserBy(array('vsid' => $RoleData->vsid)) AS $cUser) { //Alle Benutzer auflisten mit Haken oder Ohne
			$Selected = $cUser->RID == $Role ? 'checked' : '';
			$UserString .= '<label><input type="checkbox" value="'.$cUser->UID.'" '.$Selected.' />'.$cUser->Name.'</label>';
		}
		
		echo json_encode(array(
				'perms' => $PermString,
				'users' => $UserString
		));
	}
	
	public function ContentRequest() {
		switch(getURL(1)) {
			case 'getroledata':
				$this->getRoleData();
				break;
			default:
				$HTML = new \HTMLTemplate('RoleOverview.html', $this->ClassPath);
				$HTML->replace(array(
						'VERS' => $this->getVersSelect(),
						'ROLELIST' => $this->getRoles()
				));
				$HTML->replaceLangTag();
				$HTML->display();
		}
	}
}