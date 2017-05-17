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
			$String .= '<div class="RoleListEntry" data-rid="'.$cRole['rid'].'">'.$cRole['name'].'<span class="RoleListEntryDelete">X</span></div>';
		
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
	
	private function addRole() {
		$RoleName = $_POST['name'];
		$VSID = $_POST['vsid'];
		
		if(!\User::getMyself()->hasVSAccess($VSID)) returnErrorJSON(getString('Errors noPerm'));
		
		\RoleManager::addRole($RoleName, $VSID);
		echo json_encode(array('roles' => $this->getRoles($VSID)));
	}
	
	private function delRole() {
		$RID = $_POST['rid'];
		$Role = \RoleManager::getRole($RID);
		if(!$Role) returnErrorJSON(getString('Errors invalidInput'));
		if(!\User::getMyself()->hasVSAccess($Role->vsid)) returnErrorJSON(getString('Errors noPerm'));
		
		\RoleManager::deleteRole($RID);
		echo json_encode(array('roles' => $this->getRoles($Role->vsid)));
	}
	
	private function updateRole() {
		$RID = $_POST['rid'];
		$Users = isset($_POST['users']) ? $_POST['users'] : Array();
		$Perms = isset($_POST['perms']) ? $_POST['perms'] : Array();		
		
		$Role = \RoleManager::getRole($RID);
		if(!$Role) returnErrorJSON(getString('Errors invalidInput'));
		if(!\User::getMyself()->hasVSAccess($Role->vsid)) returnErrorJSON(getString('Errors noPerm'));
		
		$CurrentPerms = json_decode($Role->entry);
		$PermsToToggle = array();
		
		foreach($Perms AS $Perm => $Checked) { //Permissions aussortieren, die zwar im Formular geändert, aber wieder zurück gesetzt wurden
			if(($Checked == 1 && in_array($Perm, $CurrentPerms)) || ($Checked == 0 && !in_array($Perm, $CurrentPerms))) continue; 
			$PermsToToggle[] = $Perm;
		}
		
		if(!empty($PermsToToggle)) \RoleManager::togglePermission($PermsToToggle, $RID); //Rollen Toggeln
		
		foreach($Users AS $UID => $Checked) {
			$User = new \User($UID);
			if(!$User->Valid) returnErrorJSON(getString('Errors invalidUser'));
			
			if($Checked == 1)
				\UserManager::editUser($UID, array('role' => $RID));
			elseif($Checked == 0 && $User->RID == $RID)
				\UserManager::editUser($UID, array('role' => 0));
		}
		echo json_encode(array());
	}
	
	public function ContentRequest() {
		switch(getURL(1)) {
			case 'updaterole':
				$this->updateRole();
				break;
			case 'getroles':
				echo json_encode(array('roles' => $this->getRoles($_POST['vsid'])));
				break;				
			case 'delrole':
				$this->delRole();
				break;
			case 'addrole':
				$this->addRole();
				break;
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