<?php
class UserEdit_EditUser {
	static function loadUserData($UID) {
		
	}
	
	static function get() {
		$String = '
		<div id="useredit_editusercontent">
			<form id="fEditUser">
				<div style="width: 400px; float: left; margin-right: 20px;">
					<label>{common username} <input type="text" id="iEditUsername" /></label>
					<label>{common name} <input type="text" id="iEditUsername" /></label>
					<label>{common password} <input type="text" id="iEditUsername" /></label>
				</div>
				<div style="width: 400px; float: left;">
					<label>{useredit role} <input type="text" id="iEditUsername" /></label>
					<label>{common versammlung} <input type="text" id="iEditUsername" /></label>
					<label>{useredit groups} <input type="text" id="iEditUsername" /></label>
				</div>
			</form>
		</div>';
		
		return $String;
	}
}
?>

