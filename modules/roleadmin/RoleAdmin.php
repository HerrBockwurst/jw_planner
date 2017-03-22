<?php
class RoleAdmin extends Module {
	
	private function __construct() {
		$this->Permission = "admin.rolemanager";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'roleadmin';
		$this->MenuItem = new MenuItem("menu RoleAdmin", 60, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new RoleAdmin();
		return $Instance;
	}
	
	private function Handler_loadRoles() {
		$MySQL = MySQL::getInstance();
		
		$RoleList = '';
		$VSListe = '';
		$PermList = '';

		//Versliste
		$SelectedVers = isset($_POST['vsid']) ? $_POST['vsid'] : User::getInstance()->VSID ;
		if(!array_key_exists($SelectedVers, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));
		
		foreach(User::getInstance()->getAccessableVers() AS $VSID => $VSName) {			
			$Selected = $VSID == $SelectedVers ? 'selected' : '';
			$VSListe .= '<option value="'.$VSID.'" '.$Selected.'>'.$VSName.'</option>';
		}
			

		//Rollenliste + Permissions
		//Where für Versammlungen
		if(isset($_POST['vsid']))
			$MySQL->where('vsid', $_POST['vsid']);
		else
			$MySQL->where('vsid', User::getInstance()->VSID);
		
		//Where für RID
		if(isset($_POST['rid']))
			$MySQL->where('rid', $_POST['rid']);
		$MySQL->select('roles', NULL, 500); //TODO Maximal 500 Sätze, erweiterund durch Offset
		
		if($MySQL->countResult() == 0) $RoleList = getString('roleadmin noRolesAssigned');
		
		foreach($MySQL->fetchAll() AS $cRole) {
			$RoleList .= '<div class="RoleAdmin_RoleEntry clickable" data-rid="'.$cRole['rid'].'">'.$cRole['name'].'<span class="RoleAdmin_RoleDelete"></span></div>';			
			
			$SelectedPerms = json_decode($cRole['entry']);
			
			foreach(User::getInstance()->getClearedPerms() AS $cPerm) {
				//Nur Permissions auslesen für aktuelle Rolle
				if(!isset($_POST['rid']) || 
					( isset($_POST['rid']) && $_POST['rid'] != $cRole['rid'] ) ) continue;
				
				if($PermList == '') 
					$PermList .= '<div id="RoleAdmin_PermHeadline" data-rid="'.$cRole['rid'].'">'.$cRole['name'].'</div>';
				$Checked = in_array($cPerm, $SelectedPerms) ? 'checked' : '';
				$PermList .= '<label><input type="checkbox" value="'.$cPerm.'" '.$Checked.'>'.getString('permissions '.$cPerm).'</label>';
			}
		}
		$RoleList .= '<button id="RoleAdmin_bNewRole" style="display: block; margin: 5px auto;">'.getString('roleadmin NewRole').'</button>';
		$PermList .= '<br class="floatbreak" /><button id="RoleAdmin_bSaveRole">'.getString('roleadmin SaveRole').'</button>';
			
		
		echo json_encode(array('verslist' => $VSListe, 'rolelist' => $RoleList, 'permlist' => $PermList));
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
		
		$FilteredPerms = RoleManager::getFilteredPerms($RID, array_merge(User::getInstance()->getClearedPerms(), $Perms));
		
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
			case 'loadRoles':
				$this->Handler_loadRoles();
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
				printHtml('NewRole.html', $this->ClassPath);
				break;
		}
	}
	
	public function ActionSite() {
		
	}
	
}