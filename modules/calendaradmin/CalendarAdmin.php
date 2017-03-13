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
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte für Kalender
		
		$Output = replaceLangTags(loadHtml('CalendarHeadline.html', $this->ClassPath));
		
		for($i = 1; $i < 8; $i++) {
			$Output .= '<td>'.$this->getCalendarPattern($i, $CID).'</td>';
		}
		
		$Output .= replaceLangTags(loadHtml('CalendarCloser.html', $this->ClassPath));
		$Output .= replaceLangTags(loadHtml('CalendarPostadder.html', $this->ClassPath));
		
		echo json_encode(array('html' => removeWhiteSpace($Output)));
	}
	
	private function getCalendarPattern($Day, $CID) {
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('day', $Day);
		$MySQL->where('cid', $CID);
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
		
		if(!array_key_exists($VSID, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte für VS
		
		$MySQL = MySQL::getInstance();
		
		if(!$MySQL->insert('calendar', array(
				'vsid' => $VSID,
				'name' => $CName,
				'blacklist' => json_encode(array()),
				'whitelist' => json_encode(array())
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
		
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit')); //Ungültiges Pattern
		
		if(!array_key_exists($MySQL->fetchRow()->vsid, User::getInstance()->getAccessableVers())) returnErrorJSON(getString('errors noPerm')); //Keine Rechte für VS
		
		$MySQL->where('patt_id', $PattId);
		if(!$MySQL->delete('pattern')) returnErrorJSON(getString('errors sql'));
		
		echo json_encode(array());
	}

	private function Handler_addPattern() {
		
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