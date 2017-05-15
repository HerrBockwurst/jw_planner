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
	
	private function getVersSelect($PreSelected = NULL) {
		$String = "";
		foreach(\User::getMyself()->getAccessableVers() AS $VSID => $VSName) {			
			$Selected = ($VSID == \User::getMyself()->VSID && is_null($PreSelected)) || $VSID == $PreSelected ? 'selected' : ''; 
			$String .= "<option value=\"{$VSID}\" {$Selected}>{$VSName}</option>";
		}
		
		return $String;
	}
	
	private function getUserCards() {
		$VSID = isset($_POST['vsid']) ? $_POST['vsid'] : \User::getMyself()->VSID;
		$RetVal = array();
		foreach(\UserManager::getUserBy(array('vsid' => $VSID)) AS $cUser) {
			$Active = $cUser->Active == 1 ? getString('Common Yes') : getString('Common No');
			$RoleName = is_null($cUser->RoleName) ? getString('Admin noRole') : $cUser->RoleName;
			$RetVal[] = array(
					'uid' => $cUser->UID,
					'name' => $cUser->Name,
					'vsname' => $cUser->VSName,
					'active' => $Active,
					'email' => $cUser->Email,
					'role' => $RoleName,
					'groups' => 3
			);
		}		
		return json_encode($RetVal);
	}
	
	private function getPermOptions() {
		$Perms = \User::getMyself()->getClearedPerms();
		$String = "";
		foreach($Perms AS $cPerm)
			$String .= "<option value=\"{$cPerm}\">[(Permission {$cPerm})]</option>";
		
		return $String;
	}
	
	private function getRoles($VSID = NULL, $ECHO = TRUE) {
		if(!is_null($VSID)) 			$Vers = $VSID;
		elseif(isset($_POST['vs'])) 	$Vers = $_POST['vs'];
		else 							$Vers = \User::getMyself()->VSID;
		if(!\User::getMyself()->hasVSAccess($Vers)) returnErrorJSON(getString('Errors noPerm'));
		
		$RoleList = array();
		$RoleList[] = array('value' => '0', 'text' => getString('Admin noRole'));
		
		foreach(\RoleManager::getRoles($Vers) AS $cRole) 
			$RoleList[] = array('value' => $cRole['rid'], 'text' => $cRole['name']); 
		
		if($ECHO) echo json_encode($RoleList);
		else return json_encode($RoleList);
	}
	
	private function getGroups($VSID = NULL, $ECHO = TRUE) {
		if(!is_null($VSID)) 			$Vers = $VSID;
		elseif(isset($_POST['vs'])) 	$Vers = $_POST['vs'];
		else 							$Vers = \User::getMyself()->VSID;		
		if(!\User::getMyself()->hasVSAccess($Vers)) returnErrorJSON(getString('Errors noPerm'));
		
		$Groups = array();
		foreach(\GroupManager::getGroupsByVers($Vers) AS $cGroup) 
			$Groups[] = array('value' => $cGroup['gid'], 'text' => $cGroup['name']);
		
		if($ECHO) echo json_encode($Groups);
		else return json_encode($Groups);
	}
	
	private function AddUser() {
		$Name = $_POST['name'];
		$Password = $_POST['pass'];
		$Email = $_POST['email'];
		$Active = $_POST['active'];
		$Vers = $_POST['vers'];
		$Role = $_POST['role'];
		$Perms = isset($_POST['perms']) ? $_POST['perms'] : array();
		$Groups = isset($_POST['groups']) ? $_POST['groups'] : array();
		
		if(empty($Name) || empty($Password) || empty($Vers)) returnErrorJSON(getString('Errors WrongFields')); //Leere Felder		
		if(!\User::getMyself()->hasVSAccess($Vers)) returnErrorJSON(getString('Errors noPerm')); //Keine Rechte für Versammlung
		
		\UserManager::addUser($Name, $Password, $Email, $Active, $Vers, $Role, $Groups, $Perms);
		echo json_encode(array());
	}
	
	private function displayEdit() {
		$UID = getURL(2);
		if(empty($UID)) returnErrorJSON(getString('Errors invalidUser'));

		$User = new \User($UID);		
		if(!$User->Valid || !\User::getMyself()->hasVSAccess($User->VSID)) returnErrorJSON(getString('Errors invalidUser'));
		
		if(!\User::getMyself()->hasVSAccess($User->VSID)) returnErrorJSON(getString('Errors invalidUser'));
		
		$Groups = array();
		foreach(\GroupManager::getGroupsByUser($User->UID) AS $cGroup)
			$Groups[] = $cGroup['gid'];
		
		$HTML = new \HTMLTemplate('EditUser.html', $this->ClassPath);
		$HTML->replace(array(
			'NAME' => $User->Name,
			'UID' => $User->UID,
			'EMAIL' => $User->Email,
			'VERS' => $this->getVersSelect($User->VSID),
			'ROLE' => $this->getRoles($User->VSID, FALSE),
			'ROLEPRE' => $User->RID,
			'GROUPS' => $this->getGroups($User->VSID, FALSE),
			'GROUPSPRE' => json_encode($Groups),
			'PERMS' => $this->getPermOptions(),
			'ACTIVE' => $User->Active ? "1" : "0"
		));
		$HTML->replaceLangTag();
		$HTML->display();
	}
	
	private function EditUser() {
		$User = new \User($_POST['uid']);
		$Name = $_POST['name'];
		$Password = $_POST['pass'];
		$Email = $_POST['email'];
		$Active = $_POST['active'];
		$Vers = $_POST['vers'];
		$Role = $_POST['role'];
		$Perms = isset($_POST['perms']) ? $_POST['perms'] : array();
		$Groups = isset($_POST['groups']) ? $_POST['groups'] : array();
		
		if(!$User->Valid) returnErrorJSON(getString('Errors invalidUser'));
		
		if(empty($Name) || empty($Vers)) returnErrorJSON(getString('Errors WrongFields')); //Leere Felder
		if(!\User::getMyself()->hasVSAccess($Vers) || !\User::getMyself()->hasVSAccess($User->VSID)) returnErrorJSON(getString('errors noPerm')); //Keine Rechte für Versammlung
		
		$NewData = array();
		if($User->Name != $Name) 		$NewData['name'] = $Name;
		if(!empty($Password)) 			$NewData['password'] = password_hash($Password, PASSWORD_DEFAULT);
		if($User->Email != $Email) 		$NewData['email'] = $Email;
		if($User->Active != $Active) 	$NewData['active'] = $Active;
		if($User->VSID != $Vers) 		$NewData['vsid'] = $Vers;
		if($User->RID != $Role) 		$NewData['role'] = $Role;
										$NewData['perms'] = json_encode($Perms);
										
		\GroupManager::unsetUser($User->UID); //Um ihn aus allen Gruppen der alten Versammlung zu entfernen
		\UserManager::editUser($User->UID, $NewData);
		\GroupManager::addUser($User->UID, $Groups);
		echo json_encode(array());
	}
	
	private function DelUser() {
		$User = new \User($_POST['uid']);
		if(!$User->Valid) returnErrorJSON(getString('Errors invalidUser'));
		if(!\User::getMyself()->hasVSAccess($User->VSID)) returnErrorJSON(getString('Errors noPerm')); //Keine Rechte für Versammlung
		
		\GroupManager::unsetUser($User->UID); //Um ihn aus allen Gruppen der alten Versammlung zu entfernen
		\UserManager::delUser($User->UID);
		
		echo json_encode(array());
	}
	
	function ContentRequest() {
		switch(getURL(1)) {
			case 'deluser':
				$this->DelUser();
				break;
			case 'edituser':
				$this->EditUser();
				break;
			case 'adduser':
				$this->AddUser();
				break;
			case 'edit':
				$this->displayEdit();
				break;
			case 'getgroups':
				$this->getGroups();
				break;
			case 'getroles':
				$this->getRoles();
				break;
			case 'updateuserlist':
				echo $this->getUserCards();
				break;
			case 'add':
				$html = new \HTMLTemplate('AddUser.html', $this->ClassPath);
				$html->replace(array('VERS' => $this->getVersSelect()));
				$html->replace(array('PERM' => $this->getPermOptions()));
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