<?php
class PermissionManager {
	public static function togglePerm($User, $Perm) {
		
	}
	
	public static function getPermsByRole($rid) {
		if(empty($rid)) return array();
		$MySQL = MySQL::getInstance();		
		
		$MySQL->where('rid', $rid);
		$MySQL->select('roles', NULL, 1);
		$Perms = $MySQL->fetchRow();
		return json_decode($Perms->entry);
	}
}