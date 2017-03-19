<?php
class GroupAdmin extends Module {
	
	private function __construct() {
		$this->Permission = "admin.groups";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'groupadmin';
		$this->MenuItem = new MenuItem("menu GroupAdmin", 70, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new GroupAdmin();
		return $Instance;
	}
	
	private function Handler_loadGroups() {
		$MySQL = MySQL::getInstance();
		
		$GroupList = '';
		$VSListe = '';
		$UserList = '';
				
		//Versliste
		$SelectedVers = isset($_POST['vsid']) ? $_POST['vsid'] : User::getInstance()->VSID ;
		if(!array_key_exists($SelectedVers, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));
		
		foreach(User::getInstance()->getAccessableVers() AS $VSID => $VSName) {			
			$Selected = $VSID == $SelectedVers ? 'selected' : '';
			$VSListe .= '<option value="'.$VSID.'" '.$Selected.'>'.$VSName.'</option>';
		}
			
		//Gruppenliste + User
		//Where für Versammlungen
		if(isset($_POST['vsid']))
			$MySQL->where('vsid', $_POST['vsid']);
		else
			$MySQL->where('vsid', User::getInstance()->VSID);
		
		//Where für GID
		if(isset($_POST['gid']))
			$MySQL->where('gid', $_POST['gid']);
		$MySQL->select('groups', NULL, 500); //TODO Maximal 500 Sätze, erweiterund durch Offset
			
		if($MySQL->countResult() == 0) $GroupList = getString('groupadmin noGroupsAssigned');
		$Groups = $MySQL->fetchAll();
		
		//Benutzer auslesen
		$MySQL->where('vsid',  $SelectedVers);
		$MySQL->select('users');
		
		$User = $MySQL->fetchAll();
		
		foreach($Groups AS $cGroup) {
			$GroupList .= '<div class="GroupAdmin_GroupEntry clickable" data-rid="'.$cGroup['gid'].'">'.$cGroup['name'].'<span class="GroupAdmin_GroupDelete"></span></div>';			
			
			$SelectedUsers = json_decode($cGroup['members']);
			
			foreach($User AS $cUser) {
				if($UserList == '')
					$UserList .= '<div id="GroupAdmin_UserHeadline" data-gid="'.$cGroup['gid'].'">'.$cGroup['name'].'</div>';
									
				$Checked = in_array($cUser, $SelectedUsers) ? 'checked' : '';
				$UserList .= '<label><input type="checkbox" value="'.$cUser['uid'].'" '.$Checked.'>'.$cUser['name'].'</label>';
			}			
		}
		$GroupList .= '<button id="GroupAdmin_bNewGroup" style="display: block; margin: 5px auto;">'.getString('groupadmin NewGroup').'</button>';
		$UserList .= '<button id="GroupAdmin_bSaveGroup">'.getString('groupadmin SaveGroup').'</button>';
			
		
		echo json_encode(array('verslist' => $VSListe, 'grouplist' => $GroupList, 'userlist' => $UserList));
	}
	
	private function Handler_delRole() {
		if(!isset($_POST['rid'])) returnErrorJSON(getString('errors formSubmit'));
		$RID = $_POST['rid'];
		
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('rid', $RID);
		$MySQL->select('roles', NULL, 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit'));
		
		$Role = $MySQL->fetchRow();
		
		if(!array_key_exists($Role->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors formSubmit')); //Keine Rechte
		
		//Rolle löschen
		$MySQL->where('rid', $RID);
		if(!$MySQL->delete('roles')) returnErrorJSON(getString('errors sql'));
		
		//Rolle bei Benutzern entfernen
		$MySQL->where('role', $RID);
		if(!$MySQL->update('users', array('role' => 0))) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}
	
	private function Handler_updateRole() {
		if(!isset($_POST['rid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$RID = $_POST['rid'];		
		$Perms = isset($_POST['perms']) ? $_POST['perms'] : array();
		
		$MySQL = MySQL::getInstance();
		$MySQL->where('rid', $RID);
		$MySQL->select('roles', array('vsid'), 1);
		if($MySQL->countResult() == 0 || !array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) 
			returnErrorJSON(getString('errors noPerm')); //Rolle nicht in Versammlung
		
		foreach($Perms AS $cPerm) 
			if(array_search($cPerm, User::getInstance()->getClearedPerms()) === FALSE) returnErrorJSON(getString('errors noPerm')); //Test ob er Perms für alle Perms hat
		
		
		$FilteredPerms = RoleManager::getFilteredPerms($RID, User::getInstance()->getClearedPerms());
		
		$MySQL->where('rid', $RID);
		if(!$MySQL->update('roles', array('entry' => json_encode(array_merge($Perms, $FilteredPerms))))) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}
	
	private function Handler_addRole() {
		$VSID = isset($_POST['vsid']) ? $_POST['vsid'] : '';
		$RoleName = isset($_POST['rolename']) ? $_POST['rolename'] : '';
		
		if(empty($RoleName) || empty($VSID)) returnErrorJSON(getString('errors formSubmit'));
		if(!array_key_exists($VSID, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));
		
		$MySQL = MySQL::getInstance();
		
		if(!$MySQL->insert('roles', array('vsid' => $VSID, 'name' => $RoleName, 'entry' => json_encode(array())))) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}
	
	
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'loadGroups':
				$this->Handler_loadGroups();
				break;
			case 'delRole':
				$this->Handler_delRole();
				break;
			case 'updateRole':
				$this->Handler_updateRole();
				break;
			case 'addRole':
				$this->Handler_addRole();
				break;
			default:
				break;
		}
	}
	
	public function ActionLoad() {
		switch(getURL(2)) {
			default:
				printHtml('Overview.html', $this->ClassPath);
				printHtml('NewGroup.html', $this->ClassPath);
				break;
		}
	}
	
	public function ActionSite() {
		
	}
	
}