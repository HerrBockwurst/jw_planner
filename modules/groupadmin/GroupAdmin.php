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
			$GroupList .= '<div class="GroupAdmin_GroupEntry clickable" data-gid="'.$cGroup['gid'].'">'.$cGroup['name'].'<span class="GroupAdmin_GroupDelete"></span></div>';			
			
			$SelectedUsers = json_decode($cGroup['members']);
			
			foreach($User AS $cUser) {
				if($UserList == '')
					$UserList .= '<div id="GroupAdmin_UserHeadline" data-gid="'.$cGroup['gid'].'">'.$cGroup['name'].'</div>';
									
				$Checked = in_array($cUser['uid'], $SelectedUsers) ? 'checked' : '';
				$UserList .= '<label><input type="checkbox" value="'.$cUser['uid'].'" '.$Checked.'>'.$cUser['name'].'</label>';
			}			
		}
		$GroupList .= '<button id="GroupAdmin_bNewGroup" style="display: block; margin: 5px auto;">'.getString('groupadmin NewGroup').'</button>';
		$UserList .= '<br class="floatbreak" /><button id="GroupAdmin_bSaveGroup">'.getString('groupadmin SaveGroup').'</button>';
			
		
		echo json_encode(array('verslist' => $VSListe, 'grouplist' => $GroupList, 'userlist' => $UserList));
	}
	
	private function Handler_delGroup() {
		if(!isset($_POST['gid'])) returnErrorJSON(getString('errors formSubmit'));
		$GID = $_POST['gid'];
		
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('gid', $GID);
		$MySQL->select('groups', NULL, 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit'));
		
		$Group = $MySQL->fetchRow();
		
		if(!array_key_exists($Group->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors formSubmit')); //Keine Rechte
		
		//Gruppe löschen
		$MySQL->where('gid', $GID);
		if(!$MySQL->delete('groups')) returnErrorJSON(getString('errors sql'));
		
		//Gruppe aus Kalender löschen
		
		$MySQL->where('vsid', $Group->vsid);
		$MySQL->select('calendar', array('cid', 'blacklist', 'whitelist'));
		
		foreach($MySQL->fetchAll() AS $cCalendar) {
			$Blacklist = json_decode($cCalendar['blacklist']);
			$Whitelist = json_decode($cCalendar['whitelist']);
			
			if(in_array($GID, $Blacklist)) {
				unset($Blacklist[array_search($GID, $Blacklist)]);
				$Blacklist = array_values($Blacklist);
				$MySQL->where('cid', $cCalendar['cid']);
				if(!$MySQL->update('calendar', array('blacklist' => json_encode($Blacklist)))) returnErrorJSON(getString('errors sql'));
			}
			
			if(in_array($GID, $Whitelist)) {
				unset($Whitelist[array_search($GID, $Whitelist)]);
				$Whitelist = array_values($Whitelist);
				$MySQL->where('cid', $cCalendar['cid']);
				if(!$MySQL->update('calendar', array('whitelist' => json_encode($Whitelist)))) returnErrorJSON(getString('errors sql'));
			}
				
		}
		
		echo json_encode(array());
	}
	
	private function Handler_updateGroup() {
		if(!isset($_POST['gid'])) returnErrorJSON(getString('errors formSubmit'));
		$GID = $_POST['gid'];		
		$Users = isset($_POST['users']) ? $_POST['users'] : array();
		
		$MySQL = MySQL::getInstance();
		$MySQL->where('gid', $GID);
		$MySQL->select('groups', array('vsid'), 1);
		if($MySQL->countResult() == 0 || !array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) 
			returnErrorJSON(getString('errors noPerm')); //Gruppe nicht in Versammlung
		
		$MySQL->where('gid', $GID);
		if(!$MySQL->update('groups', array('members' => json_encode($Users)))) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}
	
	private function Handler_addGroup() {
		$VSID = isset($_POST['vsid']) ? $_POST['vsid'] : '';
		$GroupName = isset($_POST['groupname']) ? $_POST['groupname'] : '';
		
		if(empty($GroupName) || empty($VSID)) returnErrorJSON(getString('errors formSubmit'));
		if(!array_key_exists($VSID, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));
		
		$MySQL = MySQL::getInstance();
		
		if(!$MySQL->insert('groups', array('vsid' => $VSID, 'name' => $GroupName, 'members' => json_encode(array())))) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}
	
	
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'loadGroups':
				$this->Handler_loadGroups();
				break;
			case 'delGroup':
				$this->Handler_delGroup();
				break;
			case 'updateGroup':
				$this->Handler_updateGroup();
				break;
			case 'addGroup':
				$this->Handler_addGroup();
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