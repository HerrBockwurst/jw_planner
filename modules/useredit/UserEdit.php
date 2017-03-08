<?php
class UserEdit extends Module {
	
	private function __construct() {
		$this->Permission = "";
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

	private function Handler_updateUser($Data) {		
		//Prüfen, ob alle Daten übergeben wurden
		$NeededFields = array('uid', 'name', 'password', 'role', 'vers', 'groups', 'perms', 'active');
		foreach($NeededFields AS $cField)
			if(!array_key_exists($cField, $Data)) 			returnErrorJSON(getString('errors formSubmit'));
	
			if($_POST['active'] != 0 && $Data['active'] != 1)	returnErrorJSON(getString('errors formSubmit'));
			if($Data['role'] != 0 && !RoleManager::getRole($Data['role'])) 			returnErrorJSON(getString('errors formSubmit'));
			if(empty(VersManager::getVers($Data['vers']))) 		returnErrorJSON(getString('errors formSubmit'));
	
	
			$User = new Foreigner($Data['uid']);
			if(!$User->Valid) returnErrorJSON(getString('errors formSubmit'));
	
			$ToUpdate = array();
	
			if(!array_key_exists($User->VSID, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //keine Rechte für alte VS
			if(!array_key_exists($Data['vers'], User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //keine Rechte für neue VS
	
			if(!empty($Data['password'])) $ToUpdate['password'] 			= hash('sha512', getSaltedPassword($Data['password'])); //Wenn Passwort übergeben, dann Update
			if($User->Clearname != $Data['name'])	$ToUpdate['name'] 		= $Data['name'];
			if($User->RoleID != $Data['role']) 		$ToUpdate['role'] 		= $Data['role'];
			if($User->Active != $Data['active']) 	$ToUpdate['active'] 	= $Data['active'];
			if($User->VSID != $Data['vers']) 		$ToUpdate['vsid'] 		= $Data['vers'];
	
			if(!$User->updateMe($ToUpdate)) returnErrorJSON(getString('errors sql'));
	}
	
	private function Handler_SearchUser() {
		$User = isset($_POST['name']) ? $_POST['name'] : "";
		$VS = isset($_POST['vs']) ? $_POST['vs'] : "";
		
		$mysql = MySQL::getInstance();
		
		if($User != "") {
			$mysql->where('name', "%".$User."%", "LIKE");
			$mysql->where('uid',  "%".$User."%", "LIKE", "OR");
		}
		
		if($VS != "") $mysql->where('versammlungen.name', "%".$VS."%", "LIKE");
		
		$mysql->join('users', 'vsid', 'versammlungen', 'vsid');
		$mysql->join('users', 'role', 'roles', 'rid', 'LEFT');
		$mysql->select('users', array('*', 'vs_name' => 'versammlungen.name', 'role_name' => 'roles.name'));
		
		
		echo "<table id=\"UserEdit_UserList\">";
		echo "<tr class=\"shader\">
				<td>".getString('common username')."</td>
				<td>".getString('common name')."</td>
				<td>".getString('useredit active')."?</td>
				<td>".getString('useredit role')."</td>
				<td>".getString('common versammlung')."</td>
			</tr>";
		
		$c = 0;
		foreach($mysql->fetchAll() AS $cUser) {
			$Shader = $c%2 != 0 ? 'class="shader"' : '';
			$Name = $cUser['name'];
			$UID = $cUser['uid'];
			$Aktiv = $cUser['active'] == 1 ? getString('common yes') : getString('common no');
			$Versammlung = $cUser['vs_name'];
			$Rolle = $cUser['role_name'] == '' ? getString('useredit noRole') : $cUser['role_name'];
				
			echo "<tr data-uid=\"$UID\" $Shader >
			<td>$UID</td>
			<td>$Name</td>
			<td>$Aktiv</td>
			<td>$Rolle</td>
			<td>$Versammlung</td>
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
						if(jdata.active == 1) $('#iEditActive').attr('checked', 'checked');
						else $('#iEditActive').attr('checked', false);
				
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
		foreach(RoleManager::getRoles() AS $Role) {			
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
				$this->Handler_updateUser($_POST);
				break;
			default:
				break;
		}
	}
}