<?php
class UserEdit_EditUser {
	static $Foreigner;
	
	static function loadUserData($UID) {		
		self::$Foreigner = new Foreigner($UID);
		if(!self::$Foreigner->Valid) returnErrorJSON(getString('errors noPerm'));
		
		$Roles = '<option value="0">{useredit noRole}</option>';
		foreach(RoleManager::getRoles() AS $Role) {
			$Roles .= '<option value="'.$Role['rid'].'">'.$Role['name'].'</option>';
		}
		
		$Versammlungen = '';
		foreach(User::getInstance()->getAccessableVers() AS $vsid => $name) {
			$Versammlungen .= '<option value="'.$vsid.'">'.$name.'</option>';
		}
		
		$Groups = '';
		foreach(GroupManager::getGroups(self::$Foreigner->VSID) AS $cGroup)
			$Groups .= '<label style="margin: 0px;"><input type="checkbox" value="'.$cGroup['gid'].'">'.$cGroup['name'].'</label>';
		
		$Perms = '';
		foreach(User::getInstance()->getClearedPerms() AS $cPerm)
			$Perms .= '<label style="margin: 0px;"><input type="checkbox" value="'.$cPerm.'"> {permissions '.$cPerm.'}</label>';
		
		echo json_encode(array(
				'uid' => self::$Foreigner->UID,
				'name' => self::$Foreigner->Clearname,
				'mail' => self::$Foreigner->Mail,
				'vsid' => self::$Foreigner->VSID,
				'vers' => self::$Foreigner->Vers,
				'role' => self::$Foreigner->RoleID,
				'active' => self::$Foreigner->Active,
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
		
		$String .= "
			<script>
			</script>
		";
		
		return $String;
	}
}
?>

