<?php
class Calendar extends Module {
	private function __construct() {
		$this->Permission = "calendar.entry";
		$this->ClassPath = 'calendar';
		$this->CSSFiles = 'style.css';
		$this->MenuItem = new MenuItem("menu Calendar", 10, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Calendar();
		return $Instance;
	}
	
	private function Handler_getHeadline() {
		$MySQL = MySQL::getInstance();
		$VersString = '';
		$CalString = '<option value="0">'.getString('common plsSelect').'</option>';
		
		foreach(User::getInstance()->getAccessableVers() AS $VSID => $VSName) {
			$Selected = (empty($_POST['vsid']) && $VSID == User::getInstance()->VSID) || (!empty($_POST['vsid']) && $_POST['vsid'] == $VSID) ? 'selected' : '';
			$VersString .= '<option value="'.$VSID.'" '.$Selected.'>'.$VSName.'</option>';
		}
		
		$SVers = empty($_POST['vsid']) ? User::getInstance()->VSID : $_POST['vsid'];
		
		if(!array_key_exists($SVers, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));
		
		$MySQL->where('vsid', $SVers);
		$MySQL->select('calendar');
		
		foreach($MySQL->fetchAll() AS $cCal)
			$CalString .= '<option value="'.$cCal['cid'].'">'.$cCal['name'].'</option>';
		
		echo json_encode(array('vs' => $VersString, 'cal' => $CalString));
	}

	private function getPostsInTime($CID, $Start, $End) {
		return new Posts($CID, $Start, $End);
	}
	
	private function Handler_getCalendar() {
		
		if(!isset($_POST['cid'])) returnErrorJSON(getString('errors formSubmit'));
		
		//Berechtigung prüfen
		$CID = $_POST['cid'];
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', array('vsid'), 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //ungültige CID
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));
		
		//Rechte Ok, Daten verarbeiten		
		$Date = isset($_POST['date']) ? $_POST['date'] : date('n.Y');
		$Date = explode('.', $Date);
		
		$TimestampFirst = DateTime::createFromFormat('!j.n.Y', '1.'.$Date[0].'.'.$Date[1]);		
		if(!$TimestampFirst) returnErrorJSON(getString('errors formSubmit')); //ungültiges Datum übergeben		
		
