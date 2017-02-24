<?php
class RoleManager {
	public static function getRoles($Vers = NULL) {
		$vs = $Vers == NULL ? User::getInstance()->VSID : $Vers;
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('vsid', $vs);
		$MySQL->select('roles');
		
		return $MySQL->fetchAll();		
	}
}