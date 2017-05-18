<?php
namespace App;

class GroupManagement extends \AModule {
	function __construct() {
		$this->PageID = 'groupmanagement';
		$this->ClassPath = 'app/groupmanagement';
		$this->MenuItem = new \MenuItem($this->PageID, getString('Menu GroupManagement'), MENU_USER, 30);
		$this->CSSFile = 'style.css';
		$this->Scope = SCOPE_DESKTOP_APP;
		$this->Permission = 'admin.groups';
	}
	
	private function getVersSelect($PreSelected = NULL) {
		$String = "";
		foreach(\User::getMyself()->getAccessableVers() AS $VSID => $VSName) {
			$Selected = ($VSID == \User::getMyself()->VSID && is_null($PreSelected)) || $VSID == $PreSelected ? 'selected' : '';
			$String .= "<option value=\"{$VSID}\" {$Selected}>{$VSName}</option>";
		}
		
		return $String;
	}
	
	private function getGroups($Vers = NULL) {
		$Vers = is_null($Vers) ? \User::getMyself()->VSID : $Vers;
		if(!\User::getMyself()->hasVSAccess($Vers)) returnErrorJSON(getString('Errors noPerm'));
		
		$Groups = \GroupManager::getGroupsByVers($Vers);
		if(empty($Groups)) return '<div class="GroupListEntry" data-gid="0">'.getString('Admin noGroupsAssigned').'</div>';
		
		$String = "";
		
		foreach($Groups AS $cGroup)
			$String .= '<div class="GroupListEntry" data-gid="'.$cGroup['gid'].'">'.$cGroup['name'].'<span class="GroupListEntryEdit"></span><span class="GroupListEntryDelete">X</span></div>';
			
		return $String;
	}
	
	private function getGroupData() {
		$Group = \GroupManager::getGroup($_POST['gid']);
		if(!$Group) returnErrorJSON(getString('Errors invalidInput'));
		if(!\User::getMyself()->hasVSAccess($Group->vsid)) returnErrorJSON(getString('Errors noPerm'));		
		
		$Users = json_decode($Group->members);
		
		$UserString = "";
		
		foreach(\UserManager::getUserBy(array('vsid' => $Group->vsid)) AS $cUser) { //Alle Benutzer auflisten mit Haken oder Ohne
			$Selected = in_array($cUser->UID, $Users) ? 'checked' : '';
			$UserString .= '<label><input type="checkbox" value="'.$cUser->UID.'" '.$Selected.' />'.$cUser->Name.'</label>';
		}
		
		echo json_encode(array(
				'users' => $UserString
		));
	}
	
	private function delGroup() {
		$GID = $_POST['gid'];
		$Group = \GroupManager::getGroup($GID);
		if(!$Group) returnErrorJSON(getString('Errors invalidInput'));
		if(!\User::getMyself()->hasVSAccess($Group->vsid)) returnErrorJSON(getString('Errors noPerm'));
		
		\GroupManager::delGroup($GID);
		echo json_encode(array('groups' => $this->getGroups($Group->vsid)));
	}
	
	private function addGroup() {
		$GroupName = $_POST['name'];
		$VSID = $_POST['vsid'];
		
		if(!\User::getMyself()->hasVSAccess($VSID)) returnErrorJSON(getString('Errors noPerm'));
		
		\GroupManager::addGroup($GroupName, $VSID);
		echo json_encode(array('groups' => $this->getGroups($VSID)));
	}
	
	private function updateGroup() {
		$GID = $_POST['gid'];
		$Users = isset($_POST['users']) ? $_POST['users'] : Array();
		
		$Group = \GroupManager::getGroup($GID);
		if(!$Group) returnErrorJSON(getString('Errors invalidInput'));
		if(!\User::getMyself()->hasVSAccess($Group->vsid)) returnErrorJSON(getString('Errors noPerm'));
		
		\GroupManager::setUsers($GID, $Users);
		
		echo json_encode(array());
	}
	
	private function changeGroupName() {
		$Group = \GroupManager::getGroup($_POST['gid']);
		$NewName = $_POST['name'];
		if(!$Group) returnErrorJSON(getString('Errors invalidInput'));
		if(!\User::getMyself()->hasVSAccess($Group->vsid)) returnErrorJSON(getString('Errors noPerm'));
		
		\GroupManager::changeName($Group->gid, $NewName);
		echo json_encode(array('groups' => $this->getGroups($Group->vsid)));
	}
	
	public function ContentRequest() {
		switch(getURL(1)) {
			case 'editgroup':
				$this->changeGroupName();
				break;
			case 'updategroup':
				$this->updateGroup();
				break;
			case 'addgroup': 
				$this->addGroup();
				break;
			case 'delgroup':
				$this->delGroup();
				break;
			case 'getgroups':
				echo json_encode(array('groups' => $this->getGroups($_POST['vsid'])));				
				break;
			case 'getgroupdata':
				$this->getGroupData();
				break;
			default:
				$HTML = new \HTMLTemplate('GroupOverview.html', $this->ClassPath);
				$HTML->replace(array(
						'VERS' => $this->getVersSelect(),
						'GROUPLIST' => $this->getGroups()
				));
				$HTML->replaceLangTag();
				$HTML->display();
				break;
		}
	}
}