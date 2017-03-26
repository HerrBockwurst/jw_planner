<?php
class UserEdit extends Module {
	
	private $ReservedUsernames = array('system', 'all');
	
	private function __construct() {
		$this->Permission = "admin.useredit";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'useredit';
		$this->MenuItem = new MenuItem("menu UserEdit", 50, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new UserEdit();
		return $Instance;
	}

	private function Handler_updateUser() {
		//Prüfen, ob alle Daten übergeben wurden
		$NeededFields = array('uid', 'name', 'password', 'role', 'vers', 'active');
		foreach($NeededFields AS $cField)
			if(!array_key_exists($cField, $_POST)) returnErrorJSON(getString('errors formSubmit'));
		
		//Lade Daten
		$name 		= $_POST['name'];
		$password 	= $_POST['password'];
		$role 		= $_POST['role'];
		$vers 		= $_POST['vers'];
		$active		= intval($_POST['active']);
		$perms 		= isset($_POST['perms']) ? $_POST['perms'] : array();
		$groups		= isset($_POST['groups']) ? $_POST['groups'] : array();
		$email		= trim($_POST['email']);
		$username 	= $_POST['uid'];
	
		$MySQL = MySQL::getInstance();
		$User = new Foreigner($username);
			
		//Prüfe Berechtigung
		if(	!array_key_exists($vers, User::getInstance()->getAccessableVers()) ||
			!array_key_exists($User->VSID, User::getInstance()->getAccessableVers())	)
													returnErrorJSON(getString('errors noPerm')); //keine Rechte für Versammlung
		if(empty($name)) 							returnErrorJSON(getString('errors formSubmit')); //Leere Felder, Falsch ausgefüllte Felder
		if($active != 1 && $active != 0) 			returnErrorJSON(getString('errors formSubmit')); //Aktiv falsch
		if($email != "" && !validateEmail($email))  returnErrorJSON(getString('errors formSubmit')); //Email Falsch
	
		//Prüfe Rolle
		if($role != 0) {
			$MySQL->where('rid', $role);
			$MySQL->select('roles', NULL, 1);
			if($MySQL->countResult() == 0 || ($MySQL->countResult() != 0 && $MySQL->fetchRow()->vsid != $vers)) returnErrorJSON(getString('errors formSubmit')); //Rolle nicht in VS vorhanden
		}
	
		//Prüfe Gruppen
		$GroupsToAdd = array();
		if(!empty($groups)) {
			$MySQL->where('vsid', $vers);
			$MySQL->select('groups');
				
			$AvailableGroups = array();
			foreach($MySQL->fetchAll() AS $cGroup)
				$AvailableGroups[] = $cGroup['gid'];
					
			foreach($groups AS $cGroup => $Checked) { 
				if($Checked == "true" && !in_array($cGroup, $AvailableGroups)) returnErrorJSON(getString('errors formSubmit')); //Gruppe nicht in Versammlung
				if($Checked == "true") $GroupsToAdd[] = $cGroup;
			}
		}
	
		//Prüfe Rechte für Permissions
		$NewPermArray = array();
		foreach($perms AS $cPerm => $Checked) {
			if($Checked != "true") continue;
			if(!User::getInstance()->hasPerm($cPerm)) returnErrorJSON(getString('errors noPerm')); //keine Rechte für Permission
			$NewPermArray[] = $cPerm;
		}
		//Permission anhängen, die er zu bearbeitende Nutzer hat, aber der bearbeiter nicht
		foreach($User->getFilteredPerms(User::getInstance()->getClearedPerms()) AS $cPerm)
			$NewPermArray[] = $cPerm;
			
		$perms = $NewPermArray;
			
	
		/*
		 * Alle Prüfungen bestanden, Lege nutzer an
		 */			
		$UserData = array(
			'uid' => $username,
			'name' => $name,
			'active' => $active,
			'vsid' => $vers,
			'role' => $role,
			'email' => $email,
			'perms' => json_encode($perms)
		);
		
		if(!empty($password)) $UserData['password'] = password_hash($password, PASSWORD_DEFAULT);

		GroupManager::unsetUser($User->UID); //Zuerst aus allen Gruppen der alten Versammlung entfernen
		
		if(!$User->updateMe($UserData)) returnErrorJSON(getString('errors sql')); //Dann User Updaten
				
		GroupManager::addUser($User->UID, $GroupsToAdd); //Dann in alle Gruppen eintragen
		
		echo json_encode(array());
		
	}
	
	private function Handler_SearchUser() {
		$User = isset($_POST['name']) ? $_POST['name'] : "";
		$VS = isset($_POST['vs']) ? $_POST['vs'] : "";
		
		$mysql = MySQL::getInstance();
		
		if($User != "") {
			$mysql->where('name', "%".$User."%", "LIKE");
			$mysql->where('uid',  "%".$User."%", "LIKE", "OR");
		}
		
		if($VS != "") $mysql->where('name', "%".$VS."%", "LIKE", "AND", "versammlungen");
		
		$mysql->join('users', 'vsid', 'versammlungen', 'vsid');
		$mysql->join('users', 'role', 'roles', 'rid', 'LEFT');
		$mysql->orderBy('vsid', "ASC", 'users');
		$mysql->select('users', array('*', 'vs_name' => 'versammlungen.name', 'role_name' => 'roles.name'), 500);
		
		
		echo "<table id=\"UserEdit_UserList\">";
		echo "<tr class=\"shader\">
				<td>".getString('common username')."</td>
				<td>".getString('common name')."</td>
				<td>".getString('common email')."</td>
				<td>".getString('useredit active')."?</td>
				<td>".getString('useredit role')."</td>
				<td>".getString('common versammlung')."</td>
				<td><span style=\"display:inline-block; min-width: 1000px;\"></span></td>
			</tr>";
		
		$c = 0;
		foreach($mysql->fetchAll() AS $cUser) {
			if(!User::getInstance()->hasVSAccess($cUser['vsid'])) continue;
			$Shader = $c%2 != 0 ? 'class="shader"' : '';
			$Name = $cUser['name'];
			$UID = $cUser['uid'];
			$Email = $cUser['email'];
			$Aktiv = $cUser['active'] == 1 ? getString('common yes') : getString('common no');
			$Versammlung = $cUser['vs_name'];
			$Rolle = $cUser['role_name'] == '' ? getString('useredit noRole') : $cUser['role_name'];
				
			echo "<tr data-uid=\"$UID\" $Shader >
			<td>$UID</td>
			<td>$Name</td>
			<td>$Email</td>
			<td>$Aktiv</td>
			<td>$Rolle</td>
			<td>$Versammlung</td>
			<td></td>
			</tr>";
				
			$c++;
		}
		
		echo "</table>";
		
		echo "
			<script>
				$('#UserEdit_UserList').find('tr').click(function() {
					$.post('".PROTO.HOME."/datahandler/useredit/updateFields', {uid : $(this).attr('data-uid')}, function(data) {
						testRedirect(data);
						if(testError(data, true, true)) return;
						jdata = JSON.parse(data);
		
						$('#iEditRole').html(jdata.fRoles).val(jdata.role);
						$('#iEditVers').html(jdata.fVers).val(jdata.vsid);
						$('#dEditGroups').html(jdata.fGroups);
						$('#dEditPerms').html(jdata.fPerms);
				
						$('#iEditUsername').val(jdata.uid);
						$('#iEditName').val(jdata.name);
						$('#iEditVers').val(jdata.vsid);
						$('#iEditEmail').val(jdata.mail);
						if(jdata.active == 1) 
							if(!($('#iEditActive').is(':checked'))) $('#iEditActive').trigger('click');
						else
							if($('#iEditActive').is(':checked')) $('#iEditActive').trigger('click'); 
						
				
						$('#Content').stop().animate({scrollTop: 0}, 200);
						$('#useredit_searchcontent').animate({left: \"100%\"}, 1000);
						$('#useredit_editusercontent').animate({left: \"0%\"}, 1000);
		
					});
				});
			</script>";		
	}

	private function Handler_loadUserData($UID) {		
		$Foreigner = new Foreigner($UID);
		if(!$Foreigner->Valid) returnErrorJSON(getString('errors formSubmit'));
		
		$Roles = '<option value="0">==useredit noRole==</option>';
		foreach(RoleManager::getRoles($Foreigner->VSID) AS $Role) {			
			$Roles .= '<option value="'.$Role['rid'].'">'.$Role['name'].'</option>';
		}
		
		$Versammlungen = '';
		foreach(User::getInstance()->getAccessableVers() AS $vsid => $name) {
			$Versammlungen .= '<option value="'.$vsid.'">'.$name.'</option>';
		}
		
		$Groups = '';
		$Prechecked = array();
		$MySQL = MySQL::getInstance();
		$MySQL->where('vsid', $Foreigner->VSID);
		$MySQL->where('members', '%"'.$Foreigner->UID.'"%', "LIKE");
		$MySQL->select('groups');

		foreach($MySQL->fetchAll() AS $fetchedGroup) 
			$Prechecked[] = $fetchedGroup['gid'];
		
		foreach(GroupManager::getGroups($Foreigner->VSID) AS $cGroup) {
			$checked = array_search($cGroup['gid'], $Prechecked) !== FALSE ? "checked" : "";
			$Groups .= '<label style="margin: 0px;"><input type="checkbox" value="'.$cGroup['gid'].'" '.$checked.'>'.$cGroup['name'].'</label>';
		}
		
		$Perms = '';
		foreach(User::getInstance()->getClearedPerms() AS $cPerm) {
			$checked = $Foreigner->hasPerm($cPerm) ? "checked" : ""; 
			$Perms .= '<label style="margin: 0px;"><input type="checkbox" value="'.$cPerm.'" '.$checked.'> ==permissions '.$cPerm.'==</label>';
		}
		
		echo json_encode(array(
				'uid' => $Foreigner->UID,
				'name' => $Foreigner->Clearname,
				'mail' => $Foreigner->Mail,
				'vsid' => $Foreigner->VSID,
				'vers' => $Foreigner->Vers,
				'role' => $Foreigner->RoleID,
				'active' => $Foreigner->Active,
				'fRoles' => replaceLangTags($Roles),
				'fGroups' => replaceLangTags($Groups),
				'fVers' => replaceLangTags($Versammlungen),
				'fPerms' => replaceLangTags($Perms)
		));
	}
	
	private function Handler_loadNewUser() {
		$Roles = '<option value="0" selected>==useredit noRole==</option>';

		$Versammlungen = '<option value="novs" selected>==common plsSelect==</option>';
		foreach(User::getInstance()->getAccessableVers() AS $vsid => $name) 
			$Versammlungen .= '<option value="'.$vsid.'">'.$name.'</option>';

		$Perms = '';
		foreach(User::getInstance()->getClearedPerms() AS $cPerm) 
			$Perms .= '<label style="margin: 0px;"><input type="checkbox" value="'.$cPerm.'"> ==permissions '.$cPerm.'==</label>';
		
		echo json_encode(array(
				'fRoles' => replaceLangTags($Roles),
				'fVers' => replaceLangTags($Versammlungen),
				'fPerms' => replaceLangTags($Perms)				
		)); 
		
	}
	
	private function Handler_loadGroups() {
		$VSID = isset($_POST['vsid']) ? $_POST['vsid'] : FALSE;
		if($VSID === FALSE) returnErrorJSON(getString('errors formSubmit'));
		
		if($VSID != 'novs' && !array_key_exists($VSID, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); 

		//Gruppen
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('vsid', $VSID);
		$MySQL->select('groups');
		$Groups = $MySQL->countResult() == 0 ? '<div style="text-align: center; padding: 5px;">'.getString('useredit noGroupsAssigned').'</div>' : '';
		foreach($MySQL->fetchAll() AS $cGroup)
			$Groups .= '<label style="margin: 0px;"><input type="checkbox" value="'.$cGroup['gid'].'">'.$cGroup['name'].'</label>';
		
		//Rollen
		$Roles = '<option value="0" selected>==useredit noRole==</option>';
		foreach(RoleManager::getRoles($VSID) AS $Role)
			$Roles .= '<option value="'.$Role['rid'].'">'.$Role['name'].'</option>';
			
		echo json_encode(array('fGroups' => $Groups, 'fRoles' => replaceLangTags($Roles)));
	}
	
	private function Handler_AddUser() {
		//Prüfen, ob alle Daten übergeben wurden
		$NeededFields = array('name', 'password', 'role', 'vers', 'active');
		foreach($NeededFields AS $cField)
			if(!array_key_exists($cField, $_POST)) returnErrorJSON(getString('errors WrongFields'));
		
		//Lade Daten
		$name 		= $_POST['name'];
		$password 	= $_POST['password'];
		$role 		= $_POST['role'];
		$vers 		= $_POST['vers'];
		$active		= intval($_POST['active']);
		$perms 		= isset($_POST['perms']) ? $_POST['perms'] : array();
		$groups		= isset($_POST['groups']) ? $_POST['groups'] : array();
		$email		= trim($_POST['email']);
		$username 	= parseUsername($name, $this->ReservedUsernames);
		
		$MySQL = MySQL::getInstance();
			
		//Prüfe Berechtigung
		if($vers == 'novs') returnErrorJSON(getString('errors WrongFields')); //Keine Versammlung übergeben
		if(!array_key_exists($vers, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //keine Rechte für Versammlung
		if(empty($password) || empty($name)) returnErrorJSON(getString('errors WrongFields')); //Leere Felder, Falsch ausgefüllte Felder
		if($active != 1 && $active != 0) returnErrorJSON(getString('errors formSubmit')); //Aktiv falsch
		if($email != "" && !validateEmail($email))  returnErrorJSON(getString('errors formSubmit')); //Email Falsch
		
		//Prüfe Rolle
		if($role != 0) {
			$MySQL->where('rid', $role);
			$MySQL->select('roles', NULL, 1);
			if($MySQL->countResult() != 0 && $MySQL->fetchRow()->vsid != $vers) returnErrorJSON(getString('errors formSubmit')); //Rolle nicht in VS vorhanden
		}

		//Prüfe Gruppen
		$OnlyCheckedGroups = array();
		if(!empty($groups)) {
			$MySQL->where('vsid', $vers);
			$MySQL->select('groups');
			
			$AvailableGroups = array();
			foreach($MySQL->fetchAll() AS $cGroup) 
				$AvailableGroups[] = $cGroup['gid'];
			
			
			foreach($groups AS $cGroup => $Checked) {
				if($Checked == "true" && !in_array($cGroup, $AvailableGroups)) returnErrorJSON(getString('errors formSubmit')); //Gruppe nicht in Versammlung
				if($Checked == "true") $OnlyCheckedGroups[] = $cGroup;
			}
		}
		
		//Prüfe Rechte für Permissions
		$NewPermArray = array();
		foreach($perms AS $cPerm => $Checked) {
			if($Checked != "true") continue;
			$NewPermArray[] = $cPerm;
		}
		$perms = $NewPermArray;
		
		foreach($perms AS $cPerm)
			if(!User::getInstance()->hasPerm($cPerm)) returnErrorJSON(getString('errors noPerm')); //keine Rechte für Permission
		
		/*
		 * Alle Prüfungen bestanden, Lege nutzer an
		 */
			
		$UserData = array(
				'uid' => $username,
				'name' => $name,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'active' => $active,
				'vsid' => $vers,
				'role' => $role,
				'email' => $email,
				'perms' => json_encode($perms)
		);
		
		if(!$MySQL->insert('users', $UserData)) returnErrorJSON(getString('errors sql'));
		
		foreach($OnlyCheckedGroups AS $cGroup) {
			$MySQL->where('gid', $cGroup);
			$MySQL->select('groups', array('members'), 1);
			
			$Members = json_decode($MySQL->fetchRow()->members);
			$Members[] = $username;
			$Members = json_encode(array_values($Members));
			
			$MySQL->where('gid', $cGroup);
			if(!$MySQL->update('groups', array('members' => $Members)))  returnErrorJSON(getString('errors sql'));
		}
		echo json_encode(array());
	}
	
	private function Handler_DelUser() {
		$User = new Foreigner($_POST['uid']);
		
		//Prüfe Berechtigung
		if(!$User->Valid) returnErrorJSON(getString('errors invalidUID')); //Keine gültige UID
		if(!array_key_exists($User->VSID, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte für Versammlung
		
		//Nutzer aus Gruppen entfernen
		GroupManager::unsetUser($User->UID);
		
		//Nutzer aus Einträgen entfernen
		CalendarManager::removeUser($User->UID);
		
		//Nachrichten des Nutzers löschen
		//TODO
		
		//Nutzer löschen
		$User->deleteMe();
		
		echo json_encode(array());		
	}
	
	public function ActionSite() {
		
	}
	
	public function ActionLoad() {
		switch(getURL(2)) {
			case 'searchUser':
				$this->Handler_SearchUser();
				break;
			default:
				printHtml('EditUser.html', $this->ClassPath);
				printHtml('NewUser.html', $this->ClassPath);
				printHtml('Overview.html', $this->ClassPath);
				break;
		}
	}
	
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'updateFields':
				if(!isset($_POST['uid'])) return;
				$this->Handler_loadUserData($_POST['uid']);
				break;
			case 'updateUser':
				$this->Handler_updateUser();
				break;
			case 'getNewUser':
				$this->Handler_loadNewUser();
				break;
			case 'getDataByVers':
				$this->Handler_loadGroups();
				break;
			case 'addUser':
				$this->Handler_AddUser();
				break;
			case 'deleteUser':
				$this->Handler_DelUser();
				break;
			default:
				break;
		}
	}
}