		$MaxDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $Date[0], $Date[1]);
		$cDay = 1;
		$cWeekday = 1;
		
		//Posts laden
		$Posts = $this->getPostsInTime($CID, $TimestampFirst->getTimestamp(), ($TimestampFirst->getTimestamp() + ($MaxDaysInMonth * 24 * 60 * 60) - 1));
		
		//Kalender erstellen
		
		$Calendar = loadHtml('CalendarHeader.html', $this->ClassPath);
		
		while($cDay <= $MaxDaysInMonth) {
			$cTimestamp = (DateTime::createFromFormat('!j.n.Y', $cDay.'.'.$Date[0].'.'.$Date[1]))->getTimestamp();
			
			if($cWeekday == 1) $Calendar .= '<tr>';			
			
			if($cWeekday == date('N', $cTimestamp)) {
				$Class = '';
				if(!empty($Posts->getPostsByDay($cTimestamp))) $Class .= 'clickable withPosts';
				if($Posts->RequestInDay($cTimestamp)) $Class .= ' withReq';
				$Calendar .= '<td class="'.$Class.'" data-timestamp="'.$cTimestamp.'">'.$cDay.'</td>';
				$cDay++;
			} else {
				$Calendar .= '<td class="grayed"></td>';
			}
			
			$cWeekday++;
			
			while(($cDay > $MaxDaysInMonth) && $cWeekday != 8) {
				$Calendar .= '<td class="grayed"></td>';
				$cWeekday++;
			}
			
			if($cWeekday == 8) {
				$Calendar .= '</tr>';
				$cWeekday = 1;
			}
		}
		
		$Calendar .= loadHtml('CalendarFooter.html', $this->ClassPath);
		$Switch = replacer(loadHtml('CalendarSwitch.html', $this->ClassPath), //Switch erstellen
				array(
					"NMONTH" => $TimestampFirst->format("n"),
					"NYEAR" => $TimestampFirst->format("Y"),
					"MONTH" => getString("common ".$TimestampFirst->format("F")),
					"YEAR" => $TimestampFirst->format('Y')
				));
		
		
		$Calendar = replacer($Calendar, array( //Switch einfügen
				"SWITCH" => $Switch
		));
		
		echo json_encode(array('html' => replaceLangTags($Calendar)));
		
	}
	
	private function Handler_getPosts() {
		if(!isset($_POST['time']) || !isset($_POST['cid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$Start = $_POST['time'];
		$End = $Start + (24*60*60) - 1;
		
		//Permission prüfen
		$CID = $_POST['cid'];
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', array('vsid'), 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //ungültige CID
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));		
		
		//Alles OK, Auslesen
		$Posts = $this->getPostsInTime($CID, $Start, $End);
		$PostList = '';
		
		foreach($Posts->getPosts() AS $cPost) {
			$Classes = '';
			if(count($cPost->Entrys) > 0 && $cPost->Count > count($cPost->Entrys)) $Classes .= ' notFull';
			if($cPost->Count == count($cPost->Entrys)) $Classes .= ' Full';
			if(count($cPost->Requests) > 0 && $cPost->Count != count($cPost->Entrys)) $Classes .= ' hasRequest';
			
			$PostList .= '<div data-pid="'.$cPost->PID.'" class="Calendar_Post clickable'.$Classes.'">'.date('H:i', $cPost->Start).' - '.date('H:i', $cPost->End).'</div>';
		}
		
		$HTML = loadHtml('PostList.html', $this->ClassPath);
		$HTML = replacer($HTML, array("POSTLIST" => $PostList));
		
		echo json_encode(array('html' => replaceLangTags($HTML)));
		
	}
	
	private function Handler_getPostDetailes() {
		if(!isset($_POST['pid'])) returnErrorJSON(getString('errors formSubmit'));		
		$PID = $_POST['pid'];
		
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('pid', $PID);
		$MySQL->join('posts', 'cid', 'calendar', 'cid');
		$MySQL->select('posts', array('*', 'calendar.vsid'), 1);
		
		$RPost = $MySQL->fetchRow();
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Kein PID gefunden
		if(!array_key_exists($RPost->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte
		
		$Post = new Post($RPost->pid, $RPost->start, $RPost->end, $RPost->count, $RPost->entrys, $RPost->req, $RPost->cid);
		
		$PostContent = '<div id="Calendar_PostEntryList"><span>==calendar Entrys== (==calendar max== (COUNT))</span>';		
		$PostContent .= $Post->formatPostForEntryList();
		$PostContent .= '</div>';
		
		$PostContent .= $this->getPostDetailButtons($Post->Entrys, $Post->Requests, $Post->Count, $Post->PID); 
		$PostContent .= loadHtml("PostDetail.html", $this->ClassPath);
		
		$PostContent = replacer($PostContent, array('COUNT' => $Post->Count));
				
		echo json_encode(array('html' => replaceLangTags($PostContent)));
	}
	
	private function getPostDetailButtons($Entrys, $Requests, $Count, $PID) {
		$MySQL = MySQL::getInstance();
		$MySQL->join('posts', 'cid', 'calendar', 'cid');
		$MySQL->where('pid', $PID);
		$MySQL->where('vsid', User::getInstance()->VSID, '=', 'AND', 'calendar');
		$MySQL->select('posts', array('calendar.vsid'), 1);
		if($MySQL->countResult() != 1) return ''; //Eintragung nur in eigener VS möglich
		
		$UID = User::getInstance()->UID;
		$Full = count($Entrys) == $Count ? TRUE : FALSE;
		
		$String = '<div id="Calendar_PostDetailButtonContainer">'; 
		$String .= '<div>';
		
		if(in_array($UID, $Entrys))
			$String .= '<button id="bCalendar_EntrySwitch" class="redbutton">==calendar doUnentry==</button>';			
		elseif(!$Full)
			$String .= '<button id="bCalendar_EntrySwitch">==calendar doEntry==</button>';
		
		if(!in_array($UID, $Requests) && in_array($UID, $Entrys))
			$String .= '<button id="bCalendar_RequestSwitch">==calendar bRequest==</button>';
		elseif(in_array($UID, $Entrys))
			$String .= '<button id="bCalendar_RequestSwitch" class="redbutton">==calendar bRequest==</button>';		
		
		$String .= '</div>';
		$String .= '<div>';
			
		if(User::getInstance()->hasPerm('admin.calendar'))
			$String .= '<button id="bCalendar_DelPost" class="redbutton">==calendar delPost==</button>';
		
		if(User::getInstance()->hasPerm('calendar.entry.other'))
			$String .= '<button id="bCalendar_EntryOther">==calendar addOther==</button>';
		
		$String .= '</div>';
		$String .= '</div>';
		
		return $String;
	}
	
	private function toggleUser($UID) {
		if(!isset($_POST['pid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$PID = $_POST['pid'];
		$MySQL = MySQL::getInstance();
		
		//Teste Versammlung
		$MySQL->where('pid', $PID);
		$MySQL->join('posts', 'cid', 'calendar', 'cid');
		$MySQL->select('posts', array('*', 'calendar.vsid'), 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Ungültige PID
		
		$Row = $MySQL->fetchRow();
		$P_VSID = $Row->vsid;
		$Post = new Post($Row->pid, $Row->start, $Row->end, $Row->count, $Row->entrys, $Row->req, $Row->cid);
		
		$MySQL->where('uid', $UID);
		$MySQL->select('users', array('vsid'), 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Ungültige UID
		
		if($P_VSID != $MySQL->fetchRow()->vsid) returnErrorJSON(getString('errors noPerm')); //User VSID != Post VSID
		
		if(in_array(User::getInstance()->UID, $Post->Entrys)) {
			//Benutzer austragen
			$NewUsers = $Post->Entrys;
			if(array_search(User::getInstance()->UID, $NewUsers) !== FALSE)
				unset($NewUsers[array_search(User::getInstance()->UID, $NewUsers)]);
			
			//Requests austragen
			$NewReq = $Post->Requests;
			if(array_search(User::getInstance()->UID, $NewReq) !== FALSE)
				unset($NewReq[array_search(User::getInstance()->UID, $NewReq)]);
			
			//SQL
			$MySQL->where('pid', $PID);
			if(!$MySQL->update('posts', array(
					'entrys' => json_encode(array_values($NewUsers)), 
					'req' => json_encode(array_values($NewReq))
			))) returnErrorJSON(getString('errors sql'));
		} else {
			//Benutzer eintragen
			if($Post->Count <= count($Post->Entrys)) returnErrorJSON(getString('errors PostFull'));
			
			$NewUsers = $Post->Entrys;
			$NewUsers[] = User::getInstance()->UID;
			
			$MySQL->where('pid', $PID);
			if(!$MySQL->update('posts', array('entrys' => json_encode(array_values($NewUsers))))) returnErrorJSON(getString('errors sql'));			
		}
		
		echo json_encode(array());
		
	}
	
	private function Handler_toggleMe() {
		$this->toggleUser(User::getInstance()->UID);
	}
	
	private function Handler_toggleReq() {
		$UID = User::getInstance()->UID;
		
		if(!isset($_POST['pid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$PID = $_POST['pid'];
		$MySQL = MySQL::getInstance();
		
		//Teste Versammlung
		$MySQL->where('pid', $PID);
		$MySQL->join('posts', 'cid', 'calendar', 'cid');
		$MySQL->select('posts', array('*', 'calendar.vsid'), 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Ungültige PID
		
		$Row = $MySQL->fetchRow();
		$P_VSID = $Row->vsid;
		$Post = new Post($Row->pid, $Row->start, $Row->end, $Row->count, $Row->entrys, $Row->req, $Row->cid);
		
		$MySQL->where('uid', $UID);
		$MySQL->select('users', array('vsid'), 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Ungültige UID
		
		if($P_VSID != $MySQL->fetchRow()->vsid) returnErrorJSON(getString('errors noPerm')); //User VSID != Post VSID
		
		if(in_array(User::getInstance()->UID, $Post->Requests)) {
			//Requests austragen
			$NewReq = $Post->Requests;
			if(array_search(User::getInstance()->UID, $NewReq) !== FALSE)
				unset($NewReq[array_search(User::getInstance()->UID, $NewReq)]);
					
			//SQL
			$MySQL->where('pid', $PID);
			if(!$MySQL->update('posts', array('req' => json_encode(array_values($NewReq))))) returnErrorJSON(getString('errors sql'));
		} else {
			//Benutzer eintragen
			if(!in_array($UID, $Post->Entrys)) returnErrorJSON(getString('errors formSubmit')); //Will sich wo Requesten, wo er gar nicht drin steht
			
			$NewReq = $Post->Requests;
			$NewReq[] = User::getInstance()->UID;
				
			$MySQL->where('pid', $PID);
			if(!$MySQL->update('posts', array('req' => json_encode(array_values($NewReq))))) returnErrorJSON(getString('errors sql'));
		}
		
		echo json_encode(array());
		
	}
	
	
	public function ActionLoad() {
		switch(getUrl(2)) {
			default:
				printHtml('Overview.html', $this->ClassPath);
				break;				
		}
	}
	
	public function ActionSite() {
	
	}
	
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'getHeadline':
				$this->Handler_getHeadline();
				break;
			case 'getCalendar':
				$this->Handler_getCalendar();
				break;
			case 'getPosts': 
				$this->Handler_getPosts();
				break;
			case 'getPostDetailes':
				$this->Handler_getPostDetailes();
				break;
			case 'toggleMe':
				$this->Handler_toggleMe();
				break;
			case 'toggleReq':
				$this->Handler_toggleReq();
				break;
			default:
				break;
		}
	}
}