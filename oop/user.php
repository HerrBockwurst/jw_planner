<?php
class UserObject {
	public $uid, $name, $email, $vsid;
	private $perms;
	
	function __construct() {
		$this->auth();
	}
	
	private function insertUserData($result) {
		$this->uid = strval($result->uid);
		$this->name = strval($result->name);
		$this->email = strval($result->email);
		$this->vsid = strval($result->vsid);
		$this->perms = json_decode($result->perms);
	}
	
	function auth() {
		global $mysql;
		/*
		 * Alte Sessions löschen
		 */
		
		$mysql->where('expire', time(), '<');
		$mysql->delete('sessions');
		
		/*
		 * Teste ob Benutzer Session hat
		 */
		
		$mysql->where('sid', session_id());
		$mysql->select('sessions');
		$result = $mysql->fetchRow();
		
		if(!$result &&
			(checkURL(0, 'datahandler') || checkURL(0, 'load')) &&
			(strpos(getURL(2), '.css') === false && strpos(getURL(2), '.js') === false) &&
			!checkURL(1, 'login')) {	
			/*
			 * Bedingungen: 
			 * Keine Session gefunden
			 * URL = Datahandler oder load
			 * Url 2 is nicht .css und nicht .js
			 * Es soll nicht Login geladen werden
			 */
			echo json_encode(array('redirect' => PROTO.HOME));
			exit;
		}			
		elseif(!$result && $_SERVER['HTTP_HOST'] != HOME) {
			//Wenn eine unterseite direkt angefahren wird
			header('Location: '.PROTO.HOME);
			exit;
		}

		if($result) {
			/*
			 * Session updaten
			 */
			
			$mysql->where('uid', strval($result->uid));
			$mysql->select('users', array('uid', 'name', 'email', 'vsid', 'perms'), 1);
			$result = $mysql->fetchRow();
				
			$mysql->where('sid', session_id());
			$mysql->update('sessions', array('expire' => time() + 20*SESSIONTIME));
			
			$this->insertUserData($result);
		}
	}
	
	public function hasPerm($perm = ''): bool {		
		if(in_array($perm, $this->perms)) return true;
		return false;
	}
	
	public function getSubPerm($needle) {
		$retval = array();
		foreach($this->perms AS $perm):
		if(strpos($perm, $needle) !== false):
		$retval[] = $perm;
		endif;
		endforeach;
	
		if(empty($retval)) return false;
		return $retval;
	}
	
	public function getAllPerms($withSpecial = false): array {
		if($withSpecial) return $this->perms;
		
		$retPerms = $this->perms;
		$specialPerms = $this->getSubPerm('.vs.');
		
		foreach($specialPerms AS $specialPerm) unset($retPerms[array_search($specialPerm, $retPerms)]);
		
		return $retPerms;
	}
}

$user = new UserObject();