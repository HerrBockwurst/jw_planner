<?php
class CalendarAdmin extends Module {
	
	private function __construct() {
		$this->Permission = "admin.calendar";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'calendaradmin';
		$this->MenuItem = new MenuItem("menu CalendarAdmin", 40, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new CalendarAdmin();
			return $Instance;
	}
	
	private function Handler_getHeadlineData() {	
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

	private function Handler_getCalendar() {
		
		$CID = isset($_POST['cid']) ? $_POST['cid'] : '';
		
		if(empty($CID)) returnErrorJSON(getString('errors formSubmit')); //CID Leer
		
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', NULL, 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Kalender existiert nicht
		$Calendar = $MySQL->fetchRow();
		if(!array_key_exists($Calendar->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte f�r Kalender
		
		$Output = replaceLangTags(loadHtml('CalendarHeadline.html', $this->ClassPath));
		
		for($i = 1; $i < 8; $i++) {
			$Output .= '<td>'.$this->getCalendarPattern($i, $CID).'</td>';
		}
		
		$Output .= replaceLangTags(loadHtml('CalendarCloser.html', $this->ClassPath));
		$Output .= replaceLangTags(loadHtml('CalendarPostadder.html', $this->ClassPath));
		
		$ToReplace = array(
			'LISTMODE' => $Calendar->listmode
		);
		
		$Output = replacer($Output, array('LIST' => replaceLangTags(replacer(loadHtml('List.html', $this->ClassPath), $ToReplace))));
		
		echo json_encode(array('html' => removeWhiteSpace($Output)));
	}
	
	private function getCalendarPattern($Day, $CID) {
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('day', $Day);
		$MySQL->where('cid', $CID);
		$MySQL->orderBy('start');
		$MySQL->select('pattern');
		
		$RetVal = '';
		
		foreach($MySQL->fetchAll() AS $cPattern) {
			$RetVal .= '
					<div class="CalendarAdmin_PatternEntry clickable" data-patternid="'.$cPattern['patt_id'].'">
						<div style="float:left">
							<span>'.$this->transformTime($cPattern['start']).'</span>
							<span>'.$this->transformTime($cPattern['end']).'</span>
						</div>
						<div style="float: right;">
							'.$cPattern['count'].'
						</div>
						<br class="floatbreak" />
					</div>';
		}
		
		$RetVal .= '
				<div class="CalendarAdmin_PatternEntry clickable" data-day="'.$Day.'">
					'.getString('calendaradmin addPost').'
				</div>';
		return $RetVal;
	}
	
	private function transformTime($Time) {
		return substr("0".floor($Time/60), -2).":".substr("0".$Time%60, -2);
	}
	
	private function Handler_addCalendar() {
		$CName = isset($_POST['cname']) ? $_POST['cname'] : '';
		$VSID = isset($_POST['vsid']) ? $_POST['vsid'] : '';
		
		if(empty($CName) || empty($VSID)) returnErrorJSON(getString('errors formSubmit')); //Postdaten Leer
		
		if(!array_key_exists($VSID, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte f�r VS
		
		$MySQL = MySQL::getInstance();
		
		if(!$MySQL->insert('calendar', array(
				'vsid' => $VSID,
				'name' => $CName,
				'blacklist' => json_encode(array()),
				'whitelist' => json_encode(array()),
				'listmode' => 'blacklist'
		))) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}
	
	private function Handler_deletePattern() {
		if(!isset($_POST['pattid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$PattId = $_POST['pattid'];
		
		$MySQL = MySQL::getInstance();
		$MySQL->where('patt_id', $PattId);
		$MySQL->join('pattern', 'cid', 'calendar', 'cid');
		$MySQL->select('pattern', array('calendar.vsid'), 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Ung�ltiges Pattern
		
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte f�r VS
		
		$MySQL->where('patt_id', $PattId);
		if(!$MySQL->delete('pattern')) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}

	private function Handler_addPattern() {
		$NeededDatas = array('fH', 'fM', 'tH', 'tM', 'count', 'day', 'cid');
		foreach($NeededDatas AS $NeededData)
			if(!array_key_exists($NeededData, $_POST)) returnErrorJSON(getString('errors formSubmit')); //Nicht alle Daten �bergeben
		
		$CID = $_POST['cid'];
		$Day = $_POST['day'];
		$Count = $_POST['count'];
		$Start = (intval($_POST['fH']) * 60 ) + intval($_POST['fM']);
		$End = (intval($_POST['tH']) * 60 ) + intval($_POST['tM']);
		
		
		//Rechte Pr�fen
		$MySQL = MySQL::getInstance();
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', array('vsid'), 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Falscher CID
		
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte f�r VS
		
		//Werte pr�fen
		if($End > 1425 || $Start > 1410 || $End < 30 || $Start < 15) returnErrorJSON(getString('errors TimeFormat')); //Zeiten au�er Bereich
		if($Start >= $End) returnErrorJSON(getString('errors TimeFormat')); //Zeiten au�er Bereich
		
		//Andere Eintr�ge zu dem Tag holen
		$MySQL->where('day', $Day);
		$MySQL->where('cid', $CID);
		$MySQL->select('pattern');
		
		foreach($MySQL->fetchAll() AS $cPattern) {
			//Pr�fen ob Zeiten schon vorhanden
			if($cPattern['start'] == $Start || $cPattern['end'] == $End) returnErrorJSON(getString('errors TimeBlocked'));	//Zeit vorhanden		
			if($Start >= $cPattern['start'] && $End <= $cPattern['end']) returnErrorJSON(getString('errors TimeBlocked')); //Zeit zwischen Zeit
			if($Start >= $cPattern['start'] && $Start < $cPattern['end']) returnErrorJSON(getString('errors TimeBlocked')); //Start zwischen Zeit
			if($End > $cPattern['start'] && $End <= $cPattern['end']) returnErrorJSON(getString('errors TimeBlocked')); //End zwischen Zeit
		}
		
		//Alles OK, eintragen
		if(!$MySQL->insert('pattern', array(
			'start' => $Start,
			'end' => $End,
			'cid' => $CID,
			'day' => $Day,
			'count' => $Count
		))) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
		
	}
	
	private function Handler_delCalendar() {
		if(!isset($_POST['cid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$CID = $_POST['cid'];
		
		$MySQL = MySQL::getInstance();
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', array('vsid'), 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit'));
		
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm'));
		
		//Alles OK, l�schen
		$MySQL->where('cid', $CID);
		if(!$MySQL->delete('calendar')) returnErrorJSON(getString('errors sql'));
		
		$MySQL->where('cid', $CID);
		if(!$MySQL->delete('posts')) returnErrorJSON(getString('errors sql'));
		
		$MySQL->where('cid', $CID);
		if(!$MySQL->delete('pattern')) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}

	private function Handler_genPosts() {
		$NeededDatas = array('from', 'to', 'cid');
		foreach($NeededDatas AS $NeededData)
			if(!array_key_exists($NeededData, $_POST)) returnErrorJSON(getString('errors formSubmit')); //Nicht alle Daten �bergeben
		
		$From = DateTime::createFromFormat("!j.n.Y", $_POST['from']);
		$To = DateTime::createFromFormat("!j.n.Y", $_POST['to'])->setTime(23,59,59);
		$CID = $_POST['cid'];
		
		if(!$From || !$To) returnErrorJSON(getString('errors TimeFormat')); //Zeit nicht lesbar
		if(($From->diff($To))->format("%r") == "-") returnErrorJSON(getString('errors TimeFormat')); //From > To
		
		$MySQL = MySQL::getInstance();
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', array('vsid'), 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit'));		
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte f�r VS
		
		$MySQL->where('cid', $CID);
		$MySQL->where('start', $From->getTimestamp(), '>=');
		$MySQL->where('end', $To->getTimestamp(), '<=');
		$MySQL->select('posts');
		$Posts = array();
		
		foreach($MySQL->fetchAll() AS $cPost)
			$Posts[$cPost['start']] = $cPost['end'];
		
		$MySQL->where('cid', $CID);
		$MySQL->select('pattern');
			
		$InsertData = array();		
		
		foreach($MySQL->fetchAll() AS $cPattern) {
			$CurrDay = DateTime::createFromFormat('!j.n.Y', $From->format('j.n.Y'));
			while(($CurrDay->diff($To))->format("%r") == "") {
				if(intval($CurrDay->format('N')) == $cPattern['day']) {
					$Start = DateTime::createFromFormat('!j.n.Y', $CurrDay->format('j.n.Y'));
					$End = DateTime::createFromFormat('!j.n.Y', $CurrDay->format('j.n.Y'));
					
					$Start->setTime(floor($cPattern['start']/60), $cPattern['start']%60);					
					$End->setTime(floor($cPattern['end']/60), $cPattern['end']%60);

					if(isset($Posts[$Start->getTimestamp()])) {
						$CurrDay->add(new DateInterval('P1D'));
						continue; //Posts existiert schon
					}
					
					$InsertData[] = array(
						'cid' => $CID,
						'start' => $Start->getTimestamp(),
						'end' => $End->getTimestamp(),
						'count' => $cPattern['count'],
						'entrys' => '[]',
						'req' => '[]'
					);
				}
				$CurrDay->add(new DateInterval('P1D'));
			}						
		}
		if(!$MySQL->insert('posts', $InsertData)) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}
	
	private function Handler_getLists() {
		if(!isset($_POST['cid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$CID = $_POST['cid'];
		
		$MySQL = MySQL::getInstance();
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', array('vsid','blacklist', 'whitelist', 'listmode'), 1);
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //ung�ltige CID
		$Calendar = $MySQL->fetchRow();
		$Blacklist = json_decode($Calendar->blacklist);
		$Whitelist = json_decode($Calendar->whitelist);
		$Groups = GroupManager::getGroups($Calendar->vsid);
		
		$StringBlacklist = "<div>".getString('calendaradmin blacklist')."</div>";
		$StringWhitelist = "<div>".getString('calendaradmin whitelist')."</div>";
		
		foreach($Groups AS $cGroup) {
			$Checked = in_array($cGroup['gid'], $Blacklist) ? "checked" : "";
			$StringBlacklist .= '<label><input type="checkbox" value="'.$cGroup['gid'].'" '.$Checked.'>'.$cGroup['name'].'</label>';
			
			$Checked = in_array($cGroup['gid'], $Whitelist) ? "checked" : "";
			$StringWhitelist .= '<label><input type="checkbox" value="'.$cGroup['gid'].'" '.$Checked.'>'.$cGroup['name'].'</label>';
		}
		
		echo json_encode(array('blacklist' => $StringBlacklist, 'whitelist' => $StringWhitelist));
	}
	
	private function Handler_updateList() {
		if(!isset($_POST['cid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$CID = $_POST['cid'];
		$Blacklist = isset($_POST['bl']) ? $_POST['bl'] : array();
		$Whitelist = isset($_POST['wl']) ? $_POST['wl'] : array();
		
		$Calendar = CalendarManager::getCalendarData($CID);		
		$Groups = GroupManager::getGroups($Calendar->vsid);
		
		User::getInstance()->checkVSAccess($Calendar->vsid);
		
		if(!GroupManager::isValidGroup($Blacklist, $Calendar->vsid) || !GroupManager::isValidGroup($Whitelist, $Calendar->vsid))
			returnErrorJSON(getString('errors formSubmit')); //Gruppen sind nicht in der VS
		
		CalendarManager::updateLists($Blacklist, $Whitelist, $CID);
		
		echo json_encode(array());
		
	}
	
	private function Handler_updateListMode() {
		if(!isset($_POST['cid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$CID = $_POST['cid'];
		CalendarManager::toggleListMode($CID);
		
		echo json_encode(array());		
		
	}
	
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'getHeadline':
				$this->Handler_getHeadlineData();
				break;
			case 'getCalendar':
				$this->Handler_getCalendar();
				break;
			case 'addCalendar': 
				$this->Handler_addCalendar();
				break;
			case 'deletePattern':
				$this->Handler_deletePattern();
				break;
			case 'addPattern':
				$this->Handler_addPattern();
				break;
			case 'delCalendar':
				$this->Handler_delCalendar();
				break;
			case 'genPosts':
				$this->Handler_genPosts();
				break;
			case 'getLists':
				$this->Handler_getLists();
				break;
			case 'updateList':
				$this->Handler_updateList();
				break;
			case 'switchList':
				$this->Handler_updateListMode();
				break;
			default:
				break;
		}
	}
	
	public function ActionLoad() {
		switch(getURL(2)) {
			default:
				printHtml('CalendarPatternAdder.html', $this->ClassPath);
				printHtml('Overview.html', $this->ClassPath);
				printHtml('NewCalendar.html', $this->ClassPath);
				break;
		}
	}
	
	public function  ActionSite() {
		
	}
}