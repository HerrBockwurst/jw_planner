<?php
class Posts {
	
	private $Posts = array();
	
	public function __construct($CID, $Start, $End) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('cid', $CID);
		$MySQL->where('start', $Start, '>=');
		$MySQL->where('end', $End, '<=');
		$MySQL->select('posts');
		
		foreach($MySQL->fetchAll() AS $cPost) 
			$this->Posts[] = new Post($cPost['pid'], $cPost['start'], $cPost['end'], $cPost['count'], $cPost['entrys'], $cPost['req']);
		
	}
	
	public function getPostsByDay($Timestamp) {
		$RetVal = array();
		$End = $Timestamp + (24*60*60) - 1;
		
		foreach($this->Posts AS $cPost) 
			if($cPost->Start >= $Timestamp && $cPost->Start <= $End) $RetVal[] = $cPost; //Gibt Post Objekt zurück
		
		return $RetVal;
	}
	
	public function getPosts() {
		return $this->Posts;
	}
}

class Post {
	public $PID, $Start, $End, $Count, $Entrys, $Requests;
	
	public function __construct($PID, $Start, $End, $Count, $Entrys, $Requests) {
		$this->PID = $PID;
		$this->Start = $Start;
		$this->End = $End;
		$this->Count = $Count;
		$this->Entrys = json_decode($Entrys);
		$this->Requests = json_decode($Requests);
	}
}