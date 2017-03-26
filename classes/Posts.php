<?php
class Posts {
	
	private $Posts = array();
	
	public function __construct($CID, $Start, $End) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('cid', $CID);
		$MySQL->where('start', $Start, '>=');
		$MySQL->where('end', $End, '<=');
		$MySQL->orderBy('start');
		$MySQL->select('posts');
		
		foreach($MySQL->fetchAll() AS $cPost) 
			$this->Posts[] = new Post($cPost['pid'], $cPost['start'], $cPost['end'], $cPost['count'], $cPost['entrys'], $cPost['req'], $cPost['cid']);
		
	}
	
	public function getPostsByDay($Timestamp) {
		$RetVal = array();
		$End = $Timestamp + (24*60*60) - 1;
		
		foreach($this->Posts AS $cPost) 
			if($cPost->Start >= $Timestamp && $cPost->Start <= $End) $RetVal[] = $cPost; //Gibt Post Objekt zurück
		
		return $RetVal;
	}
	
	public function RequestInDay($Timestamp): bool {
		$RetVal = FALSE;
		foreach($this->getPostsByDay($Timestamp) AS $cPost) {
			if(count($cPost->Requests) > 0 && count($cPost->Entrys) != $cPost->Count) $RetVal = TRUE;
		}		
		return $RetVal;		
	}
	
	public function getPosts() {
		return $this->Posts;
	}
}

class Post {
	public $PID, $Start, $End, $Count, $Entrys, $Requests, $CID;
	
	public function __construct($PID, $Start, $End, $Count, $Entrys, $Requests, $CID) {
		$this->PID = $PID;
		$this->Start = $Start;
		$this->End = $End;
		$this->Count = $Count;
		$this->Entrys = json_decode($Entrys);
		$this->Requests = json_decode($Requests);
		$this->CID = $CID;
	}
	
	private function getInitial($String) {
		$Name = explode(' ', $String);
		return strtoupper(substr($Name[0], 0, 1).substr($Name[(count($Name) - 1)], 0, 1));
	}
	
	private function TextColor($Hex) {
		$rgb = explode(',',$Hex);
		$r = $rgb[0];
		$g = $rgb[1];
		$b = $rgb[2];
		
		$Pointer = 0;
		$Pointer = $r > 150 ? $Pointer + 1 : $Pointer - 1;
		$Pointer = $g > 150 ? $Pointer + 1 : $Pointer - 1;
		$Pointer = $b > 150 ? $Pointer + 1 : $Pointer - 1;
		
		return $Pointer < 0 ? "rgb(240,240,240)" : "rgb(40,40,40)";
	}
	
	private function getDelButton() {
		$String = '';
		
		if(User::getInstance()->hasPerm('calendar.entry.other')) 
			$String = '<button>==calendar deleteUser==</button>';
		
		return $String;
	}
	
	public function formatPostForEntryList() {
		
		if(empty($this->Entrys)) 
			return getString('calendar noEntrys'); //Keine Enträge vorhanden
		
		
		$UserDB = array();
		$MySQL = MySQL::getInstance();
		
		foreach($this->Entrys AS $Entry) 
			$MySQL->where('uid', $Entry, '=', 'OR');
		
		$MySQL->select('users', array('uid', 'name'));
		
		
		foreach($MySQL->fetchAll() AS $UserData) 
			$UserDB[$UserData['uid']] = $UserData['name'];		
		
		$String = '';
		
		foreach($this->Entrys AS $Entry) {
			$Req = '';
			if(in_array($Entry, $this->Requests)) $Req = 'hasRequest';
			$String .= '
				<div class="Calendar_PostEntry" data-uid="'.$Entry.'">
					<div class="'.$Req.'" style="color: '.$this->TextColor(stringToColorCode($Entry)).'; background-color: rgb('.stringToColorCode($Entry).')">
						'.$this->getInitial($UserDB[$Entry]).'						
					</div>					
					<div>'.$UserDB[$Entry].'</div>					
					'.$this->getDelButton().'
				</div>
				';
		}
		
		return $String;
	}
	
}