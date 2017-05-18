<?php
namespace App;

class CalendarManagement extends \AModule {
	function __construct() {
		$this->PageID = 'calendarmanagement';
		$this->ClassPath = 'app/calendarmanagement';
		$this->MenuItem = new \MenuItem($this->PageID, getString('Menu CalendarManagement'), MENU_CALENDAR, 50);
		$this->CSSFile = 'style.css';
		$this->Scope = SCOPE_DESKTOP_APP;
		$this->Permission = 'admin.calendar';
	}
	
	private function getVersSelect($PreSelected = NULL) {
		$String = "";
		foreach(\User::getMyself()->getAccessableVers() AS $VSID => $VSName) {
			$Selected = ($VSID == \User::getMyself()->VSID && is_null($PreSelected)) || $VSID == $PreSelected ? 'selected' : '';
			$String .= "<option value=\"{$VSID}\" {$Selected}>{$VSName}</option>";
		}		
		return $String;
	}
	
	private function getCalendarSelect($VSID = NULL) {
		$VSID = is_null($VSID) ? \User::getMyself()->VSID : $VSID;
		if(!\User::getMyself()->hasVSAccess($VSID)) returnErrorJSON(getString('Errors noPerm'));
		$String = "";
		
		foreach(\CalendarManager::getCalendars($VSID) AS $cCal) 
			$String .= "<option value=\"{$cCal['cid']}\">{$cCal['name']}</option>";
		
		if(empty($String)) $String = "<option value=\"0\">[(Admin noCalendarAssigned)]</option>";
		
		return $String;
	}
	
	private function getCalendar() {
		$VSID = $_POST['vsid'];
		if(!\User::getMyself()->hasVSAccess($VSID)) returnErrorJSON(getString('Errors noPerm'));
		$Data = array();
		
		foreach(\CalendarManager::getCalendars($VSID) AS $cCal)
			$Data[] = array(
					'value' => $cCal['cid'],
					'text' => $cCal['name']
			);
		
		if(empty($Data)) $Data[] = array(
					'value' => "0",
					'text' => getString('Admin noCalendarAssigned')
		);
		
		echo json_encode(array('calendar' => $Data));
	}
	
	private function getCalendarData() {
		$CID = $_POST['cid'];
		$Calendar = \CalendarManager::getCalendar($CID);
		
		$Pattern = array();
		
		
	}
	
	public function ContentRequest() {
		switch(getURL(1)) {
			case 'getcalendardata':
				$this->getCalendarData();
				break;
			case 'getcalendar':
				$this->getCalendar();
				break;
			default:
				$HTML = new \HTMLTemplate('CalendarOverview.html', $this->ClassPath);
				$HTML->replace(array(
						'VERS' => $this->getVersSelect(),
						'CALENDAR' => $this->getCalendarSelect()
				));				
				$HTML->replaceLangTag();
				$HTML->display();
				break;
		}
	}
}