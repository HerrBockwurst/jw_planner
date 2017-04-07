<?php
class UserManager {
	public static function getUsersBy($Filter = array(), $Limit = 200) {
		$mysql = MySQL::getInstance();
		
		foreach($Filter AS $Row => $Val) {
			$Exploded = explode(".", $Row);

			if(count($Exploded) == 2) {
				$Table = $Exploded[0];
				$Row = $Exploded[1];
			} elseif(count($Exploded) == 1) {
				$Table = NULL;
				$Row = $Exploded[0];
			} else return array();
			
			if(substr($Val,0,1) == '!')
				$mysql->where($Row, substr($Val,1), '<>', 'AND', $Table);
			elseif(substr($Val,0,1) == '<')
				$mysql->where($Row, substr($Val,1), '<', 'AND', $Table);
			elseif(substr($Val,0,1) == '>')
				$mysql->where($Row, substr($Val,1), '>', 'AND', $Table);
			elseif(substr($Val,0,1) == '%')
				$mysql->where($Row, '%'.substr($Val,1).'%', 'LIKE', 'AND', $Table);
			elseif(!preg_match('/[A-Za-z0-9]/', substr($Val,0,1)))
				$mysql->where($Row, substr($Val,1), '=', 'AND', $Table);
			else
				$mysql->where($Row, $Val, '=', 'AND', $Table);
		}
		$mysql->join('users', 'role', 'roles', 'rid', 'LEFT');
		$mysql->join('users', 'vsid', 'versammlungen', 'vsid', 'LEFT');
		$mysql->orderBy('vsid');
		$mysql->orderBy('uid');
		$mysql->select('users', array('*', 'vs_name' => 'versammlungen.name', 'role_name' => 'roles.name'), $Limit);
		
		return $mysql->fetchAll();
	}
}