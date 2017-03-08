<?php
class UserEdit_EditUser {
	
	static function updateUser($Data) {
		
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
	
	static function loadUserData($UID) {		
		$Foreigner = new Foreigner($UID);
		if(!$Foreigner->Valid) returnErrorJSON(getString('errors formSubmit'));
		
		$Roles = '<option value="0">{useredit noRole}</option>';
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
			$Perms .= '<label style="margin: 0px;"><input type="checkbox" value="'.$cPerm.'" '.$checked.'> {permissions '.$cPerm.'}</label>';
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
	
	static function get() {		
		
		$String = '
		<div id="useredit_editusercontent">
			<div style="font-weight: bold;">{useredit edit}</div>
			<form id="fEditUser" class="smallspacer">				
				<div style="width: 400px; float: left; margin-right: 20px;">
					<label>{common username} <input type="text" id="iEditUsername" disabled/></label>
					<label>{common name} <input type="text" id="iEditName" /></label>
					<label>{common password} <input type="text" id="iEditPassword" /></label>
				</div>
				<div style="width: 400px; float: left;">
					<label>{useredit role} 
						<select id="iEditRole">
						</select>
				
					</label>
					<label>{common versammlung}
						<select id="iEditVers">
						</select>	
					</label>
					<label>{useredit active} <input type="checkbox" id="iEditActive" style="float: right;"/></label>
				</div>
				<br class="floatbreak" />
				<div style="width: 400px; float: left; margin-right: 20px;">
					<fieldset>
						<legend>{useredit groups}</legend>
						<div id="dEditGroups">
						</div>						
					</fieldset>
				</div>
				<div style="width: 400px; float: left; margin-right: 20px;">
					<fieldset>
						<legend>{useredit specialPerms}</legend>
						<div id="dEditPerms">
						</div>
					</fieldset>
				</div>
				<br class="floatbreak" />
				<button>{useredit updateUser}</button>
				<button class="redbutton">{common back}</button>
			</form>
		</div>';
		
		return $String;
	}
	
	static function getScript() {
		$String = "
			<script>
				function UpdateUser() {
					var groups = {};
				
					$('#dEditGroups').children('label').each(function() {
						var input = $(this).children('input');
						groups[input.val()] = input.is(':checked') ? true : false;
					});
				
					var perms = {};
				
					$('#dEditPerms').children('label').each(function() {
						var input = $(this).children('input');
						perms[input.val()] = input.is(':checked') ? true : false;
					});
				
					var active = $('#iEditActive').is(':checked') ? 1 : 0;
				
					var data = {
						uid : $('#iEditUsername').val(),
						name : $('#iEditName').val(),
						password : $('#iEditPassword').val(),
						role : $('#iEditRole').val(),
						vers : $('#iEditVers').val(),
						active : active,
						groups : groups,
						perms : perms
					}
				
					$.post('".PROTO.HOME."/datahandler/useredit/updateUser', data, function(data) {
						
						testError(data, true);
					});
				}
		
				function EditGoBack() {
					$('#Content').stop().animate({scrollTop: 0}, 200);
					$('#useredit_searchcontent').animate({left: \"0%\"}, 1000);
					$('#useredit_editusercontent').animate({left: \"-100%\"}, 1000);
				}
		
				$('#fEditUser').submit(function (e) {
					e.preventDefault();
				});
		
				$('#fEditUser').children('button').click(function() {
					if($(this).hasClass('redbutton')) EditGoBack();
					else UpdateUser();
				});
			</script>
		";
		
		return $String;
	}
}
?>

